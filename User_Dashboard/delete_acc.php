<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('db_conn.php');

// Security Check: Ensure user is logged in
if (!isset($_SESSION['email']) || $_SESSION['type'] != 1) {
    header("Location: ../Home/signin.php");
    exit;
}

$user_email = $_SESSION['email'];

// Check if account_id is provided via GET (e.g., delete_acc.php?id=5)
if (isset($_GET['id'])) {
    $account_id = intval($_GET['id']);

    // Prepare DELETE statement with a check for user_email for security
    $sql = "DELETE FROM bank_accounts WHERE account_id = ? AND user_email = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("is", $account_id, $user_email);

    if ($stmt->execute()) {
        // Check if any row was actually deleted
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Account deleted successfully');location.replace('view_acc.php');</script>";
        } else {
            echo "<script>alert('Account not found or access denied');location.replace('view_acc.php');</script>";
        }
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
    $stmt->close();
} else {
    header("Location: view_acc.php");
    exit;
}
?>