<?php
date_default_timezone_set('Asia/Kolkata');
session_start();
include('db_conn.php');

// 1. Get Room ID and Fetch Appointment
$room = isset($_GET['room']) ? $_GET['room'] : '';
$stmt = $connection->prepare("SELECT * FROM appointments WHERE room_id = ?");
$stmt->bind_param("s", $room);
$stmt->execute();
$res = $stmt->get_result();
$app = $res->fetch_assoc();

if (!$app) { die("Room not found."); }

// 2. Setup Time Logic
$appTime = strtotime($app['date'] . ' ' . $app['time']);
$endTime = $appTime + 3600; 
$now = time();
$bufferTime = $appTime - (15 * 60); 

// 3. SECURITY CHECKS (Fixed invisible character issue)
$currentPayment = strtolower(trim($app['payment_status'] ?? ''));
$currentStatus  = strtolower(trim($app['status'] ?? '')); // Fixed variable naming

if ($currentPayment !== 'verified' && $currentPayment !== 'paid') {
    die("Access Denied: Payment not verified.");
}

// Ownership Check
if ($_SESSION['user_id'] != $app['user_id'] && $_SESSION['user_id'] != $app['advisor_id']) {
    die("Unauthorized access.");
}

// Time Window Check
if ($now < $bufferTime) {
    die("The session has not started yet. Returns at " . date("h:i A", $bufferTime));
}
if ($now > $endTime && $currentStatus !== 'completed') {
    die("This session has expired.");
}

// --- FIXED SESSION END HANDLER ---
if (isset($_POST['end_session'])) {
    // We update the status to 'completed'
    $updateStmt = $connection->prepare("UPDATE appointments SET status='completed' WHERE room_id = ?");
    $updateStmt->bind_param("s", $room);
    
    if ($updateStmt->execute()) {
        // Force refresh the current page to show "Session Closed"
        header("Location: chat_room.php?room=" . $room);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat Room - MoneyMorph</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        #chatWindow { height: 500px; overflow-y: auto; background-color: #e5ddd5; padding: 15px; display: flex; flex-direction: column; }
        .msg-wrapper { display: flex; width: 100%; margin-bottom: 10px; }
        .msg { max-width: 75%; padding: 8px 12px; font-size: 0.95rem; border-radius: 7.5px; box-shadow: 0 1px 0.5px rgba(0,0,0,0.13); }
        .sent { background-color: #dcf8c6; margin-left: auto; }
        .received { background-color: #ffffff; margin-right: auto; }
        .pulse { animation: blinker 1.5s linear infinite; }
        @keyframes blinker { 50% { opacity: 0; } }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-primary"><i class="fa fa-comments me-2"></i>Live Consultation</h5>
            <div class="d-flex align-items-center gap-2">
                <span id="timer" class="badge bg-info p-2">Initializing...</span>
                
                <?php if ($currentStatus !== 'completed' && $_SESSION['type'] == 2): ?>
                    <form method="POST">
                        <button type="submit" name="end_session" class="btn btn-sm btn-danger" onclick="return confirm('Change status to Completed and end session?');">
                            <i class="fa fa-times-circle"></i> End Session
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <div id="chatWindow"></div>
            
            <?php if ($currentStatus !== 'completed'): ?>
                <div class="input-group mt-3">
                    <input type="text" id="messageInput" class="form-control" placeholder="Type message...">
                    <button class="btn btn-primary" id="sendBtn"><i class="fa fa-paper-plane"></i> Send</button>
                </div>
            <?php else: ?>
                <div class="alert alert-warning mt-3 text-center">
                    <i class="fa fa-lock"></i> This session has been completed and closed.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script>
    const startTime = <?php echo $appTime; ?> * 1000;
    const endTime = <?php echo $endTime; ?> * 1000;
    const isCompleted = "<?php echo $currentStatus; ?>" === "completed";

    function updateTimer() {
        if(isCompleted) {
            $("#timer").text("Completed").removeClass('bg-info').addClass('bg-secondary');
            return;
        }
        const now = new Date().getTime();
        if (now < startTime) {
            $("#timer").text("Starts soon");
        } else if (now <= endTime) {
            const diff = endTime - now;
            const mins = Math.floor((diff % 3600000) / 60000);
            const secs = Math.floor((diff % 60000) / 1000);
            $("#timer").html("<i class='fa fa-circle text-white pulse'></i> Ends in: " + mins + "m " + secs + "s").addClass('bg-danger');
        } else {
            $("#timer").text("Expired").addClass('bg-secondary');
        }
    }
    setInterval(updateTimer, 1000);
    updateTimer();

    function loadMessages() {
        if(isCompleted) return;
        $('#chatWindow').load('load_messages.php?room=<?php echo $room; ?>', function() {
            var chat = $('#chatWindow');
            chat.scrollTop(chat[0].scrollHeight);
        });

        // Use this to auto-refresh for the client when advisor ends session
        $.get('check_status.php?room=<?php echo $room; ?>', function(status) {
            if(status.trim() === 'completed') { location.reload(); }
        });
    }
    setInterval(loadMessages, 3000);
    loadMessages();

    $('#sendBtn').click(function() {
        let msg = $('#messageInput').val().trim();
        if(msg != "") {
            $.post('send_message.php', { message: msg, room: '<?php echo $room; ?>' }, function() {
                $('#messageInput').val('');
                loadMessages(); 
            });
        }
    });
</script>
</body>
</html>