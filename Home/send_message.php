<?php
session_start();
include('db_conn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $room    = $_POST['room'];
    $sender  = $_SESSION['user_id'];
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $stmt = $connection->prepare("INSERT INTO messages (room_id, sender_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $room, $sender, $message);
        $stmt->execute();
    }
}
?>