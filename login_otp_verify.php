<?php
session_start();
require 'db.php';
require 'device_fingerprint.php';
require 'location_check.php';
require 'activity_logger.php';

if (!isset($_SESSION['pending_user_id'], $_POST['otp'], $_SESSION['otp_purpose'])) {
    header("Location: login.php");
    exit;
}

// Only handle login OTP here
if ($_SESSION['otp_purpose'] !== 'login') {
    header("Location: otp_verify.php");
    exit;
}

$userId = (int)$_SESSION['pending_user_id'];
$code = trim($_POST['otp']);

function backWithError($msg) {
    header("Location: otp.php?error=" . urlencode($msg));
    exit;
}

try {
    // Get user email for logging
    $stmt = $pdo->prepare("SELECT Email FROM Users WHERE Id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        backWithError("User not found.");
    }

    /* -----------------------------------------------
       1) Validate OTP for login
    -----------------------------------------------*/
    $stmt = $pdo->prepare("
        SELECT `Id`, `ExpiresAt`, `IsUsed`
        FROM otps
        WHERE `UserId` = ?
          AND `Code` = ?
          AND `Purpose`='login'
        ORDER BY `CreatedAt` DESC
        LIMIT 1
    ");
    $stmt->execute([$userId, $code]);
    $otp = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$otp) {
        backWithError("Invalid OTP.");
    }
    
    if ($otp['IsUsed']) backWithError("OTP already used.");

    $now = new DateTime("now", new DateTimeZone("UTC"));
    $exp = new DateTime($otp['ExpiresAt'], new DateTimeZone("UTC"));

    if ($now > $exp) backWithError("OTP expired. Please login again.");

    /* -----------------------------------------------
       2) Mark OTP as used and reset failed attempts
    -----------------------------------------------*/
    $upd = $pdo->prepare("UPDATE otps SET `IsUsed`=1 WHERE `Id`=?");
    $upd->execute([$otp['Id']]);
    

    /* -----------------------------------------------
       3) ZERO TRUST: Trust device & log location
    -----------------------------------------------*/
    $deviceFingerprint = $_SESSION['device_fingerprint'] ?? getDeviceFingerprint();
    $ipAddress = $_SESSION['ip_address'] ?? getUserIP();
    
    // Trust this device for future logins
    trustDevice($pdo, $userId, $deviceFingerprint);
    
    // Log successful login
    logLoginAttempt($pdo, $userId, $ipAddress, true);

    /* -----------------------------------------------
       4) Start session with Zero Trust metadata
    -----------------------------------------------*/
    unset($_SESSION['pending_user_id'], $_SESSION['otp_email'], $_SESSION['otp_purpose']);

    $_SESSION['user_id'] = $userId;
    $_SESSION['last_activity'] = time();
    $_SESSION['session_start_time'] = time(); // Record session start time for absolute timeout
    $_SESSION['expire_after'] = 120; // 2 minutes idle timeout
    $_SESSION['device_fingerprint'] = $deviceFingerprint;
    $_SESSION['ip_address'] = $ipAddress;

    // Log successful login
    logActivity($userId, 'login_success', "Device: $deviceFingerprint", 'success');

    header("Location: dashboard.php");
    exit;

} catch (Throwable $e) {
    error_log("LOGIN OTP VERIFY ERR: " . $e->getMessage());
    backWithError("Unexpected error: " . $e->getMessage());
}
