<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set session idle timeout (2 minutes = 120 seconds)
$_SESSION['expire_after'] = 120;

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=" . urlencode("Please login first."));
    exit;
}

// Inactivity Timeout Check
if (isset($_SESSION['last_activity'])) {
    $inactive = time() - $_SESSION['last_activity'];
    $expire_after = $_SESSION['expire_after'] ?? 120; // Default to 2 minutes if not set

    if ($inactive > $expire_after) {
        // Destroy session
        session_unset();
        session_destroy();

        // Redirect to login with idle timeout message
        header("Location: login.php?timeout=idle");
        exit;
    }
}

// Absolute Session Timeout Check (5 minutes = 300 seconds)
$absoluteTimeout = 300;
if (isset($_SESSION['session_start_time']) && (time() - $_SESSION['session_start_time'] > $absoluteTimeout)) {
    // Log this security event
    if (function_exists('logActivity') && isset($_SESSION['user_id'])) {
        logActivity($_SESSION['user_id'], 'session_absolute_timeout', 'Session expired after 5 minutes', 'failed');
    }
    
    session_unset();
    session_destroy();
    // Redirect to login with absolute timeout message
    header("Location: login.php?timeout=abs");
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>
