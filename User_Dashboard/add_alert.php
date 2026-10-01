<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('db_conn.php'); 

// 1. Security Check - Using 'email' for consistency
if (!isset($_SESSION['email']) || $_SESSION['type'] != 1) {
    header("Location: ../Home/signin.php");
    exit;
}

$current_user_email = $_SESSION['email']; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = htmlspecialchars($_POST['description']);
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];

    // 2. Prepare Statement - Ensure 'user_email' matches the column name in your DB
    $sql = "INSERT INTO upcoming_alerts (user_email, description, amount, due_date, status) VALUES (?, ?, ?, ?, 'pending')";
    $stmt = $connection->prepare($sql);
    
    // "s" = string, "d" = double/decimal. Using "sdss" (email, description, amount, date)
    $stmt->bind_param("ssds", $current_user_email, $description, $amount, $due_date);

    if ($stmt->execute()) {
        echo "<script>alert('Alert added successfully!'); window.location.href = 'add_alert.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
    $connection->close();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>MoneyMorph</title>
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
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


      <!-- Sidebar Start -->
      <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="../Home/index.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"></i>MoneyMoroh</h3>
                </a>

                <div class="d-flex align-items-center ms-4 mb-4">
                    <!-- USER IMAGE DISPLAY SECTION-->
                    <div class="position-relative">
                      <!---->  <img class="rounded-circle" src="img/usr.png" alt="" style="width: 40px; height: 40px;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <!-- USER NAME DISPLAY SECTION-->
                        <h6 class="mb-0">Dashboard</h6>
                        <!--<span>Admin</span>-->  
                    </div>
                </div>
                <div class="navbar-nav w-100">
                 <!--   <a href="../Home/index.html" class="nav-item nav-link active"><i class="fa fa-tachometer-alt me-2"></i>HOME</a>-->
                    <a href="index.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>DASHBOARD</a>
                    <a href="view_appoinment.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Apoinment</a>
                    <a href="add_alert.php" class="nav-item nav-link"><i class="fa fa-table me-2"></i>Add Alert</a>
                    <a href="fin_plan.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>FIN PLANS</a>
                   
                    <a href="../Home/resource.php" class="nav-item nav-link"><i class="fa fa-table me-2"></i>RESOURSES</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="far fa-file-alt me-2"></i>GOALS</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="view_goal.php" class="dropdown-item">MANAGE GOALS</a>
                            <a href="add_goal.php" class="dropdown-item">ADD GOALS</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="far fa-file-alt me-2"></i>ACCOUNTS</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="add_acc.php" class="dropdown-item">ADD ACCOUNT</a>
                            <a href="view_acc.php" class="dropdown-item">MANAGE ACCOUNTS</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="far fa-file-alt me-2"></i>TRANSACATIONS</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="view_trns.php" class="dropdown-item">MANAGE TRANSACTIONS</a>
                            <a href="add_trns.php" class="dropdown-item">ADD TRANSACATIONS</a>
                        </div>
                    </div>
                   
                   
                
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-user-edit"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="img/usr.png" alt="" style="width: 40px; height: 40px;">
                            <!--USER NAME DISPLAY SECTION-->
                            <span class="d-none d-lg-inline-flex">USER</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <a href="alert.php" class="dropdown-item">Alerts</a>
                            <a href="../Home/logout.php" class="dropdown-item">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->

    <div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-12">
            <div class="bg-secondary text-center rounded p-4">
                <h6 class="mb-0">Add Upcoming EMI / Due / Bill</h6>
                <form id="alertForm" action="add_alert.php" method="POST">
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" name="description" required>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Alert</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Form End -->
</div>
<!-- Content End -->

            </div>
            <!-- Form End -->


          
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