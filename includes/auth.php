<?php
session_start();
require_once '../config/database.php';

class Auth {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function register($username, $full_name, $email, $password, $bitcoin_addr, $usdt_addr, $litecoin_addr) {
        try {
            // Check if username or email already exists
            $check_query = "SELECT id FROM users WHERE username = :username OR email = :email";
            $check_stmt = $this->conn->prepare($check_query);
            $check_stmt->bindParam(':username', $username);
            $check_stmt->bindParam(':email', $email);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Username or email already exists'];
            }
            
            // Generate verification token
            $verification_token = bin2hex(random_bytes(32));
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $query = "INSERT INTO users (username, full_name, email, password, bitcoin_address, usdt_address, litecoin_address, verification_token) 
                     VALUES (:username, :full_name, :email, :password, :bitcoin_addr, :usdt_addr, :litecoin_addr, :verification_token)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':bitcoin_addr', $bitcoin_addr);
            $stmt->bindParam(':usdt_addr', $usdt_addr);
            $stmt->bindParam(':litecoin_addr', $litecoin_addr);
            $stmt->bindParam(':verification_token', $verification_token);
            
            if ($stmt->execute()) {
                $user_id = $this->conn->lastInsertId();
                
                // Create initial balance record
                $balance_query = "INSERT INTO user_balances (user_id) VALUES (:user_id)";
                $balance_stmt = $this->conn->prepare($balance_query);
                $balance_stmt->bindParam(':user_id', $user_id);
                $balance_stmt->execute();
                
                // Send verification email
                $this->sendVerificationEmail($email, $verification_token);
                
                return ['success' => true, 'message' => 'Registration successful! Please check your email to verify your account.'];
            }
            
            return ['success' => false, 'message' => 'Registration failed'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function login($username, $password) {
        try {
            $query = "SELECT id, username, full_name, email, password, is_verified FROM users WHERE username = :username OR email = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$user['is_verified']) {
                    return ['success' => false, 'message' => 'Please verify your email before logging in'];
                }
                
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['email'] = $user['email'];
                    
                    return ['success' => true, 'message' => 'Login successful'];
                }
            }
            
            return ['success' => false, 'message' => 'Invalid username or password'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function logout() {
        session_destroy();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    private function sendVerificationEmail($email, $token) {
        $subject = "Verify your account - Crypto Trading Platform";
        $verification_url = "http://localhost/verify.php?token=" . $token;
        $message = "
        <html>
        <head>
            <title>Account Verification</title>
        </head>
        <body>
            <h2>Welcome to Crypto Trading Platform!</h2>
            <p>Thank you for registering. Please click the link below to verify your account:</p>
            <p><a href='{$verification_url}'>Verify Account</a></p>
            <p>If you cannot click the link, copy and paste this URL into your browser:</p>
            <p>{$verification_url}</p>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: noreply@cryptotrading.com' . "\r\n";
        
        mail($email, $subject, $message, $headers);
    }
    
    public function verifyEmail($token) {
        try {
            $query = "UPDATE users SET is_verified = 1, verification_token = NULL WHERE verification_token = :token";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':token', $token);
            
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Email verified successfully! You can now login.'];
            }
            
            return ['success' => false, 'message' => 'Invalid verification token'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
?>