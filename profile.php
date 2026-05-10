<?php
session_start();
include('db_conn.php');

/* ===============================
   AUTH CHECK (MATCHES index.php)
   =============================== */
if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 2) {
    header("Location: ../Home/signin.php");
    exit();
}

$reg_id = $_SESSION['user_id'];

/* ===============================
   GET ADVISOR EMAIL FROM reg
   =============================== */
$stmt = $connection->prepare("SELECT name, email FROM reg WHERE id = ?");
$stmt->bind_param("i", $reg_id);
$stmt->execute();
$reg = $stmt->get_result()->fetch_assoc();

$advisor_name  = $reg['name'];
$advisor_email = $reg['email'];

/* ===============================
   FETCH PROFILE (IF EXISTS)
   =============================== */
$advQ = $connection->prepare("SELECT * FROM adv_reg WHERE email = ?");
$advQ->bind_param("s", $advisor_email);
$advQ->execute();
$data = $advQ->get_result()->fetch_assoc();

/* ===============================
   SAVE PROFILE
   =============================== */
if (isset($_POST['save'])) {

    $name        = $_POST['name'];
    $phone       = $_POST['phone'];
    $qual        = $_POST['qual'];
    $yoe         = $_POST['yoe'];
    $description = $_POST['description'];

    /* Image upload */
    if (!empty($_FILES['photo']['name'])) {
        $photo = time() . "_" . $_FILES['photo']['name'];
        $tmp   = $_FILES['photo']['tmp_name'];
        $path  = "uploads/advisors/" . $photo;

        if (!is_dir("uploads/advisors")) {
            mkdir("uploads/advisors", 0777, true);
        }
        move_uploaded_file($tmp, $path);
    } else {
        $photo = $data['photo'] ?? '';
    }

    /* Handle Certificate Upload (NEW) */
    if (!empty($_FILES['certificate']['name'])) {
        $cert = time() . "_cert_" . $_FILES['certificate']['name'];
        move_uploaded_file($_FILES['certificate']['tmp_name'], "uploads/advisors/" . $cert);
    } else {
        $cert = $data['certificate'] ?? '';
    }

    /* INSERT OR UPDATE */
    /* INSERT OR UPDATE */
if ($data) {
        $sql = "UPDATE adv_reg SET 
                name=?, phone=?, qual=?, yoe=?, photo=?, certificate=?, description=?
                WHERE email=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssssssss", $name, $phone, $qual, $yoe, $photo, $cert, $description, $advisor_email);
    } else {
        $sql = "INSERT INTO adv_reg 
                (id, name, email, phone, qual, yoe, photo, certificate, description, stat)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("issssssss", $reg_id, $name, $advisor_email, $phone, $qual, $yoe, $photo, $cert, $description);
    }
    $stmt->execute();
    echo "<script>alert('Profile and Certificate saved successfully'); location='profile.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Advisor Profile | MoneyMorph</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
<div class="container-fluid position-relative d-flex p-0">

    <!-- Sidebar -->
    <div class="sidebar pe-4 pb-3">
        <nav class="navbar bg-secondary navbar-dark">
            <a href="index.php" class="navbar-brand mx-4 mb-3">
                <h3 class="text-primary">MoneyMorph</h3>
            </a>
            <div class="navbar-nav w-100">
                <a class="nav-item nav-link"><h4>ADVISOR</h4></a>
                <a href="index.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>DASHBOARD</a>
                <a href="view_appoin.php" class="nav-item nav-link"><i class="fa fa-calendar-check me-2"></i>APPOINTMENTS</a>
                <a href="add_plan.php" class="nav-item nav-link"><i class="fa fa-plus-circle me-2"></i>ADD PLANS</a>
                <a href="view_plans.php" class="nav-item nav-link"><i class="fa fa-list me-2"></i>MANAGE PLANS</a>
                <a href="profile.php" class="nav-item nav-link active"><i class="fa fa-user me-2"></i>PROFILE</a>
             <a href="../Home/chat_room.php" class="nav-item nav-link"> <i class="fa fa-home me-2"></i>Chat Room</a>
                 

            </div>
        </nav>
    </div>
<!-- Content Start -->
    <div class="content">

        <!-- Navbar -->
        <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
            <a href="#" class="sidebar-toggler flex-shrink-0">
                <i class="fa fa-bars"></i>
            </a>
            <div class="navbar-nav align-items-center ms-auto">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <img class="rounded-circle me-lg-2" src="../User_Dashboard/img/usr.png" style="width:40px;height:40px;">
                        <span class="d-none d-lg-inline-flex">Advisor</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                        <form action="../Home/logout.php" method="post">
                            <button type="submit" name="logout" class="dropdown-item">
                                Logout <i class="fa fa-arrow-right ms-3"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
        <!-- Navbar End -->

        <!-- Profile Form -->
        <div class="container-fluid pt-4 px-4">
            <div class="bg-secondary rounded p-4">
                <h4 class="mb-4 text-primary">Advisor Profile</h4>

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name"
                               value="<?= $data['name'] ?? '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($advisor_email) ?>" readonly>

                    </div>

                    <div class="mb-3">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone"
                               value="<?= $data['phone'] ?? '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Qualification</label>
                        <input type="text" class="form-control" name="qual"
                               value="<?= $data['qual'] ?? '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Years of Experience</label>
                        <input type="number" class="form-control" name="yoe"
                               value="<?= $data['yoe'] ?? '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Profile Photo</label>
                        <input type="file" class="form-control" name="photo">
                        <?php if (!empty($data['photo'])) { ?>
                            <img src="uploads/advisors/<?= $data['photo'] ?>" width="100" class="mt-2 rounded">
                        <?php } ?>
                    </div>

                    <div class="mb-3">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="4" required><?= $data['description'] ?? '' ?></textarea>
                    </div>

                    <div class="mb-3">
    <label class="form-label">Professional Certificate (PDF or Image)</label>
    <input type="file" class="form-control" name="certificate" accept=".pdf,.jpg,.jpeg,.png">
    <?php if (!empty($data['certificate'])) { ?>
        <div class="mt-2">
            <a href="uploads/advisors/<?= $data['certificate'] ?>" target="_blank" class="btn btn-sm btn-outline-info">
                <i class="fa fa-eye"></i> View Current Certificate
            </a>
        </div>
    <?php } ?>
</div>

                    <button type="submit" name="save" class="btn btn-primary">Save Profile</button>

                </form>
            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
