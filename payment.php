<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../Advisor_Dashboard/db_conn.php'); 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Home/signin.php");
    exit;
}

$app_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $connection->prepare("SELECT a.*, r.name as advisor_name FROM appointments a JOIN reg r ON a.advisor_id = r.id WHERE a.id = ? AND a.user_id = ?");
$stmt->bind_param("ii", $app_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$appointment = $result->fetch_assoc();

if (!$appointment) {
    die("Appointment not found or unauthorized access.");
}
// Handle Payment Simulation
if (isset($_POST['pay_now'])) {
    $card_number = $_POST['card_number']; 
    $last_four = substr($card_number, -4); // Get last 4 digits for records
    $amount = 50.00;

    // Start a Transaction to ensure both tables update or neither do
    $connection->begin_transaction();
try {
        // 1. Update the appointment status (Use lowercase 'paid' for consistency)
       $updateApp = $connection->prepare("UPDATE appointments SET payment_status = 'paid' WHERE id = ?");
         $updateApp->bind_param("i", $app_id);
        $updateApp->execute();

        // 2. Insert into the payments record table
        // Double check that 'appointment_id' is the correct column name in your DB
       $insertPay = $connection->prepare("INSERT INTO payments (appointment_id, user_id, amount, card_last_four) VALUES (?, ?, ?, ?)");
        $insertPay->bind_param("iids", $app_id, $_SESSION['user_id'], $amount, $last_four);
          $insertPay->execute();

        // Commit changes
        $connection->commit();

        echo "<script>alert('Payment Successful! The advisor will now verify your session.'); window.location.href='view_appoinment.php';</script>";
        exit;

    } catch (Exception $e) {
        $connection->rollback();
        echo "Error recording payment: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment - MoneyMorph</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-secondary text-white">
    <div class="container pt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card bg-dark text-white border-primary p-4">
                    <h3 class="text-primary text-center">Complete Payment</h3>
                    <hr class="bg-primary">
                    <div class="mb-3">
                        <p><strong>Advisor:</strong> <?php echo htmlspecialchars($appointment['advisor_name']); ?></p>
                        <p><strong>Service:</strong> <?php echo htmlspecialchars($appointment['purpose']); ?></p>
                        <p><strong>Date:</strong> <?php echo $appointment['date']; ?> at <?php echo $appointment['time']; ?></p>
                        <p><strong>Amount:</strong> <span class="text-success">$50.00</span></p>
                    </div>

                    <form method="POST">
                        <div class="mb-3">
    <label class="form-label">Card Number</label>
    <input type="text" name="card_number" class="form-control" placeholder="1234 5678 9101 1121" required maxlength="16">
</div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Expiry</label>
                                <input type="text" class="form-control" placeholder="MM/YY" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">CVV</label>
                                <input type="password" class="form-control" placeholder="***" required>
                            </div>
                        </div>
                        <button type="submit" name="pay_now" class="btn btn-primary w-100 mt-3">Confirm & Pay</button>
                        <a href="index.php" class="btn btn-outline-light w-100 mt-2">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>