<?php
session_start();
include('db_conn.php');

if (!isset($_SESSION['email']) || $_SESSION['type'] != 1) {
    header("Location: ../Home/signin.php");
    exit;
}

$user_email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_id       = $_POST['account_id'];
    $transaction_type = $_POST['transaction_type'];
    $amount           = floatval($_POST['amount']);
    // Updated variable name to match your table column 'category'
    $category         = $_POST['category'];
    $date             = date('Y-m-d');

    // Fetch account details
    $stmt = $connection->prepare("SELECT balance FROM bank_accounts WHERE account_id = ? AND user_email = ?");
    $stmt->bind_param("is", $account_id, $user_email);
    $stmt->execute();
    $res = $stmt->get_result();
    $acc = $res->fetch_assoc();

    if (!$acc) {
        echo "<script>alert('Invalid account');location.replace('add_trns.php');</script>";
        exit;
    }

    $current_balance = $acc['balance'];

    if ($transaction_type === 'Withdraw' && $amount > $current_balance) {
        echo "<script>alert('Insufficient balance');location.replace('add_trns.php');</script>";
        exit;
    }

    $new_balance = ($transaction_type === 'Deposit') ? $current_balance + $amount : $current_balance - $amount;

    // Update Account Balance
    $update = $connection->prepare("UPDATE bank_accounts SET balance = ? WHERE account_id = ?");
    $update->bind_param("di", $new_balance, $account_id);
    
    if ($update->execute()) {
        // Log Transaction
        $log = $connection->prepare("INSERT INTO transactions (user_email, account_id, transaction_type, amount, category, transaction_date) VALUES (?, ?, ?, ?, ?, ?)");
        $log->bind_param("sisdss", $user_email, $account_id, $transaction_type, $amount, $category, $date);
        $log->execute();
        
        echo "<script>alert('Transaction Successful');location.replace('view_trns.php');</script>";
    }
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

  <!-- Transaction Form Start -->
<form action="add_trns.php" method="POST">

    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-sm-12 col-xl-6">
                <div class="bg-secondary rounded h-100 p-4">
                    <h6 class="mb-4">PERFORM TRANSACTION</h6>
                    <div class="mb-3">
    <label for="account_id" class="form-label">Select Account</label>
    <select class="form-select" id="account_id" name="account_id" required>
        <option selected disabled>Select an account</option>
        <?php
        // FIX: Changed 'id' to 'email' to match your login session
        $user_email = $_SESSION['email'];  
        
        $account_sql = "SELECT account_id, account_title, account_number FROM bank_accounts WHERE user_email = ?";
        $stmt = $connection->prepare($account_sql);
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Populate dropdown with account data
            while ($account = $result->fetch_assoc()) {
                echo "<option value='" . $account['account_id'] . "'>" . 
                     htmlspecialchars($account['account_title']) . " - " . 
                     htmlspecialchars($account['account_number']) . 
                     "</option>";
            }
        } else {
            echo "<option disabled>No accounts found. Please add one first.</option>";
        }
        $stmt->close();
        ?>
    </select>
</div>

                    <!-- Transaction Details -->
                    <div class="mb-3">
                        <label for="transaction_type" class="form-label">Transaction Type</label>
                        <select class="form-select" id="transaction_type" name="transaction_type" required>
                            <option value="Deposit">Deposit</option>
                            <option value="Withdraw">Withdraw</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="" selected disabled>Choose category...</option>
                            <option value="Loan">Loan</option>
                            <option value="Grocery">Grocery</option>
                            <option value="Medical">Medical</option>
                            <option value="Education">Education</option>
                            <option value="Fuel">Fuel</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="submit" class="btn btn-primary" value="Submit Transaction">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


    <!-- Form End -->
</div>
<!-- Content End -->

            </div>
            <!-- Form End -->


           <!-- Footer Start -->
           <div class="container-fluid bg-secondary text-light footer mt-5 pt-4 px-4 wow fadeIn" data-wow-delay="0.1s">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-lg-4 col-md-6">
                        <h4 class="text-uppercase mb-4">Get In Touch</h4>
                        <div class="d-flex align-items-center mb-2">
                            <div class="btn-square bg-dark flex-shrink-0 me-3">
                                <span class="fa fa-map-marker-alt text-primary"></span>
                            </div>
                            <span>123 Street, New York, USA</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="btn-square bg-dark flex-shrink-0 me-3">
                                <span class="fa fa-phone-alt text-primary"></span>
                            </div>
                            <span>+012 345 67890</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="btn-square bg-dark flex-shrink-0 me-3">
                                <span class="fa fa-envelope-open text-primary"></span>
                            </div>
                            <span>info@example.com</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h4 class="text-uppercase mb-4">Quick Links</h4>
                        <a class="btn btn-link" href="">About Us</a><br>
                        <a class="btn btn-link" href="">Contact Us</a><br>
                        <a class="btn btn-link" href="">Our Services</a><br>
                        <a class="btn btn-link" href="">Terms & Condition</a><br>
                        <a class="btn btn-link" href="">Support</a>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h4 class="text-uppercase mb-4">Newsletter</h4>
                        <div class="position-relative mb-4">
                            <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
                            <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">nnUp</button>
                        </div>
                        <div class="d-flex pt-1 m-n1">
                            <a class="btn btn-lg-square btn-dark text-primary m-1" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-lg-square btn-dark text-primary m-1" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-lg-square btn-dark text-primary m-1" href=""><i class="fab fa-youtube"></i></a>
                            <a class="btn btn-lg-square btn-dark text-primary m-1" href=""><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="copyright">
                    <div class="row bg-dark">
                        <div class="col-md-6  text-center text-md-start mb-3 mb-md-0">
                            &copy; <a class="border-bottom" href="#">MoneyMorph</a>, All Right Reserved.
                        </div>
                        <div class="col-md-6 text-center text-md-end">
                            <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                            Designed By <a class="border-bottom" href="">MoneyMoroh</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->
        </div>
        <!-- Content End -->


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