<?php
session_start();

// Check if OTP session exists
if (!isset($_SESSION['otp_purpose'], $_SESSION['otp_email'])) {
    header('Location: login.php');
    exit;
}

// For signup AND login, we need pending_user_id
if (in_array($_SESSION['otp_purpose'], ['signup', 'login']) && !isset($_SESSION['pending_user_id'])) {
    header('Location: login.php');
    exit;
}

// For send_money, account_settings we need user_id (already logged in)
if (in_array($_SESSION['otp_purpose'], ['send_money', 'account_settings']) && !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$email = $_SESSION['otp_email'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-[Inter]">

<div class="max-w-xl mx-auto mt-16 bg-white p-8 rounded-2xl shadow-xl">

    <h1 class="text-2xl font-bold mb-4">Verify Your OTP</h1>

    <p class="text-gray-600 mb-4">
        Code sent to <strong><?php echo htmlspecialchars($email) ?></strong><br>
        Expires in <span id="timer" class="text-red-600 font-bold">2:00</span>
    </p>

    <?php if (!empty($_GET['error'])): ?>
        <div class="p-3 bg-red-100 text-red-700 rounded mb-4">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <?php 
    // Route to correct verification based on purpose
    $purpose = $_SESSION['otp_purpose'] ?? 'signup';
    
    if ($purpose === 'login') {
        $action = 'login_otp_verify.php';
    } elseif ($purpose === 'send_money') {
        $action = 'send_money_otp_verify.php';
    } elseif ($purpose === 'account_settings') {
        $action = 'account_settings_otp_verify.php';
    } else {
        $action = 'otp_verify.php';
    }
    ?>
    <form method="POST" action="<?= $action ?>" class="space-y-4">
        <input maxlength="6" name="otp"
               class="w-full text-center text-xl p-3 border rounded-xl"
               placeholder="Enter 6-digit code" required>

        <button class="w-full bg-blue-600 text-white p-3 rounded-xl font-semibold">
            Verify
        </button>
    </form>
</div>

<script>
let sec = 120;
const t = document.getElementById("timer");

setInterval(() => {
    sec--;
    if (sec <= 0) {
        t.textContent = "Expired";
        return;
    }
    t.textContent = Math.floor(sec/60) + ":" + String(sec%60).padStart(2, "0");
}, 1000);
</script>

</body>
</html>
