<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('db_conn.php');

if (isset($_POST['submit'])) {
    // 1. Check if user is logged in
    if (!isset($_POST['user_email']) || empty($_POST['user_email'])) {
        die("Error: User ID is missing. Please log in again.");
    }

    // 2. Capture variables
    $date = $_POST['date'];
    $raw_time = $_POST['time']; // This is the 24h format from the input (e.g. 15:30)
    
    // 3. CONVERT TO AM/PM FOR DATABASE
    $formatted_time = date("h:i A", strtotime($raw_time)); // Converts 15:30 to 03:30 PM
    
    $purpose = $_POST['purpose'];
    $additional_info = $_POST['additional_info'];
    $user_id = $_POST['user_email']; 
    $advisor_id = $_POST['advisor_id'];

    // 4. Prepare Statement
    $stmt = $connection->prepare("INSERT INTO appointments (advisor_id, date, time, purpose, additional_info, user_id) 
                                 VALUES (?, ?, ?, ?, ?, ?)");

    // 5. Bind Parameters (Notice we use $formatted_time here)
    $stmt->bind_param("issssi", $advisor_id, $date, $formatted_time, $purpose, $additional_info, $user_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Appointment booked successfully!'); location.replace('fin_advisor.php');</script>";
    } else {
        echo "Database Error: " . $stmt->error;
    }

    $stmt->close();
}
?>