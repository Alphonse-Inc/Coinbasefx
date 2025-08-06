<?php
require_once 'includes/auth.php';

$auth = new Auth();

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error_message = '';
$success_message = '';
$token = '';
$valid_token = false;
$user_data = null;

// Check if token is provided and valid
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $token_result = $auth->verifyResetToken($token);
    
    if ($token_result['success']) {
        $valid_token = true;
        $user_data = $token_result['user'];
    } else {
        $error_message = $token_result['message'];
    }
} else {
    $error_message = 'Invalid reset link. Please request a new password reset.';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $valid_token) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $reset_token = $_POST['token'];
    
    if (empty($new_password) || empty($confirm_password)) {
        $error_message = 'Please fill in all fields';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'Passwords do not match';
    } elseif (strlen($new_password) < 6) {
        $error_message = 'Password must be at least 6 characters long';
    } else {
        $result = $auth->resetPassword($reset_token, $new_password);
        
        if ($result['success']) {
            $success_message = $result['message'];
            $valid_token = false; // Hide form after successful reset
        } else {
            $error_message = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Crypto Trading Platform</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>CryptoTrade</h1>
        </div>
        
        <?php if ($valid_token): ?>
            <h2 style="text-align: center; margin-bottom: 20px; color: #333;">Set New Password</h2>
            <p style="text-align: center; margin-bottom: 30px; color: #666;">Hi <?php echo htmlspecialchars($user_data['full_name']); ?>! Enter your new password below.</p>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <div class="switch-form">
                <p><a href="index.php">Click here to login with your new password</a></p>
            </div>
        <?php elseif ($valid_token): ?>
            <form method="POST" action="">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required placeholder="Enter new password (min. 6 characters)">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your new password">
                </div>
                
                <button type="submit" class="btn">Reset Password</button>
            </form>
            
            <div class="switch-form">
                <p>Remember your password? <a href="index.php">Back to Login</a></p>
            </div>
        <?php else: ?>
            <div class="switch-form">
                <p><a href="forgot_password.php">Request a new password reset</a></p>
                <p><a href="index.php">Back to Login</a></p>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
        
        document.getElementById('new_password').addEventListener('input', function() {
            const confirmPassword = document.getElementById('confirm_password');
            if (confirmPassword.value !== '') {
                if (this.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Passwords do not match');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }
        });
    </script>
</body>
</html>