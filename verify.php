<?php
require_once 'includes/auth.php';

$auth = new Auth();
$message = '';
$success = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $result = $auth->verifyEmail($token);
    $message = $result['message'];
    $success = $result['success'];
} else {
    $message = 'Invalid verification link';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Crypto Trading Platform</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>CryptoTrade</h1>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <div class="switch-form">
                <p><a href="index.php">Click here to login</a></p>
            </div>
        <?php else: ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($message); ?></div>
            <div class="switch-form">
                <p><a href="register.php">Register again</a> | <a href="index.php">Back to login</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>