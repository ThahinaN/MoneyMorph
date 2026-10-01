<?php
session_start();
include('db_conn.php'); 

if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 0) {
    header("Location: ../Home/signin.php");
    exit;
}

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// 1. Get total users
$sqlUsers = "SELECT COUNT(*) AS c FROM reg WHERE type = 1";
$resUsers = mysqli_query($connection, $sqlUsers);
$totalUsers = mysqli_fetch_assoc($resUsers)['c'];

// 2. Get total advisors
$sqlAdvisors = "SELECT COUNT(*) AS c FROM reg WHERE type = 2";
$resAdvisors = mysqli_query($connection, $sqlAdvisors);
$totalAdvisors = mysqli_fetch_assoc($resAdvisors)['c'];

// 3. Get total financial plans
$sqlPlans = "SELECT COUNT(*) AS c FROM financial_plans";
$resPlans = mysqli_query($connection, $sqlPlans);
$totalPlans = mysqli_fetch_assoc($resPlans)['c'];

// 4. Get total feedback
$sqlFeedback = "SELECT COUNT(*) AS c FROM feedback";
$resFeedback = mysqli_query($connection, $sqlFeedback);
$totalFeedback = mysqli_fetch_assoc($resFeedback)['c'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>MoneyMorph Admin Dashboard</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .welcome-container {
            padding: 40px 0;
            text-align: center;
        }
        .welcome-text {
            font-size: 2.5em;
            font-weight: 700;
            color: #fff;
        }
        .typing-effect {
            color: #eb1616;
            border-right: 3px solid #eb1616;
            white-space: nowrap;
            overflow: hidden;
            display: inline-block;
            animation: typing 3s steps(30, end), blink 0.75s step-end infinite;
        }
        @keyframes typing { from { width: 0; } to { width: 100%; } }
        @keyframes blink { from, to { border-color: transparent; } 50% { border-color: #eb1616; } }

        .stat-card {
            background: #191c24;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #2c2f3b;
        }
        .stat-icon {
            width: 60px; height: 60px;
            background: rgba(235, 22, 22, 0.1);
            color: #eb1616;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 25px;
        }
    </style>
</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="index.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary">MoneyMorph</h3>
                </a>
                <div class="navbar-nav w-100">
                    <a href="../Home/index.php" class="nav-item nav-link"> <i class="fa fa-home me-2"></i>Home</a>
                    <a href="index.php" class="nav-item nav-link active"><i class="fa fa-tachometer-alt me-2"></i>DASHBOARD</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-user-tie me-2"></i>ADVISORS</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="fin_approve.php" class="dropdown-item">APPROVE ADVISORS</a>
                            <a href="view_fin.php" class="dropdown-item">VIEW ADVISORS</a>
                        </div>
                    </div>
                    <a href="view_fin_plans.php" class="nav-item nav-link"><i class="fas fa-file-invoice-dollar me-2"></i>MANAGE PLANS</a>
                    <a href="view_feedback.php" class="nav-item nav-link"><i class="fas fa-comments me-2"></i>FEEDBACK</a>
                </div>
            </nav>
        </div>

        <div class="content">
            <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                <a href="#" class="sidebar-toggler flex-shrink-0"><i class="fa fa-bars"></i></a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="../User_Dashboard/img/usr.png" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex">Admin</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <a href="../Home/logout.php" class="dropdown-item">Logout</a>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid pt-4 px-4">
                <div class="bg-secondary rounded p-4 welcome-container">
                    <div class="welcome-text">
                        <span class="typing-effect">Welcome to MoneyMorph, Admin 👋</span>
                    </div>
                </div>
            </div>

            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <p class="mb-2 text-white">Total Users</p>
                                <h4 class="mb-0 text-primary"><?php echo $totalUsers; ?></h4>
                            </div>
                            <div class="stat-icon"><i class="fa fa-users"></i></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <p class="mb-2 text-white">Total Advisors</p>
                                <h4 class="mb-0 text-primary"><?php echo $totalAdvisors; ?></h4>
                            </div>
                            <div class="stat-icon"><i class="fa fa-user-tie"></i></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <p class="mb-2 text-white">Total Plans</p>
                                <h4 class="mb-0 text-primary"><?php echo $totalPlans; ?></h4>
                            </div>
                            <div class="stat-icon"><i class="fa fa-chart-line"></i></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <p class="mb-2 text-white">Feedback</p>
                                <h4 class="mb-0 text-primary"><?php echo $totalFeedback; ?></h4>
                            </div>
                            <div class="stat-icon"><i class="fa fa-comments"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>