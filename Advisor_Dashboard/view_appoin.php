<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('db_conn.php');
session_start();


$needsRefresh = false;

// Authentication
if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 2) {
    header("Location: ../Home/signin.php");
    exit;
}

$Adid = $_SESSION['user_id'];
date_default_timezone_set('Asia/Kolkata');
$now = time(); 

// --- SINGLE CONSOLIDATED POST HANDLER ---
// --- RESTRUCTURED INDEPENDENT POST HANDLERS ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['appointmentId'])) {
    $appId = intval($_POST['appointmentId']);

    // --- Action 1: APPROVE ---
    if (isset($_POST['approve'])) {
        $roomId = bin2hex(random_bytes(8)); 
        $stmt = $connection->prepare("UPDATE appointments SET status='Approved', room_id=? WHERE id=? AND advisor_id=?");
        $stmt->bind_param("sii", $roomId, $appId, $Adid);
        $stmt->execute();
        header("Location: view_appoin.php?msg=approved");
        exit;
    } 

    // --- Action 2: REJECT ---
    if (isset($_POST['reject'])) {
        $stmt = $connection->prepare("UPDATE appointments SET status='Rejected', room_id=NULL WHERE id=? AND advisor_id=?");
        $stmt->bind_param("ii", $appId, $Adid);
        $stmt->execute();
        header("Location: view_appoin.php?msg=rejected");
        exit;
    }

    // --- Action 3: VERIFY PAYMENT (The critical fix) ---
    // We make this its own separate 'if' block
    // --- Action 3: VERIFY PAYMENT (The critical fix) ---
if (isset($_POST['verify_payment'])) {
  $stmt = $connection->prepare("UPDATE appointments SET payment_status='Verified' WHERE id=? AND advisor_id=?");
      $stmt->bind_param("ii", $appId, $Adid);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            header("Location: view_appoin.php?msg=verified_success");
        } else {
            // This happens if the ID or Advisor ID didn't match anything
            header("Location: view_appoin.php?msg=no_change");
        }
        exit;
    } else {
        die("Update Failed: " . $stmt->error);
    }
}

    // --- Action 4: COMPLETE ---
    if (isset($_POST['complete'])) {
        $stmt = $connection->prepare("UPDATE appointments SET status='Completed' WHERE id=? AND advisor_id=?");
        $stmt->bind_param("ii", $appId, $Adid);
        $stmt->execute();
        header("Location: view_appoin.php?msg=completed");
        exit;
    }
}
// Updated SQL to join the payments table... (rest of your code is fine)
// Optimized SQL to prevent duplicate rows using GROUP BY
$stmt = $connection->prepare("
    SELECT 
        a.id, a.user_id, a.date, a.time, a.purpose, a.status, a.payment_status, a.room_id,
        r.name as user_name, r.email as user_email, 
        MAX(p.amount) as amount 
    FROM appointments a
    JOIN reg r ON a.user_id = r.id
    LEFT JOIN payments p ON a.id = p.appointment_id
    WHERE a.advisor_id = ?
    GROUP BY a.id
    ORDER BY a.date ASC, a.time ASC
");
$stmt->bind_param("i", $Adid);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>MoneyMorph

</title>
    <title>MoneyMorph

 Advisor Dashboard</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="img/favicon.ico" rel="icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .pulse-live { animation: pulse-blue 2s infinite; }
        @keyframes pulse-blue {
            0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
        }
        .text-danger.small {
    font-weight: 600;
    letter-spacing: 0.5px;
}
    </style>
    <!-- Add your existing head content here -->
</head>
<body>
<div class="container-fluid position-relative d-flex p-0">
        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary">MoneyMorph

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
            <div class="col-sm-12 col-xl-12">
                <div class="bg-secondary text-center rounded p-4">
                    <h6 class="mb-0">Appointments</h6>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                           <thead>
                               <tr class="text-white">
    <th>Client</th>
    <th>Schedule</th>
    <th>Purpose</th>
    <th>Amount</th> <th>Status</th>
    <th>Actions</th>
</tr>
                            </thead>
                           <tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): 
            $rawStatus = strtolower(trim($row['status'] ?? 'pending'));
            $rawPayment = strtolower(trim($row['payment_status'] ?? ''));

            if ($rawStatus == 'approved' && ($rawPayment == 'paid' || $rawPayment == 'pending' || $rawPayment == '')) {
                $needsRefresh = true;
            }

            $appTime = strtotime(trim($row['date']) . ' ' . trim($row['time']));
            
            $isVerified = ($rawPayment === 'verified');
            $hasPaid = ($rawPayment === 'paid' || $rawPayment === 'pending');
            $isLive = ($now >= ($appTime - 900) && $now <= ($appTime + 3600)); 
        ?>
        <tr>
            <td>
                <strong><?php echo htmlspecialchars($row['user_name']); ?></strong><br>
                <small class="text-muted"><?php echo htmlspecialchars($row['user_email']); ?></small>
            </td>
            <td>
                <?php echo date('d M Y', $appTime); ?><br>
                <small class="text-primary font-weight-bold"><?php echo date('h:i A', $appTime); ?></small>
            </td>
            <td><?php echo htmlspecialchars($row['purpose']); ?></td>

            <td>
                <?php if (!empty($row['amount'])): ?>
                    <span class="text-success fw-bold">$<?php echo number_format($row['amount'], 2); ?></span>
                <?php else: ?>
                    <span class="text-muted small">Not Paid</span>
                <?php endif; ?>
            </td>

            <td>
                <?php
                    // Main Appointment Status Badge
                  $class = ($rawStatus == 'approved') ? 'bg-info' : (($rawStatus == 'completed') ? 'bg-success' : (($rawStatus == 'rejected') ? 'bg-danger' : 'bg-warning text-dark'));
        echo "<span class='badge $class'>" . ucfirst($rawStatus) . "</span>";
                    
                    // Payment Status Badge
                    if ($rawPayment === 'verified') {
            echo " <span class='badge bg-success'><i class='fa fa-check-double'></i> Verified</span>";
        } elseif ($rawPayment === 'paid') {
            echo " <span class='badge bg-info'><i class='fa fa-check'></i> Paid</span>";
        } else {
            echo " <span class='badge bg-secondary'>Pending Payment</span>";
        }
                ?>
            </td>
         <td>
    <form method="POST">
        <input type="hidden" name="appointmentId" value="<?php echo $row['id']; ?>">
        
        <?php if ($rawStatus == 'pending'): ?>
            <button name="approve" class="btn btn-success btn-sm">Approve</button>
            <button name="reject" class="btn btn-danger btn-sm">Reject</button>
        
        <?php elseif ($rawStatus == 'approved'): 
            $oneHourLater = $appTime + 3600; 
        ?>
            <?php if ($now > $oneHourLater): ?>
                <span class="text-danger small"><i class="fa fa-clock"></i> Meeting expired</span>
            <?php elseif ($rawPayment === 'verified'): ?>
                <span class="badge bg-success mb-1">Payment Verified</span><br>
                <a href="../Home/chat_room.php?room=<?php echo $row['room_id']; ?>" class="btn btn-outline-warning btn-sm pulse-live">Start Chat</a>
            <?php elseif ($rawPayment === 'paid' || $rawPayment === 'Paid'): ?>
                <button name="verify_payment" class="btn btn-warning btn-sm">Confirm & Verify Payment</button>
            <?php else: ?>
                <span class="text-muted small">Waiting for User Payment</span>
            <?php endif; ?>

        <?php elseif ($rawStatus == 'completed'): ?>
            <span class="text-success small"><i class="fa fa-check"></i> Session Completed</span>
            
        <?php else: ?>
            <span class="text-muted small">No Action Required</span>
        <?php endif; ?>
    </form>
</td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="6" class="text-center py-4 text-muted">No appointments found.</td></tr>
    <?php endif; ?>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
<?php if ($needsRefresh): ?>
    console.log("Waiting for user payments or verification... refreshing in 10 seconds.");
    setTimeout(function(){
       location.reload();
    }, 10000); 
<?php endif; ?>
</script>
</body>

</html>

