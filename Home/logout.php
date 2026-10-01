<?php
session_start();

// 1. Capture the user type before clearing the session so we know where to redirect
$user_type = isset($_SESSION['type']) ? $_SESSION['type'] : '';

// 2. Unset all session variables
$_SESSION = array();

// 3. Destroy the session cookie if it exists
if (ini_get("session_use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Finally, destroy the session
session_destroy();

// 5. Role-based redirect matching your sign-in logic
// Type 0 = Admin, Type 2 = Advisor, others = User
if ($user_type == 0) {
    header("Location: signin.php?role=admin");
} elseif ($user_type == 2) {
    header("Location: signin.php?role=advisor");
} else {
    // This covers the standard user or if the session was already empty
    header("Location: signin.php"); 
}
exit();
?>