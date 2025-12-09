<?php 
require 'auth.php';
require 'db.php';

// Fetch username from database if not in session
if (!isset($_SESSION['username']) && isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT Username FROM Users WHERE Id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['username'] = $user['Username'];
    } else {
        // Fallback if user not found (shouldn't normally happen)
        $_SESSION['username'] = 'User';
    }
}

// Calculate session timeout
if (isset($_SESSION['user_id'], $_SESSION['session_start_time'], $_SESSION['last_activity'])) {
    $absoluteTimeout = 300; // 5 minutes
    $idleTimeout = 120;     // 2 minutes

    // Calculate Remaining Absolute Time
    $timeElapsedAbsolute = time() - $_SESSION['session_start_time'];
    $absoluteRemaining = max(0, $absoluteTimeout - $timeElapsedAbsolute);

    // Calculate Remaining Idle Time
    $timeElapsedIdle = time() - $_SESSION['last_activity'];
    $idleRemaining = max(0, $idleTimeout - $timeElapsedIdle);

    // Session expires at the sooner of the two limits
    $initialTimeRemaining = min($absoluteRemaining, $idleRemaining);
} else {
    $initialTimeRemaining = 0; // Should not happen if auth.php runs correctly
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Secure Wallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background:#f3f4f6; }
        .container { max-width:480px; margin:auto; }
        .btn-primary { background:#3b82f6; color:white; padding:.75rem 1rem; border-radius:.75rem; }
        .btn-primary:hover { background:#2563eb; }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center">

<div class="container bg-white p-8 rounded-2xl shadow-xl">
    <h2 class="text-2xl font-bold mb-2 text-gray-800">Welcome, <span class="text-blue-600"><?php echo htmlspecialchars($_SESSION['username']); ?></span></h2>
    <div class="text-sm font-medium text-red-600 mb-4">
        Session expires in: <span id="sessionTimer">--:--</span>
    </div>
    <p class="text-sm text-gray-500 mb-6">Your secure digital wallet dashboard.</p>

    <div class="space-y-4">
        <a href="send_money.php" class="block text-center btn-primary">Send Money</a>
        <a href="account_settings.php" class="block text-center p-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl font-semibold">Account Settings</a>
        <a href="logout.php" class="block text-center p-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl font-semibold">Log Out</a>
    </div>
</div>

<script>
let timeRemaining = <?= $initialTimeRemaining ?>;
const timerElement = document.getElementById('sessionTimer');

function updateTimer() {
    if (timeRemaining <= 0) {
        timerElement.textContent = "Expired";
        // Redirect to login page on session expiration
        window.location.href = 'login.php?timeout=1';
        return;
    }
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    timerElement.textContent = `${minutes}:${String(seconds).padStart(2, '0')}`;
    timeRemaining--;
}

// Call immediately and set interval
updateTimer();
const timerInterval = setInterval(updateTimer, 1000);

// Clear interval when page is unloaded to prevent memory leaks
window.addEventListener('beforeunload', () => {
    clearInterval(timerInterval);
});
</script>
</body>
</html>
