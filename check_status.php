<?php
include('db_conn.php');

// Get the room ID from the URL
$room = isset($_GET['room']) ? $_GET['room'] : '';

if ($room != '') {
    $stmt = $connection->prepare("SELECT status FROM appointments WHERE room_id = ?");
    $stmt->bind_param("s", $room);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    
    // Send the status back to the JavaScript (e.g., "approved" or "completed")
    echo strtolower($row['status'] ?? 'pending');
}
?>