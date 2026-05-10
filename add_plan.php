<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('db_conn.php');

/* Advisor login check */
if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 2) {
    header("Location: ../Home/signin.php");
    exit;
}
/* 🔐 Admin approval check from DB */
$user_id = $_SESSION['user_id'];

$stmt = $connection->prepare(
    "SELECT status FROM reg WHERE id = ? AND type = 2"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    session_destroy();
    header("Location: ../Home/signin.php");
    exit;
}

$row = $res->fetch_assoc();

if ($row['status'] !== 'approved') {
    echo "<script>
        alert('Your account is pending admin approval.');
        window.location='../Home/signin.php';
    </script>";
    exit;
}

/* Advisor ID */
$Adid = $user_id;
?>


<?php

include('db_conn.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $advisor_id = $_POST['advisor_id'];
    $description = $_POST['description'];
    
    // Check if an image file was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imagePath = 'uploads/advisors/' . basename($imageName);

        // Move the uploaded file to the desired location
        if (move_uploaded_file($imageTmpName, $imagePath)) {
            // Insert the financial plan details into the database
            $sql = "INSERT INTO financial_plans (advisor_id, image, description) VALUES (?, ?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param('sss', $advisor_id, $imagePath, $description);
            
            if ($stmt->execute()) {
                echo "<script>alert('Financial plan added successfully!');</script>";
                echo "<script>window.location.href='view_plans.php';</script>";
            } else {
                echo "<script>alert('Error: Could not add financial plan.');</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('Error: Could not upload image.');</script>";
        }
    } else {
        echo "<script>alert('Error: Please upload an image file.');</script>";
    }
} else {
    
   
}
$connection->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>MonyMorph
</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet"> 
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">
       
 <!-- Sidebar Start -->
 <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary">MonyMorph
</h3>
                </a>
                <div class="navbar-nav w-100">
                <a href="" class="nav-item nav-link"><h2>ADVISOR</h2></a>
                    <a href="index.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>DASHBOARD</a>
                    <!-- Appointment Section -->
                    <a href="view_appoin.php" class="nav-item nav-link"><i class="fa fa-calendar-check me-2"></i>APPOINTMENTS</a>
                    <a href="add_plan.php" class="nav-item nav-link"><i class="fa fa-calendar-check me-2"></i>ADD PLANS</a>
                    <a href="view_plans.php" class="nav-item nav-link"><i class="fa fa-calendar-check me-2"></i>MANAGE PLANS</a>


                </div>
            </nav>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                <a href="#" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-user-edit"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="../User_Dashboard/img/usr.png" style="width: 40px; height: 40px;">
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
      
            <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-sm-12 col-xl-6 offset-xl-3">
                <div class="bg-secondary text-center rounded p-4">
                    <h6 class="mb-4">Add Financial Plan</h6>
                    
                    <!-- Form to Add Financial Plan -->
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="image" class="form-label">Plan Image</label>
                            <input type="file" class="form-control" id="image" name="image" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        
                        <input type="hidden" name="advisor_id" value="<?php echo htmlspecialchars($Adid); ?>">
                        
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                    <!-- End of Form -->
                </div>
            </div>
        </div>
    </div>
        </div>
        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/chart/chart.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>