<?php
session_start();
include('../Home/db_conn.php'); 

if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 1) {
    header("Location: ../Home/signin.php");
    exit;
}

$conn = $connection; 
$reg_id = $_SESSION['user_id']; 
$user_id = $reg_id;
$user_email = mysqli_real_escape_string($conn, $_SESSION['email']); 

/* Get user details */
$regQ = $connection->prepare("SELECT name, email FROM reg WHERE id = ?");
$regQ->bind_param("i", $reg_id);
$regQ->execute();
$result = $regQ->get_result();

if ($result->num_rows > 0) {
    $regR = $result->fetch_assoc();
    $users_name  = $regR['name'];
    $users_email = $regR['email'];
} else {
    $users_name  = "User";
    $users_email = "";
}

$currentMonth = date('Y-m');

// --- 1. USER DASHBOARD STATS ---
$sqlApp = "SELECT COUNT(*) AS c FROM appointments WHERE user_id = ?";
$stmtApp = $connection->prepare($sqlApp);
$stmtApp->bind_param("i", $reg_id);
$stmtApp->execute();
$totalAppointments = $stmtApp->get_result()->fetch_assoc()['c'];

$sqlAlerts = "SELECT COUNT(*) AS c FROM upcoming_alerts WHERE user_email = ? AND status = 'pending'";
$stmtAlerts = $connection->prepare($sqlAlerts);
$stmtAlerts->bind_param("s", $users_email);
$stmtAlerts->execute();
$totalAlerts = $stmtAlerts->get_result()->fetch_assoc()['c'];

$sqlGoalsCount = "SELECT COUNT(*) AS c FROM goals WHERE user_id = ?";
$stmtGoalsCount = $connection->prepare($sqlGoalsCount);
$stmtGoalsCount->bind_param("i", $reg_id);
$stmtGoalsCount->execute();
$totalGoals = $stmtGoalsCount->get_result()->fetch_assoc()['c'];
// --- 2. UPDATED FINANCIAL STATS ---

// A. Net Savings (Sum of all account balances)
$balanceQuery = "SELECT SUM(balance) as net_savings FROM bank_accounts WHERE user_email = '$user_email'";
$balanceResult = mysqli_query($conn, $balanceQuery);
$balanceData = mysqli_fetch_assoc($balanceResult);
$savings = $balanceData['net_savings'] ?? 0;

// B. Monthly Spend (Sum of Withdrawals for the current month)
$spendQuery = "SELECT SUM(amount) as total_spend 
               FROM transactions 
               WHERE user_email = '$user_email' 
               AND transaction_type = 'Withdraw' 
               AND transaction_date LIKE '$currentMonth%'";
$spendResult = mysqli_query($conn, $spendQuery);
$spendData = mysqli_fetch_assoc($spendResult);
$expense = $spendData['total_spend'] ?? 0;

// C. Monthly Income (Sum of Deposits for the current month)
$incomeQuery = "SELECT SUM(amount) as total_income 
                FROM transactions 
                WHERE user_email = '$user_email' 
                AND transaction_type = 'Deposit' 
                AND transaction_date LIKE '$currentMonth%'";
$incomeResult = mysqli_query($conn, $incomeQuery);
$incomeData = mysqli_fetch_assoc($incomeResult);
$income = $incomeData['total_income'] ?? 0;

// Top Spent Query remains the same (uses email based on your previous code)
$topSpentQuery = "SELECT category, SUM(amount) as total_amount, COUNT(*) as count
                  FROM transactions 
                  WHERE user_email = '$user_email' 
                  AND transaction_type = 'Withdraw' 
                  AND transaction_date LIKE '$currentMonth%'
                  GROUP BY category
                  ORDER BY total_amount DESC 
                  LIMIT 5";
$topSpentResult = mysqli_query($conn, $topSpentQuery);

// Prepare data for the Chart
$chartLabels = [];
$chartData = [];

// Reset pointer to the beginning of the result set
mysqli_data_seek($topSpentResult, 0); 

while($row = mysqli_fetch_assoc($topSpentResult)) {
    $chartLabels[] = $row['category'];
    $chartData[] = $row['total_amount'];
}

// Reset pointer again so the table below can still use it
mysqli_data_seek($topSpentResult, 0);

$goalsQuery = "SELECT * FROM goals WHERE user_id='$user_id'";
$goalsResult = mysqli_query($conn, $goalsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>MoneyMorph - Dashboard</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .welcome-container { padding: 30px; background: #191c24; border-radius: 10px; text-align: center; border: 1px solid #2c2f3b; margin-bottom: 25px; }
        .welcome-text { font-size: 2.2rem; font-weight: 700; color: #fff; }
        .typing-effect { color: #eb1616; border-right: 3px solid #eb1616; white-space: nowrap; overflow: hidden; display: inline-block; animation: typing 3s steps(30, end), blink 0.75s step-end infinite; }
        @keyframes typing { from { width: 0; } to { width: 100%; } }
        @keyframes blink { from, to { border-color: transparent; } 50% { border-color: #eb1616; } }
        .stat-card { background: #191c24; border-radius: 10px; padding: 25px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #2c2f3b; transition: 0.3s; }
        .stat-card:hover { background: #232732; transform: translateY(-5px); }
        .stat-icon { width: 55px; height: 55px; background: rgba(235, 22, 22, 0.1); color: #eb1616; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; }
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
                    <a href="index.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>DASHBOARD</a>
                       <a href="view_appoinment.php" class="nav-item nav-link"><i class="fa fa-calendar-check me-2"></i>APPOINTMENTS</a>
                    <a href="fin_plan.php" class="nav-item nav-link"><i class="fa fa-file-invoice-dollar me-2"></i>FIN PLANS</a>
                    
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-bullseye me-2"></i>GOALS</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="view_goal.php" class="dropdown-item">MANAGE GOALS</a>
                            <a href="add_goal.php" class="dropdown-item">ADD GOALS</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-credit-card me-2"></i>ACCOUNTS</a>
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

        <div class="content">
           <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                <a href="#" class="sidebar-toggler flex-shrink-0"><i class="fa fa-bars"></i></a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="img/usr.png" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex"><?php echo htmlspecialchars($users_name); ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <a href="alert.php" class="dropdown-item">Alerts</a>
                            <a href="../Home/logout.php" class="dropdown-item">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid pt-4 px-4">
                <div class="welcome-container">
                    <div class="welcome-text"><span class="typing-effect">Welcome back, <?php echo htmlspecialchars($users_name); ?>! 👋</span></div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-sm-6 col-xl-4"><div class="stat-card"><div><p class="mb-2 text-white">Appointments</p><h4 class="mb-0 text-primary"><?php echo $totalAppointments; ?></h4></div><div class="stat-icon"><i class="fa fa-calendar-alt"></i></div></div></div>
                    <div class="col-sm-6 col-xl-4"><div class="stat-card"><div><p class="mb-2 text-white">Pending Alerts</p><h4 class="mb-0 text-primary"><?php echo $totalAlerts; ?></h4></div><div class="stat-icon"><i class="fa fa-bell"></i></div></div></div>
                    <div class="col-sm-6 col-xl-4"><div class="stat-card"><div><p class="mb-2 text-white">My Goals</p><h4 class="mb-0 text-primary"><?php echo $totalGoals; ?></h4></div><div class="stat-icon"><i class="fa fa-bullseye"></i></div></div></div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-sm-6 col-xl-4"><div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4"><i class="fa fa-wallet fa-3x text-success"></i><div class="ms-3"><p class="mb-2">Monthly Income</p><h4 class="mb-0">₹<?php echo number_format($income); ?></h4></div></div></div>
                    <div class="col-sm-6 col-xl-4"><div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4"><i class="fa fa-shopping-cart fa-3x text-primary"></i><div class="ms-3"><p class="mb-2">Monthly Spend</p><h4 class="mb-0">₹<?php echo number_format($expense); ?></h4></div></div></div>
                    <div class="col-sm-6 col-xl-4"><div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4"><i class="fa fa-piggy-bank fa-3x text-info"></i><div class="ms-3"><p class="mb-2">Net Savings</p><h4 class="mb-0">₹<?php echo number_format($savings); ?></h4></div></div></div>
                </div>

                <div class="row g-4">
    <div class="col-sm-12 col-xl-6">
        <div class="bg-secondary rounded p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h6 class="mb-0">Top Spending Categories (<?php echo date('F'); ?>)</h6>
                <a href="view_trns.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table text-start align-middle table-bordered table-hover mb-0">
                    <thead>
                        <tr class="text-white">
                            <th>Category</th>
                            <th>Trans.</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Ensure we are at the start of the result set
                        if (mysqli_num_rows($topSpentResult) > 0):
                            mysqli_data_seek($topSpentResult, 0); 
                            while($row = mysqli_fetch_assoc($topSpentResult)): 
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td><?php echo $row['count']; ?></td>
                                <td class="text-primary">₹<?php echo number_format($row['total_amount'], 2); ?></td>
                            </tr>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">No expenses recorded.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>

   <div class="col-sm-12 col-xl-6">
    <div class="bg-secondary rounded p-4 h-100">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Active Goals Tracker</h6>
            <a href="view_goal.php" class="btn btn-sm btn-outline-primary">Manage</a>
        </div>
        <?php 
        if(mysqli_num_rows($goalsResult) > 0): 
            mysqli_data_seek($goalsResult, 0);
            while($goal = mysqli_fetch_assoc($goalsResult)): 
                $now = new DateTime();
                $targetDate = new DateTime($goal['target_date']);
                
                // Use ACTUAL data from the database updated by your deposit script
                $actualSaved = $goal['current_amount']; 
                $targetAmount = $goal['target_amount'];
                
                // Calculate percentage based on real deposits
                $percent = ($targetAmount > 0) ? min(100, ($actualSaved / $targetAmount) * 100) : 0;
                
                // Calculate days remaining
                $interval = $now->diff($targetDate);
                $daysLeft = (int)$interval->format("%r%a");
        ?>
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span><strong><?php echo htmlspecialchars($goal['goal_name']); ?></strong></span>
                        <span class="badge <?php echo $daysLeft > 0 ? 'bg-primary' : 'bg-danger'; ?>">
                            <?php echo $daysLeft > 0 ? $daysLeft . " days left" : "Expired/Due"; ?>
                        </span>
                    </div>
                    <div class="progress" style="height: 12px; background-color: #191c24;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                             role="progressbar" 
                             style="width: <?php echo $percent; ?>%" 
                             aria-valuenow="<?php echo $percent; ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-white-50">Actual Saved: ₹<?php echo number_format($actualSaved); ?></small>
                        <small class="text-white-50">Target: ₹<?php echo number_format($targetAmount); ?></small>
                    </div>
                    <div class="text-end">
                        <small class="text-primary" style="font-size: 0.75rem;">
                            <?php echo round($percent, 1); ?>% Completed
                        </small>
                    </div>
                </div>
        <?php 
            endwhile; 
        else: 
        ?>
            <div class="text-center py-5">
                <p class="text-muted">No active goals found.</p>
                <a href="add_goal.php" class="btn btn-primary btn-sm">Create Goal</a>
            </div>
        <?php endif; ?>
    </div>
</div>
</div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>