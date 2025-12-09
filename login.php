<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZeroTrustBank - Login</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .app-container {
            max-width: 480px;
            margin: 5vh auto;
            background: white;
            padding: 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="app-container">
    <header class="text-center mb-8">
        <div class="header-logo text-blue-600 text-3xl font-bold">ZeroTrustBank</div>
        <p class="text-gray-500 text-sm">Secure Login</p>
    </header>

    <!-- ERROR / INFO BOX -->
    <div id="messageBox" class="hidden p-3 rounded-xl mb-4"></div>

    <!-- LOGIN FORM -->
    <form id="loginForm" class="space-y-4" method="POST" action="login_action.php">
        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="loginEmail"
                   class="w-full p-3 border rounded-xl" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password" id="loginPassword"
                   class="w-full p-3 border rounded-xl" required>
        </div>

        <button class="w-full bg-blue-600 text-white p-3 rounded-xl font-semibold">
            Log In
        </button>
    </form>

    <p class="text-center text-sm mt-4">
        Don't have an account?
        <a href="signup.php" class="text-blue-600 font-medium">Create one</a>
    </p>
</div>

<?php
// Handle timeout messages
$timeout_message = '';
$timeout_type = $_GET['timeout'] ?? '';

if ($timeout_type === 'idle') {
    $timeout_message = 'Session expired due to 2 minutes of inactivity. Please log in again.';
} elseif ($timeout_type === 'abs') {
    $timeout_message = 'Session expired due to 5-minute absolute time limit. Please log in again.';
}

// Clear the timeout parameter from URL
if (!empty($timeout_type)) {
    echo "<script>
    // Remove timeout parameter from URL without refreshing
    const url = new URL(window.location);
    url.searchParams.delete('timeout');
    window.history.replaceState({}, '', url);
    </script>";
}
?>

<!-- MESSAGE HANDLER -->
<script>
const params = new URLSearchParams(window.location.search);
const box = document.getElementById('messageBox');

// Define error messages
const errorMessages = {
    'default': 'An error occurred. Please try again.'
};

if (params.get('error')) {
    const errorCode = params.get('error');
    let message = errorMessages[errorCode] || params.get('error');
    
    // If it's a custom error message, use it directly
    if (!errorMessages[errorCode]) {
        message = params.get('error');
    }
    
    box.textContent = message;
    
    // Set appropriate styling based on error type
    if (errorCode === 'account_temp_locked') {
        box.className = "p-3 rounded-xl mb-4 text-yellow-700 bg-yellow-100";
    } else if (errorCode === 'account_perm_locked') {
        box.className = "p-3 rounded-xl mb-4 text-red-700 bg-red-100";
    } else {
        box.className = "p-3 rounded-xl mb-4 text-red-700 bg-red-100";
    }
    
    box.classList.remove("hidden");
}
</script>

<?php if (!empty($timeout_message)): ?>
    <script>
        // Display the timeout message
        const box = document.getElementById('messageBox');
        box.textContent = '<?= addslashes($timeout_message) ?>';
        box.className = "p-3 rounded-xl mb-4 text-yellow-700 bg-yellow-100";
        box.classList.remove("hidden");
    </script>
<?php endif; ?>

</body>
</html>
