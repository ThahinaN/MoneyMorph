<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('db_conn.php'); 

if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 1) {
    header("Location: ../Home/signin.php");
    exit;
}

$userid = $_SESSION['user_id']; 

// Cancellation Logic
if (isset($_POST['cancel_app'])) {
    $app_id = intval($_POST['app_id']);
    $stmt = $connection->prepare("UPDATE appointments SET status='Cancelled' WHERE id=? AND user_id=? AND (status='Pending' OR status='' OR status IS NULL)");
    $stmt->bind_param("ii", $app_id, $userid);
    
    if($stmt->execute()) {
        header("Location: view_appoinment.php?msg=cancelled");
    } else {
        die("Error: " . $connection->error);
    }
    exit;
}

$stmt = $connection->prepare("
    SELECT a.*, r.name as advisor_name 
    FROM appointments a
    JOIN reg r ON a.advisor_id = r.id
    WHERE a.user_id = ?
    ORDER BY a.date DESC, a.time DESC
");
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();
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
<style>
        .pulse-live { animation: pulse-blue 2s infinite; }
        @keyframes pulse-blue {
            0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
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
                     <div>
                           <a href="../Home/chat_room.php" class="nav-item nav-link "> <i class="fa fa-home me-2"></i>Chat Room</a>
                 

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


 <!-- Form Start -->

        <form>
            <div class="container-fluid pt-4 px-4 ">
                <div class="row g-4">


                    <div class="col-sm-12 col-xl-12 ">
                        <div class="bg-secondary text-center rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">Vew -Appoinments</h6>
                                
                            </div>
                            <div class="table-responsive">
                                <table class="table text-start align-middle table-bordered table-hover mb-0 ">
                                  <thead>
                        <tr class="text-white">
                            <th>Advisor</th>
                            <th>Date & Time</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
           <tbody>
                        <?php while ($row = $result->fetch_assoc()): 
                            $appTime = strtotime($row['date'] . ' ' . $row['time']);
                            $now = time();
                            $status = $row['status'] ?: 'Pending';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['advisor_name']); ?></td>
                            <td><?php echo date('M d, Y', $appTime); ?><br>
                                <small class="text-primary"><?php echo date("h:i A", $appTime); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                            <td>
                                <?php 
                                    $bg = ($status == 'Approved') ? 'bg-info' : (($status == 'Completed') ? 'bg-success' : (($status == 'Cancelled') ? 'bg-secondary' : 'bg-warning'));
                                    echo "<span class='badge $bg'>$status</span>";
                                ?>
                            </td>
                      <td>
    <?php 
        date_default_timezone_set('Asia/Kolkata');
        $now = time(); 
        $dbDate = trim($row['date']);
        $dbTime = trim($row['time']);
        $appTime = strtotime("$dbDate $dbTime");

        // Buffers
        $earlyJoinTime = $appTime - 900; // 15 mins before
        $fiveMinutesAfterStart = $appTime + 300; 
        $oneHourLater = $appTime + 3600;     
        
        $status = trim($row['status'] ?: 'Pending');
        // Convert to lowercase to avoid case-sensitivity issues
        $payment = strtolower(trim($row['payment_status'] ?? '')); 
    ?>

    <?php if ($status == 'Pending' || $status == ''): ?>
        <form method="POST" onsubmit="return confirm('Cancel this appointment?');">
            <input type="hidden" name="app_id" value="<?php echo $row['id']; ?>">
            <button type="submit" name="cancel_app" class="btn btn-outline-danger btn-sm">Cancel</button>
        </form>

    <?php elseif ($status == 'Completed'): ?>
        <span class="text-success small"><i class="fa fa-check-double"></i> Already attended</span>

    <?php elseif ($status == 'Cancelled' || $status == 'Rejected'): ?>
        <span class="badge bg-secondary">Closed</span>

    <?php elseif ($status == 'Approved'): ?>
        
        <?php if ($payment == 'verified'): ?>
            <?php if ($now >= $earlyJoinTime && $now <= $oneHourLater): ?>
                <a href="../Home/chat_room.php?room=<?php echo $row['room_id']; ?>" class="btn btn-success btn-sm pulse-live">Join Chat</a>
            <?php elseif ($now > $oneHourLater): ?>
                 <span class="text-danger small">Meeting expired</span>
            <?php else: ?>
                <span class="badge bg-info">Verified - Room opens at <?php echo date("h:i A", $appTime); ?></span>
            <?php endif; ?>
            
        <?php elseif ($payment == 'paid'): ?>
            <span class="badge bg-warning text-dark">Payment Pending Verification</span>

        <?php else: ?>
            <?php if ($now > $fiveMinutesAfterStart): ?>
                <span class="text-danger small"><i class="fa fa-times-circle"></i> You missed the meeting</span>
            <?php elseif ($now < $appTime): ?>
                <span class="badge bg-info">Payment opens at <?php echo date("h:i A", $appTime); ?></span>
            <?php elseif ($now >= $appTime && $now <= $fiveMinutesAfterStart): ?>
                <a href="payment.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Pay Now</a>
            <?php endif; ?>
        <?php endif; ?>

    <?php endif; ?>
</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    </div>
                                          
                </div>
            </div>
        </form>    
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
   <script>
    // Refresh if the user is waiting for the Advisor or the Clock
    if (document.querySelector('.bg-warning') || document.querySelector('.bg-info')) {
        setTimeout(function(){
            location.reload();
        }, 10000); 
    }
</script>
</body>

</html>