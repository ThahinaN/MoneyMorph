<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('db_conn.php');

/* 🔐 Admin-only access */
if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 0) {
    header("Location: ../Home/signin.php");
    exit;
}

/* ✅ Check advisor id - matched to 'approve_id' from your form */
if (isset($_POST['approve_id'])) {

    $advisorId = intval($_POST['approve_id']);
    
    // Updated SQL: removed 'status' from bind_param because it's hardcoded in SQL
    $stmt = $connection->prepare("UPDATE adv_reg SET stat = 2 WHERE id = ?");
    $stmt->bind_param("i", $advisorId);

    if ($stmt->execute()) {
        header("Location: fin_approve.php?success=1");
        exit();
    } else {
        echo "Error approving advisor: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request. ID not received.";
}

$connection->close();
?>