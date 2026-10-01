<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Admin authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 0) {
    header("Location: ../Home/signin.php");
    exit;
}

// Database connection
include('db_conn.php');

// Check if advisor ID is received
if (isset($_POST['id'])) {
    $vID = $_POST['id'];

    // Prepare delete query
    $stmt = $connection->prepare("DELETE FROM adv_reg WHERE id = ?");
    $stmt->bind_param("i", $vID);

    if ($stmt->execute()) {
        header("Location: fin_approve.php");
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$connection->close();
?>
