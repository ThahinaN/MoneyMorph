<?php
session_start();
include('db_conn.php'); 
// Top of alert.php
if (!isset($_SESSION['email']) || $_SESSION['type'] != 1) {
    header("Location: ../Home/signin.php");
    exit;
}
$userid = $_SESSION['user_id'];
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
 <!-- Add this in the head section of your HTML -->
 <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        /* Sticky footer CSS */
        html, body {
            height: 100%;
            margin: 0;
        }

        .container-fluid.position-relative.d-flex.p-0 {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1 0 auto;
        }

       
        /* Alert Styling */
        .alert-custom {
            background-color: red; /* Red background for alerts */
            color: black; /* Black text color */
            padding: 25px;
            border-radius: 5px;
        }

        /* Update your <style> section */
.alert-custom {
    background-color: rgba(235, 22, 22, 0.2); /* Transparent red */
    border-left: 5px solid #eb1616; /* Solid red accent */
    color: #ffffff;
    padding: 20px;
    margin: 20px;
    border-radius: 4px;
    position: relative;
}

.alert-custom h5 {
    color: #eb1616;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 0.9rem;
}
    </style>
</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">
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
   
    <!-- Navbar End -->
        <div> <script>
               document.addEventListener("DOMContentLoaded", function() {
    fetch('fetch_alerts.php')
        .then(response => response.json())
        .then(alerts => {
            if (alerts && alerts.length > 0) {
                let alertBox = document.createElement('div');
                alertBox.classList.add('alert-custom', 'alert-dismissible', 'fade', 'show');
                
                // Header with a close button
                alertBox.innerHTML = `
                    <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-2" data-bs-dismiss="alert"></button>
                    <h5><i class="fa fa-exclamation-circle me-2"></i>Upcoming Payments</h5>
                `;

                alerts.forEach(alert => {
                    let dueDate = new Date(alert.due_date);
                    let formattedDate = dueDate.toLocaleDateString('en-IN', {
                        day: '2-digit',
                        month: 'short'
                    });
                    
                    // Formatting the amount as currency
                    let amount = parseFloat(alert.amount).toLocaleString('en-IN');

                    alertBox.innerHTML += `
                        <div class="d-flex justify-content-between border-bottom border-dark py-2">
                            <span>${alert.description}</span>
                            <span><strong>₹${amount}</strong> on ${formattedDate}</span>
                        </div>`;
                });

                const contentArea = document.querySelector('.content');
                if(contentArea) contentArea.prepend(alertBox);
            }
        })
        .catch(error => console.error('Error fetching alerts:', error));
});
            </script>
            </div>
            <!-- Page content here -->
            
          
        </div>
        <!-- Content End -->
    </div>

    <!-- JS Libraries and Template Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
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
