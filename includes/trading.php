<?php
require_once '../config/database.php';

class Trading {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function getUserBalances($user_id) {
        try {
            $query = "SELECT * FROM user_balances WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function getCoinPrices() {
        // Simulated coin prices - in a real application, you'd fetch from an API
        return [
            'bitcoin' => 45000.00,
            'usdt' => 1.00,
            'litecoin' => 150.00
        ];
    }
    
    public function buyCoin($user_id, $coin_type, $amount, $price_per_coin) {
        try {
            $this->conn->beginTransaction();
            
            $total_cost = $amount * $price_per_coin;
            
            // Check if user has enough USD balance
            $balances = $this->getUserBalances($user_id);
            if (!$balances || $balances['usd_balance'] < $total_cost) {
                $this->conn->rollback();
                return ['success' => false, 'message' => 'Insufficient USD balance'];
            }
            
            // Update balances
            $coin_balance_field = $coin_type . '_balance';
            $new_coin_balance = $balances[$coin_balance_field] + $amount;
            $new_usd_balance = $balances['usd_balance'] - $total_cost;
            
            $update_query = "UPDATE user_balances SET {$coin_balance_field} = :coin_balance, usd_balance = :usd_balance WHERE user_id = :user_id";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(':coin_balance', $new_coin_balance);
            $update_stmt->bindParam(':usd_balance', $new_usd_balance);
            $update_stmt->bindParam(':user_id', $user_id);
            $update_stmt->execute();
            
            // Record transaction
            $transaction_query = "INSERT INTO transactions (user_id, transaction_type, coin_type, amount, price_per_coin, total_value, status) 
                                 VALUES (:user_id, 'buy', :coin_type, :amount, :price_per_coin, :total_value, 'completed')";
            $transaction_stmt = $this->conn->prepare($transaction_query);
            $transaction_stmt->bindParam(':user_id', $user_id);
            $transaction_stmt->bindParam(':coin_type', $coin_type);
            $transaction_stmt->bindParam(':amount', $amount);
            $transaction_stmt->bindParam(':price_per_coin', $price_per_coin);
            $transaction_stmt->bindParam(':total_value', $total_cost);
            $transaction_stmt->execute();
            
            $this->conn->commit();
            return ['success' => true, 'message' => 'Purchase completed successfully'];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()];
        }
    }
    
    public function sellCoin($user_id, $coin_type, $amount, $price_per_coin) {
        try {
            $this->conn->beginTransaction();
            
            $total_value = $amount * $price_per_coin;
            
            // Check if user has enough coin balance
            $balances = $this->getUserBalances($user_id);
            $coin_balance_field = $coin_type . '_balance';
            
            if (!$balances || $balances[$coin_balance_field] < $amount) {
                $this->conn->rollback();
                return ['success' => false, 'message' => 'Insufficient ' . ucfirst($coin_type) . ' balance'];
            }
            
            // Update balances
            $new_coin_balance = $balances[$coin_balance_field] - $amount;
            $new_usd_balance = $balances['usd_balance'] + $total_value;
            
            $update_query = "UPDATE user_balances SET {$coin_balance_field} = :coin_balance, usd_balance = :usd_balance WHERE user_id = :user_id";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(':coin_balance', $new_coin_balance);
            $update_stmt->bindParam(':usd_balance', $new_usd_balance);
            $update_stmt->bindParam(':user_id', $user_id);
            $update_stmt->execute();
            
            // Record transaction
            $transaction_query = "INSERT INTO transactions (user_id, transaction_type, coin_type, amount, price_per_coin, total_value, status) 
                                 VALUES (:user_id, 'sell', :coin_type, :amount, :price_per_coin, :total_value, 'completed')";
            $transaction_stmt = $this->conn->prepare($transaction_query);
            $transaction_stmt->bindParam(':user_id', $user_id);
            $transaction_stmt->bindParam(':coin_type', $coin_type);
            $transaction_stmt->bindParam(':amount', $amount);
            $transaction_stmt->bindParam(':price_per_coin', $price_per_coin);
            $transaction_stmt->bindParam(':total_value', $total_value);
            $transaction_stmt->execute();
            
            $this->conn->commit();
            return ['success' => true, 'message' => 'Sale completed successfully'];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()];
        }
    }
    
    public function getTransactionHistory($user_id, $limit = 10) {
        try {
            $query = "SELECT * FROM transactions WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function addFunds($user_id, $amount) {
        try {
            $query = "UPDATE user_balances SET usd_balance = usd_balance + :amount WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':user_id', $user_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Funds added successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to add funds'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
?>