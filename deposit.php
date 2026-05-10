<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('db_conn.php'); 

// 1. Security & Session Check
if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 1) {
    header("Location: ../Home/signin.php");
    exit;
}

$userid = $_SESSION['user_id'];
$user_email = $_SESSION['email']; 

// Fallback for user name to avoid "Table users doesn't exist" error
// If your session has a name, use it; otherwise, use a generic label.
$users_name = $_SESSION['name'] ?? 'Dashboard';

// 2. Fetch Goal Info from URL (?deposit_id=X)
if (!isset($_GET['deposit_id'])) {
    header("Location: view_goal.php");
    exit;
}

$d_id = $_GET['deposit_id'];
$stmt = $connection->prepare("SELECT id, goal_name, current_amount, target_amount FROM goals WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $d_id, $userid);
$stmt->execute();
$goalData = $stmt->get_result()->fetch_assoc();

if (!$goalData) {
    echo "<script>alert('Goal not found.'); window.location.href='view_goal.php';</script>";
    exit;
}

// 3. Handle Deposit Transaction
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'deposit') {
    $amount = floatval($_POST['depositAmount']);
    $goalId = $_POST['goal_id'];
    $acc_id = $_POST['account_id'];

    $connection->begin_transaction();

    try {
        // A. Validate Account Balance
        $acc_check = $connection->prepare("SELECT balance FROM bank_accounts WHERE account_id = ? AND user_email = ?");
        $acc_check->bind_param("is", $acc_id, $user_email);
        $acc_check->execute();
        $acc_res = $acc_check->get_result()->fetch_assoc();

        if (!$acc_res) {
            throw new Exception("Selected bank account not found.");
        }
        
        if ($acc_res['balance'] < $amount) {
            throw new Exception("Insufficient balance! Available: ₹" . number_format($acc_res['balance']));
        }

        // B. Update Goal Progress
        $stmt1 = $connection->prepare("UPDATE goals SET current_amount = current_amount + ? WHERE id = ? AND user_id = ?");
        $stmt1->bind_param("dii", $amount, $goalId, $userid);
        $stmt1->execute();

        // C. Deduct from Bank Account
        $stmt2 = $connection->prepare("UPDATE bank_accounts SET balance = balance - ? WHERE account_id = ? AND user_email = ?");
        $stmt2->bind_param("dis", $amount, $acc_id, $user_email);
        $stmt2->execute();

        // D. Record Transaction log
        $stmt3 = $connection->prepare("INSERT INTO transactions (user_email, amount, category, transaction_type, transaction_date) VALUES (?, ?, 'Goal Deposit', 'Withdraw', NOW())");
        $stmt3->bind_param("sd", $user_email, $amount);
        $stmt3->execute();

        $connection->commit();
        echo "<script>alert('Deposit Successful!'); window.location.href='view_goal.php';</script>";
        exit;

    } catch (Exception $e) {
        $connection->rollback();
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>MoneyMorph - Deposit</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="index.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary">MoneyMorph</h3>
                </a>
                <div class="navbar-nav w-100">
                    <a href="index.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>DASHBOARD</a>
                    <a href="view_goal.php" class="nav-item nav-link active"><i class="fa fa-th me-2"></i>GOALS</a>
                    <a href="view_acc.php" class="nav-item nav-link"><i class="fa fa-university me-2"></i>ACCOUNTS</a>
                </div>
            </nav>
        </div>

        <div class="content">
            <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                <a href="#" class="sidebar-toggler flex-shrink-0"><i class="fa fa-bars"></i></a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <span class="d-none d-lg-inline-flex"><?php echo htmlspecialchars($users_name); ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <a href="../Home/logout.php" class="dropdown-item">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid pt-4 px-4">
                <div class="row g-4 justify-content-center">
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4">
                            <h3 class="text-primary text-center mb-4">Deposit to Goal</h3>
                            
                            <div class="p-3 mb-4 bg-dark rounded border border-primary">
                                <h6 class="text-white mb-1">Goal: <?php echo htmlspecialchars($goalData['goal_name']); ?></h6>
                                <div class="progress mb-2" style="height: 5px;">
                                    <?php 
                                        $percent = ($goalData['current_amount'] / $goalData['target_amount']) * 100;
                                        $percent = ($percent > 100) ? 100 : $percent;
                                    ?>
                                    <div class="progress-bar bg-primary" style="width: <?php echo $percent; ?>%"></div>
                                </div>
                                <small class="text-muted">Saved: ₹<?php echo number_format($goalData['current_amount']); ?> / Target: ₹<?php echo number_format($goalData['target_amount']); ?></small>
                            </div>

                            <form method="POST">
                                <input type="hidden" name="action" value="deposit">
                                <input type="hidden" name="goal_id" value="<?php echo $goalData['id']; ?>">

                                <div class="mb-3">
                                    <label class="form-label">Select Source Bank Account</label>
                                    <select class="form-select bg-dark text-white border-0" name="account_id" required>
                                        <option value="" selected disabled>Choose Account...</option>
                                        <?php
                                        $acc_stmt = $connection->prepare("SELECT account_id, account_title, balance FROM bank_accounts WHERE user_email = ?");
                                        $acc_stmt->bind_param("s", $user_email);
                                        $acc_stmt->execute();
                                        $accounts = $acc_stmt->get_result();
                                        while($row = $accounts->fetch_assoc()): ?>
                                            <option value="<?php echo $row['account_id']; ?>">
                                                <?php echo htmlspecialchars($row['account_title']); ?> (Bal: ₹<?php echo number_format($row['balance']); ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Amount to Transfer (₹)</label>
                                    <input type="number" step="0.01" class="form-control bg-dark text-white border-0" name="depositAmount" placeholder="Enter amount" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 mb-2">Confirm Deposit</button>
                                <a href="view_goal.php" class="btn btn-outline-light w-100">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>