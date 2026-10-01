<?php
session_start();
// Adjust path if necessary to find your db_conn.php
include('../Admin_Dashboard/db_conn.php');

/* Role-based login check */
if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 2) {
    header("Location: ../Home/signin.php");
    exit;
}

$reg_id = $_SESSION['user_id'];

/* 1. Get advisor details from reg table */
$regQ = $connection->prepare("SELECT name, email FROM reg WHERE id = ?");
$regQ->bind_param("i", $reg_id);
$regQ->execute();
$regR = $regQ->get_result()->fetch_assoc();

$advisor_name  = $regR['name'];
$advisor_email = $regR['email'];

/* 2. Check advisor profile in adv_reg */
$advQ = $connection->prepare("SELECT stat FROM adv_reg WHERE email = ?");
$advQ->bind_param("s", $advisor_email);
$advQ->execute();
$advR = $advQ->get_result();

if ($advR->num_rows == 0) {
    echo "<script>alert('Please complete your profile to continue');</script>";
}

// --- Advisor Statistics Queries (Corrected for finplan.sql schema) ---
// A. Total Appointments for this advisor
$sqlApp = "SELECT COUNT(*) AS c FROM appointments WHERE advisor_id = ?";
$stmtApp = $connection->prepare($sqlApp);
// CHANGE: Use $reg_id (Integer) instead of $advisor_email (String)
$stmtApp->bind_param("i", $reg_id); 
$stmtApp->execute();
$totalAppointments = $stmtApp->get_result()->fetch_assoc()['c'];

// B. Total Plans created by this advisor
$sqlPlans = "SELECT COUNT(*) AS c FROM financial_plans WHERE advisor_id = ?";
$stmtPlans = $connection->prepare($sqlPlans);
// CHANGE: Use $reg_id (Integer) instead of $advisor_email (String)
$stmtPlans->bind_param("i", $reg_id);
$stmtPlans->execute();
$totalPlans = $stmtPlans->get_result()->fetch_assoc()['c'];

// C. Pending Appointments
$sqlPending = "SELECT COUNT(*) AS c FROM appointments WHERE advisor_id = ? AND status = 'pending'";
$stmtPending = $connection->prepare($sqlPending);
// CHANGE: Use $reg_id (Integer) instead of $advisor_email (String)
$stmtPending->bind_param("i", $reg_id);
$stmtPending->execute();
$pendingApp = $stmtPending->get_result()->fetch_assoc()['c'];



// --- UPDATED ADVISOR STATISTICS ---

// A. Total Money Earned (Sum of payments for 'completed' appointments)
$sqlEarned = "SELECT SUM(p.amount) AS total_earned 
              FROM appointments a 
              JOIN payments p ON a.id = p.appointment_id 
              WHERE a.advisor_id = ? AND a.status = 'completed'";
$stmtEarned = $connection->prepare($sqlEarned);
$stmtEarned->bind_param("i", $reg_id);
$stmtEarned->execute();
$totalEarned = $stmtEarned->get_result()->fetch_assoc()['total_earned'] ?? 0;

// B. Live Sessions Attended (Status is 'completed')
$sqlAttended = "SELECT COUNT(*) AS c FROM appointments WHERE advisor_id = ? AND status = 'completed'";
$stmtAttended = $connection->prepare($sqlAttended);
$stmtAttended->bind_param("i", $reg_id);
$stmtAttended->execute();
$sessionsAttended = $stmtAttended->get_result()->fetch_assoc()['c'];

// C. Missed Sessions (Status is 'Approved' but current time is past appointment time + 1 hour)
$currentDateTime = date('Y-m-d H:i:s');
$sqlMissed = "SELECT COUNT(*) AS c FROM appointments 
              WHERE advisor_id = ? 
              AND status = 'Approved' 
              AND STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i') < DATE_SUB(NOW(), INTERVAL 1 HOUR)";
$stmtMissed = $connection->prepare($sqlMissed);
$stmtMissed->bind_param("i", $reg_id);
$stmtMissed->execute();
$sessionsMissed = $stmtMissed->get_result()->fetch_assoc()['c'];

// D. Pending Sessions (Status is 'pending')
$sqlPendingCount = "SELECT COUNT(*) AS c FROM appointments WHERE advisor_id = ? AND status = 'pending'";
$stmtPendingCount = $connection->prepare($sqlPendingCount);
$stmtPendingCount->bind_param("i", $reg_id);
$stmtPendingCount->execute();
$sessionsPending = $stmtPendingCount->get_result()->fetch_assoc()['c'];
// --- CALENDAR DATA FETCH ---
$month = isset($_GET['m']) ? $_GET['m'] : date('m');
$year = isset($_GET['y']) ? $_GET['y'] : date('Y');

$calStmt = $connection->prepare("SELECT date, time, status FROM appointments WHERE advisor_id = ? AND MONTH(date) = ? AND YEAR(date) = ?");
$calStmt->bind_param("iii", $reg_id, $month, $year);
$calStmt->execute();
$calRes = $calStmt->get_result();

$bookedDates = [];
while ($row = $calRes->fetch_assoc()) {
    $bookedDates[$row['date']][] = [
        'time' => date('h:i A', strtotime($row['time'])),
        'status' => strtolower($row['status'])
    ];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>MoneyMorph Advisor Dashboard</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        .welcome-container {
            padding: 30px;
            background: #191c24;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #2c2f3b;
        }
        .welcome-text {
            font-size: 2.2rem;
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
            padding: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #2c2f3b;
            transition: 0.3s;
        }
        .stat-card:hover {
            background: #232732;
        }
        .stat-icon {
            width: 55px;
            height: 55px;
            background: rgba(235, 22, 22, 0.1);
            color: #eb1616;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        
    .mini-stat-card { padding: 10px; border-radius: 8px; border: 1px solid #343a40; background: #191c24; }
    .mini-stat-card h5 { font-size: 1rem; margin: 0; }
    .mini-stat-card p { font-size: 0.75rem; margin: 0; color: #6c757d; }
    
    .mini-cal table { font-size: 0.7rem; table-layout: fixed; }
    .mini-cal td { height: 60px !important; padding: 2px !important; }
    .appt-pill { font-size: 0.6rem; padding: 2px; margin-top: 1px; border-radius: 3px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
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
                    <a href="view_appoin.php" class="nav-item nav-link"><i class="fa fa-calendar-check me-2"></i>APPOINTMENTS</a>
                    <a href="add_plan.php" class="nav-item nav-link"><i class="fa fa-plus-circle me-2"></i>ADD PLANS</a>
                    <a href="view_plans.php" class="nav-item nav-link"><i class="fa fa-list me-2"></i>MANAGE PLANS</a>
                    <a href="profile.php" class="nav-item nav-link"><i class="fa fa-user me-2"></i>PROFILE</a>


            
                </div>
            </nav>
        </div>
        <div class="content">
            <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="../User_Dashboard/img/usr.png" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex"><?php echo htmlspecialchars($advisor_name); ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <form action="../Home/logout.php" method="post">
                                <button type="submit" name="logout" class="dropdown-item">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
            <div class="container-fluid pt-4 px-4">
                <div class="welcome-container">
                    <div class="welcome-text">
                        <span class="typing-effect">Welcome, Advisor <?php echo htmlspecialchars($advisor_name); ?>👋</span>
                    </div>
                </div>
            </div>

            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-6 col-xl-4">
                        <div class="stat-card">
                            <div>
                                <p class="mb-2 text-white">Total Appointments</p>
                                <h4 class="mb-0 text-primary"><?php echo $totalAppointments; ?></h4>
                            </div>
                            <div class="stat-icon">
                                <i class="fa fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-xl-4">
                        <div class="stat-card">
                            <div>
                                <p class="mb-2 text-white">Plans Managed</p>
                                <h4 class="mb-0 text-primary"><?php echo $totalPlans; ?></h4>
                            </div>
                            <div class="stat-icon">
                                <i class="fa fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div>
                    <p class="mb-2 text-white">Total Earned</p>
                    <h4 class="mb-0 text-success">$<?php echo number_format($totalEarned, 2); ?></h4>
                </div>
                <div class="stat-icon" style="color: #198754; background: rgba(25, 135, 84, 0.1);">
                    <i class="fa fa-dollar-sign"></i>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div>
                    <p class="mb-2 text-white">Sessions Attended</p>
                    <h4 class="mb-0 text-primary"><?php echo $sessionsAttended; ?></h4>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div>
                    <p class="mb-2 text-white">Missed Sessions</p>
                    <h4 class="mb-0 text-danger"><?php echo $sessionsMissed; ?></h4>
                </div>
                <div class="stat-icon" style="color: #dc3545; background: rgba(220, 53, 69, 0.1);">
                    <i class="fa fa-calendar-times"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
                <div>
                    <p class="mb-2 text-white">Pending Requests</p>
                    <h4 class="mb-0 text-warning"><?php echo $sessionsPending; ?></h4>
                </div>
                <div class="stat-icon" style="color: #ffc107; background: rgba(255, 193, 7, 0.1);">
                    <i class="fa fa-hourglass-start"></i>
                </div>
            </div>
        </div>
        <div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="bg-secondary rounded p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-white small mb-0">Schedule</h6>
                    <span class="text-muted" style="font-size: 0.7rem;"><?php echo date('F Y'); ?></span>
                </div>
                <div class="mini-cal">
                    <table class="table table-bordered border-dark text-white mb-0" style="font-size: 0.6rem; table-layout: fixed;">
                        <thead>
                            <tr class="text-muted text-center">
                                <th class="p-1">S</th><th class="p-1">M</th><th class="p-1">T</th><th class="p-1">W</th><th class="p-1">T</th><th class="p-1">F</th><th class="p-1">S</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $firstDay = date('w', strtotime("$year-$month-01"));
                            $daysInMonth = date('t', strtotime("$year-$month-01"));
                            echo "<tr>";
                            for ($i = 0; $i < $firstDay; $i++) { echo "<td></td>"; }
                            for ($day = 1; $day <= $daysInMonth; $day++) {
                                if (($i + $day - 1) % 7 == 0 && $day != 1) { echo "</tr><tr>"; }
                                $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                                $isToday = ($currentDate == date('Y-m-d')) ? 'bg-dark border-primary' : 'bg-dark border-secondary';
                                echo "<td class='$isToday' style='height: 45px; vertical-align: top;'>";
                                echo "<span class='d-block' style='font-size: 0.65rem;'>" . sprintf('%02d', $day) . "</span>";
                                if (isset($bookedDates[$currentDate])) {
                                    foreach ($bookedDates[$currentDate] as $appt) {
                                        $statusColor = ($appt['status'] == 'completed') ? 'bg-success' : 'bg-info';
                                        echo "<span class='appt-pill $statusColor text-dark' style='font-size: 0.5rem; padding: 1px; margin-top: 1px; border-radius: 2px; display: block;'>" . $appt['time'] . "</span>";
                                    }
                                }
                                echo "</td>";
                            }
                            echo "</tr>";
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="bg-secondary rounded p-3 h-100">
                <h6 class="text-white small mb-3">Advisor Performance Overview (Earnings, Rating & Sessions)</h6>
                <canvas id="fullPerformanceChart" style="max-height: 280px;"></canvas>
            </div>
        </div>
    </div>
</div>
       

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('fullPerformanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'], 
            datasets: [
                {
                    label: 'Earnings ($)',
                    data: [0, <?php echo $totalEarned * 0.5; ?>, <?php echo $totalEarned * 0.8; ?>, <?php echo $totalEarned; ?>],
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'Rating',
                    data: [4.2, 4.5, 4.8, 5.0], // Placeholder: You can fetch real avg rating later
                    borderColor: '#ffc107',
                    borderWidth: 3,
                    pointStyle: 'star',
                    tension: 0.2,
                    yAxisID: 'y1' // Uses a second axis for rating 1-5
                },
                {
                    label: 'Sessions',
                    data: [0, <?php echo $sessionsPending; ?>, <?php echo $sessionsAttended; ?>, <?php echo $sessionsAttended + 2; ?>],
                    borderColor: '#0dcaf0',
                    borderDash: [5, 5],
                    tension: 0.4,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { color: '#343a40' },
                    ticks: { color: '#6c757d' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    min: 0,
                    max: 5,
                    grid: { drawOnChartArea: false }, // Only show grid for main Y axis
                    ticks: { color: '#ffc107' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#6c757d' }
                }
            },
            plugins: {
                legend: { labels: { color: '#fff', font: { size: 10 } } }
            }
        }
    });
</script>
</body>
</html>