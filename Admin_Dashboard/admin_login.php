<?php      
include('db_conn.php'); 
session_start(); 

// Enable error reporting for debugging 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if form is submitted
if (isset($_POST['submit'])) {
    // Check if user is already logged in
    if (isset($_SESSION['aId'])) {
        echo "<script>alert('Already logged in');</script>";
        echo "<script>location.replace('index.php');</script>";
        die();
    } else {
        // Fetch form data
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prevent SQL injection
        $username = mysqli_real_escape_string($connection, $username);
        $password = mysqli_real_escape_string($connection, $password);

        // Hash the password
        $h_password = md5($password); // You should consider using password_hash() instead

        // SQL query to check user credentials with hashed password
        $sql = "SELECT * FROM reg WHERE email = '$username' AND password = '$h_password' AND type=2";
        $result = mysqli_query($connection, $sql);

        // Check for query execution errors
        if (!$result) {
            die("Database query failed: " . mysqli_error($connection));
        }

        $count = mysqli_num_rows($result);

        // If credentials are valid
        if ($count == 1) {
            $_SESSION['aId'] = $username; // Set session ID
            echo "<script>alert('Login successful');</script>";
            echo "<script>location.replace('index.php');</script>";
        } else {
            // If login failed
            echo "<script>alert('Login failed. Please check your email and password.');</script>";
           // echo "<script>location.replace('admin_login.php');</script>";
        }
    }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Admin Login - Fin-Plan</title>
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

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <style>
        body {
            background-color: #212529; /* Dark background */
            font-family: 'Open Sans', sans-serif;
            color: #ffffff; /* White text */
        }

        .sidebar {
            background-color: #343a40; /* Darker sidebar */
        }

        .content {
            background-color: #212529; /* Dark content background */
        }

        .login-box {
            background-color: #343a40; /* Darker box for the login form */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        }

        .login-box h1 {
            margin-bottom: 20px;
            color: #ffffff; /* White color for the heading */
        }

        .form-control {
            background-color: #495057; /* Dark input fields */
            border: none;
            color: #ffffff; /* White text */
        }

        .form-control::placeholder {
            color: #adb5bd; /* Light grey placeholder */
        }

        .btn-primary {
            background-color: #007bff; /* Bootstrap primary color */
            border: none; /* No border */
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Darker shade on hover */
        }

        .footer {
            background-color: #343a40; /* Darker footer */
            padding: 20px;
            color: #ffffff; /* White text in footer */
        }
    </style>
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
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-user-edit me-2"></i>Fin-Plan</h3>
                </a>
                <div class="navbar-nav w-100">
                    <a href="forms.html" class="nav-item nav-link active"><i class="fa fa-keyboard me-2"></i>Admin Login</a>
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
                            <img class="rounded-circle me-lg-2" src="" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex">Admin</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                           <!-- <a href="#" class="dropdown-item">My Profile</a>
                            <a href="#" class="dropdown-item">Settings</a>-->

                          <form action="adm_logout.php" method="post">
                              <button type="submit"  name="logout"class="dropdown-item">Logout
                                <i class="fa fa-arrow-right ms-3"></i></button> 
                         </form> 
                            
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->

            <!-- Login Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row vh-100 align-items-center justify-content-center mx-0">
                    <div class="col-md-4 login-box text-center p-4">
                        <h1 class="mb-4">Admin Login</h1>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="Enter your username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            </div>
                            <button type="submit" value="submit" name="submit" class="btn btn-primary rounded-pill py-2 px-4">Login</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Login End -->

            <!-- Footer Start -->
            <div class="footer">
                <div class="container-fluid pt-4 px-4">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start">
                            &copy; <a href="#">Your Site Name</a>, All Right Reserved.
                        </div>
                        <div class="col-12 col-sm-6 text-center text-sm-end">
                            Designed By <a href="https://htmlcodex.com" style="color: #ffffff;">Sreevemathi</a>
                            <br>Portfolio <a href="#" target="_blank" style="color: #ffffff;">Link</a>
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

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>
