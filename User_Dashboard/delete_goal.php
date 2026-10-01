<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();
include('db_conn.php'); 

// 1. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['type'] != 1) {
    header("Location: ../Home/signin.php");
    exit;
}

$userid = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    // Get the goal ID from the URL
    $goal_id = $_GET['id'];

    // SQL query to delete the goal
    $sql = "DELETE FROM goals WHERE id = ? AND user_id = ?";
    
    if ($stmt = $connection->prepare($sql)) {
        $stmt->bind_param("is", $goal_id, $userid);
        
        // Execute the query
        if ($stmt->execute()) {
            echo "<script>alert('Goal deleted successfully!');</script>";
            ?><script>location.replace("view_goal.php");</script><?php
        } else {
            echo "<script>alert('Error deleting goal. Please try again later.');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Error preparing the statement.');</script>";
    }
} else {
    echo "<script>alert('Goal ID not provided.');</script>";
}
$connection->close();
?>
