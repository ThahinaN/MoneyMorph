<?php
session_start();
include('db_conn.php');

$room = isset($_GET['room']) ? $_GET['room'] : '';
$my_id = $_SESSION['user_id']; // The ID of the person currently viewing the page

$stmt = $connection->prepare("SELECT * FROM messages WHERE room_id = ? ORDER BY created_at ASC");
$stmt->bind_param("s", $room);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // If the sender of this specific message is the person currently logged in
        $isMe = ($row['sender_id'] == $my_id);
        
        // WhatsApp Style: 'sent' (Right) for me, 'received' (Left) for the other person
        $class = $isMe ? 'sent' : 'received';
        
        echo '<div class="msg-wrapper">'; // Wrapper helps with alignment
            echo '<div class="msg ' . $class . '">';
                echo htmlspecialchars($row['message']);
                echo '<span class="timestamp">' . date('h:i A', strtotime($row['created_at'])) . '</span>';
            echo '</div>';
        echo '</div>';
    }
}
?>