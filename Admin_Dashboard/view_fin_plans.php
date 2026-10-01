<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('db_conn.php');

// Admin authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 0) {
    header("Location: ../Home/signin.php");
    exit;
}

// Delete financial plan
if (isset($_POST['delete_plan'])) {
    $plan_id = $_POST['plan_id'];

    $stmt = $connection->prepare("DELETE FROM financial_plans WHERE plan_id = ?");
    $stmt->bind_param("i", $plan_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all financial plans
$sql = "SELECT plan_id, image, description FROM financial_plans";
$result = $connection->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>FINPLAN</title>
    <title>FinPlan Advisor Dashboard</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="img/favicon.ico" rel="icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- Add your existing head content here -->
</head>
<body>
<div class="container-fluid position-relative d-flex p-0">
         <!-- Sidebar Start -->
         <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="index.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary">FIN-PLAN</h3>
                </a>
                <div class="navbar-nav w-100">
                    <a href="index.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>DASHBOARD</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-user-check me-2"></i>ADVISORS</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="fin_approve.php" class="dropdown-item">APPROVE ADVISORS</a>
                            <a href="view_fin.php" class="dropdown-item">VIEW ADVISORS</a>
                        </div>
                    </div>
                    <a href="view_fin_plans.php" class="nav-item nav-link active"><i class="fas fa-comments me-2"></i>Manage plans</a>
                    <a href="view_feedback.php" class="nav-item nav-link"><i class="fas fa-comments me-2"></i>FEEDBACK</a>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
           <!-- Navbar Start sidebar-toggler -->
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
                            <img class="rounded-circle me-lg-2" src="../User_Dashboard/img/usr.png" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex">Admin</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                           <!-- <a href="#" class="dropdown-item">My Profile</a>
                            <a href="#" class="dropdown-item">Settings</a>-->

                          <form action="../Home/logout.php" method="post">
                              <button type="submit"  name="logout"class="dropdown-item">Logout
                                <i class="fa fa-arrow-right ms-3"></i></button> 
                         </form> 
                            
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->



    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-sm-12 col-xl-12">
                <div class="bg-secondary text-center rounded p-4">
                    <h6 class="mb-0">Financial Plans</h6>
                    <div class="table-responsive">
                            <table class="table text-start align-middle table-bordered table-hover mb-0">
                                <thead>
                                    <tr class="text-white">
                                        <th scope="col">Image</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td><img src='../Advisor_Dashboard/" . htmlspecialchars($row['image']) . "' alt='Plan Image' style='width: 100px;'></td>";
                                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                            echo "<td>";
                                            echo "<form method='post' style='display:inline-block;'>";
                                            echo "<input type='hidden' name='plan_id' value='" . $row['plan_id'] . "'>";
                                            echo "<button type='submit' name='delete_plan' class='btn btn-danger btn-sm'>Delete</button>";
                                            echo "</form>";
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3' class='text-center'>No financial plans found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            </div>
                </div>
            </div>
        </div>
    </div>
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>

