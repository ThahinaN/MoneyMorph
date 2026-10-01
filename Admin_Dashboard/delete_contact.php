<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('db_conn.php');

/* 🔐 Admin-only access */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 0) {
    header("Location: ../Home/signin.php");
    exit;
}

/* ✅ Check delete request */
if (isset($_POST['delete_id'])) {

    $delete_id = intval($_POST['delete_id']);

    $stmt = $connection->prepare(
        "DELETE FROM contact_us WHERE id = ?"
    );
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        header("Location: view_feedback.php?deleted=1");
        exit;
    } else {
        echo "Error deleting record: " . $stmt->error;
    }

    $stmt->close();
}

$connection->close();
?>
