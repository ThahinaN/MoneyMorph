<?php
session_start();
include 'db_conn.php';
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode([]);
    exit;
}

$user_email = $_SESSION['email']; 

// REMOVED 'AND due_date >= CURDATE()' so you can see your 2024 data
$sql = "SELECT description, amount, due_date FROM upcoming_alerts 
        WHERE user_email = ? 
        AND status = 'pending'
        ORDER BY due_date ASC";

$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $user_email); 
$stmt->execute();
$result = $stmt->get_result();

$alerts = [];
while ($row = $result->fetch_assoc()) {
    $alerts[] = $row;
}

echo json_encode($alerts);
?>