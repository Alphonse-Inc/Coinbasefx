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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $bitcoin_address = trim($_POST['bitcoin_address']);
    $usdt_address = trim($_POST['usdt_address']);
    $litecoin_address = trim($_POST['litecoin_address']);
    
    // Validation
    if (empty($username) || empty($full_name) || empty($email) || empty($password)) {
        $error_message = 'Please fill in all required fields';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address';
    } else {
        $result = $auth->register($username, $full_name, $email, $password, $bitcoin_address, $usdt_address, $litecoin_address);
        
        if ($result['success']) {
            $success_message = $result['message'];
            // Clear form data on success
            $_POST = array();
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
    <title>Register - Crypto Trading Platform</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>CryptoTrade</h1>
        </div>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" id="full_name" name="full_name" required value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <label for="bitcoin_address">Bitcoin Address</label>
                <input type="text" id="bitcoin_address" name="bitcoin_address" value="<?php echo isset($_POST['bitcoin_address']) ? htmlspecialchars($_POST['bitcoin_address']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="usdt_address">USDT Address</label>
                <input type="text" id="usdt_address" name="usdt_address" value="<?php echo isset($_POST['usdt_address']) ? htmlspecialchars($_POST['usdt_address']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="litecoin_address">Litecoin Address</label>
                <input type="text" id="litecoin_address" name="litecoin_address" value="<?php echo isset($_POST['litecoin_address']) ? htmlspecialchars($_POST['litecoin_address']) : ''; ?>">
            </div>
            
            <button type="submit" class="btn">Register</button>
        </form>
        
        <div class="switch-form">
            <p>Already have an account? <a href="index.php">Login here</a></p>
        </div>
    </div>
</body>
</html>