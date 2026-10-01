<?php
session_start();
include('db_conn.php');

// 1. Security Check
if (!isset($_SESSION['email']) || $_SESSION['type'] != 1) {
    header("Location: ../Home/signin.php");
    exit;
}

$user_email = $_SESSION['email'];
$transaction_id = $_GET['id'];

if (isset($transaction_id)) {
    // 2. Fetch transaction details before deleting so we can adjust the balance
    $fetch_sql = "SELECT account_id, transaction_type, amount FROM transactions WHERE transaction_id = ? AND user_email = ?";
    $fetch_stmt = $connection->prepare($fetch_sql);
    $fetch_stmt->bind_param("is", $transaction_id, $user_email);
    $fetch_stmt->execute();
    $result = $fetch_stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $account_id = $row['account_id'];
        $type = $row['transaction_type'];
        $amount = $row['amount'];

        // 3. Determine how to adjust the balance
        // If we delete a Deposit, we must subtract the amount from the account.
        // If we delete a Withdrawal, we must add the amount back to the account.
        $adjustment = ($type == 'Deposit') ? -$amount : $amount;

        // 4. Update the Bank Account Balance
        $update_sql = "UPDATE bank_accounts SET balance = balance + ? WHERE account_id = ?";
        $update_stmt = $connection->prepare($update_sql);
        $update_stmt->bind_param("di", $adjustment, $account_id);
        $update_stmt->execute();

        // 5. Finally, delete the transaction record
        $delete_sql = "DELETE FROM transactions WHERE transaction_id = ?";
        $delete_stmt = $connection->prepare($delete_sql);
        $delete_stmt->bind_param("i", $transaction_id);
        
        if ($delete_stmt->execute()) {
            echo "<script>alert('Transaction deleted and balance updated.'); location.replace('view_trns.php');</script>";
        } else {
            echo "Error deleting transaction: " . $connection->error;
        }
    } else {
        echo "<script>alert('Transaction not found or access denied.'); location.replace('view_trns.php');</script>";
    }
}

$connection->close();
exit();
?>