<?php
session_start();
require 'db.php';
require 'email_config.php';
require 'device_fingerprint.php';
require 'location_check.php';
require 'activity_logger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

function backWithError(string $msg): void {
    $msg = urlencode($msg);
    header("Location: login.php?error={$msg}");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    backWithError('Invalid email.');
}

try {
    // Fetch user details
    $stmt = $pdo->prepare("SELECT Id, PasswordHash, IsVerified FROM Users WHERE Email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify password and account status
    if (!$user || empty($user['PasswordHash']) || !password_verify($password, $user['PasswordHash'])) {
        backWithError('Incorrect email or password.');
    }

    if (!$user['IsVerified']) {
        backWithError('Account not verified yet. Please sign up again.');
    }

    $userId = (int)$user['Id'];

    // ===== ZERO TRUST: Check device and location =====
    $deviceFingerprint = getDeviceFingerprint();
    $ipAddress = getUserIP();
    
    $isKnownDevice = isKnownDevice($pdo, $userId, $deviceFingerprint);
    $isKnownIP = isKnownLocation($pdo, $userId, $ipAddress);
    
    // Store for later verification
    $_SESSION['device_fingerprint'] = $deviceFingerprint;
    $_SESSION['ip_address'] = $ipAddress;
    $_SESSION['is_new_device'] = !$isKnownDevice;
    $_SESSION['is_new_location'] = !$isKnownIP;

    // Generate OTP for login (1 min)
    $otpCode   = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = (new DateTime('+1 minute'))->format('Y-m-d H:i:s');

    $stmt = $pdo->prepare("
        INSERT INTO otps (`UserId`, `Code`, `Purpose`, `ExpiresAt`, `IsUsed`, `CreatedAt`)
        VALUES (?, ?, 'login', ?, 0, NOW())
    ");
    $stmt->execute([$userId, $otpCode, $expiresAt]);

    if (!sendOtpEmail($email, $otpCode)) {
        backWithError('Could not send OTP email. Please try again.');
    }

    // Store all required session variables for OTP verification
    $_SESSION['pending_user_id'] = $userId;
    $_SESSION['otp_purpose']     = 'login';
    $_SESSION['otp_email']       = $email;
    
    // Ensure session is written before redirect
    session_write_close();

    // Log login attempt
    logActivity($userId, 'login_otp_sent', "Email: $email", 'pending');

    // Redirect to OTP entry page
    header('Location: otp.php');
    exit();

} catch (Throwable $e) {
    error_log('Login error: ' . $e->getMessage());
    backWithError('Unexpected error. Please try again.');
}
