<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/trading.php';

$auth = new Auth();
$trading = new Trading();

// Redirect if not logged in
if (!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];

$error_message = '';
$success_message = '';

// Handle logout
if (isset($_GET['logout'])) {
    $auth->logout();
    header('Location: index.php');
    exit;
}

// Handle trading actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action == 'buy' || $action == 'sell') {
            $coin_type = $_POST['coin_type'];
            $amount = floatval($_POST['amount']);
            $prices = $trading->getCoinPrices();
            $price_per_coin = $prices[$coin_type];
            
            if ($amount <= 0) {
                $error_message = 'Please enter a valid amount';
            } else {
                if ($action == 'buy') {
                    $result = $trading->buyCoin($user_id, $coin_type, $amount, $price_per_coin);
                } else {
                    $result = $trading->sellCoin($user_id, $coin_type, $amount, $price_per_coin);
                }
                
                if ($result['success']) {
                    $success_message = $result['message'];
                } else {
                    $error_message = $result['message'];
                }
            }
        } elseif ($action == 'add_funds') {
            $amount = floatval($_POST['fund_amount']);
            
            if ($amount <= 0) {
                $error_message = 'Please enter a valid amount';
            } else {
                $result = $trading->addFunds($user_id, $amount);
                
                if ($result['success']) {
                    $success_message = $result['message'];
                } else {
                    $error_message = $result['message'];
                }
            }
        }
    }
}

// Get user balances and transaction history
$balances = $trading->getUserBalances($user_id);
$transactions = $trading->getTransactionHistory($user_id, 20);
$coin_prices = $trading->getCoinPrices();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Crypto Trading Platform</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="user-info">
                <h2>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h2>
                <p>@<?php echo htmlspecialchars($username); ?></p>
            </div>
            <a href="?logout=1" class="logout-btn">Logout</a>
        </div>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <div class="dashboard-grid">
            <!-- Portfolio Balance -->
            <div class="card">
                <h3>Portfolio Balance</h3>
                <?php if ($balances): ?>
                    <div class="balance-item">
                        <span class="coin-name">USD Balance</span>
                        <span class="coin-balance">$<?php echo number_format($balances['usd_balance'], 2); ?></span>
                    </div>
                    <div class="balance-item">
                        <span class="coin-name">Bitcoin (BTC)</span>
                        <span class="coin-balance"><?php echo number_format($balances['bitcoin_balance'], 8); ?> BTC</span>
                    </div>
                    <div class="balance-item">
                        <span class="coin-name">Tether (USDT)</span>
                        <span class="coin-balance"><?php echo number_format($balances['usdt_balance'], 8); ?> USDT</span>
                    </div>
                    <div class="balance-item">
                        <span class="coin-name">Litecoin (LTC)</span>
                        <span class="coin-balance"><?php echo number_format($balances['litecoin_balance'], 8); ?> LTC</span>
                    </div>
                <?php else: ?>
                    <p>Unable to load balance information.</p>
                <?php endif; ?>
            </div>
            
            <!-- Current Prices -->
            <div class="card">
                <h3>Current Prices</h3>
                <div class="balance-item">
                    <span class="coin-name">Bitcoin (BTC)</span>
                    <span class="coin-balance">$<?php echo number_format($coin_prices['bitcoin'], 2); ?></span>
                </div>
                <div class="balance-item">
                    <span class="coin-name">Tether (USDT)</span>
                    <span class="coin-balance">$<?php echo number_format($coin_prices['usdt'], 2); ?></span>
                </div>
                <div class="balance-item">
                    <span class="coin-name">Litecoin (LTC)</span>
                    <span class="coin-balance">$<?php echo number_format($coin_prices['litecoin'], 2); ?></span>
                </div>
            </div>
            
            <!-- Buy Cryptocurrency -->
            <div class="card">
                <h3>Buy Cryptocurrency</h3>
                <form method="POST" class="trade-form">
                    <input type="hidden" name="action" value="buy">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="buy_coin_type">Cryptocurrency</label>
                            <select id="buy_coin_type" name="coin_type" required>
                                <option value="bitcoin">Bitcoin (BTC)</option>
                                <option value="usdt">Tether (USDT)</option>
                                <option value="litecoin">Litecoin (LTC)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="buy_amount">Amount</label>
                            <input type="number" id="buy_amount" name="amount" step="0.00000001" min="0" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Buy</button>
                </form>
            </div>
            
            <!-- Sell Cryptocurrency -->
            <div class="card">
                <h3>Sell Cryptocurrency</h3>
                <form method="POST" class="trade-form">
                    <input type="hidden" name="action" value="sell">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="sell_coin_type">Cryptocurrency</label>
                            <select id="sell_coin_type" name="coin_type" required>
                                <option value="bitcoin">Bitcoin (BTC)</option>
                                <option value="usdt">Tether (USDT)</option>
                                <option value="litecoin">Litecoin (LTC)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sell_amount">Amount</label>
                            <input type="number" id="sell_amount" name="amount" step="0.00000001" min="0" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-secondary">Sell</button>
                </form>
            </div>
            
            <!-- Add Funds -->
            <div class="card">
                <h3>Add Funds</h3>
                <form method="POST" class="trade-form">
                    <input type="hidden" name="action" value="add_funds">
                    <div class="form-group">
                        <label for="fund_amount">Amount (USD)</label>
                        <input type="number" id="fund_amount" name="fund_amount" step="0.01" min="0" required>
                    </div>
                    <button type="submit" class="btn">Add Funds</button>
                </form>
            </div>
        </div>
        
        <!-- Transaction History -->
        <div class="card">
            <h3>Transaction History</h3>
            <?php if (count($transactions) > 0): ?>
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Coin</th>
                            <th>Amount</th>
                            <th>Price</th>
                            <th>Total Value</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?php echo date('M j, Y H:i', strtotime($transaction['created_at'])); ?></td>
                                <td><span class="type-<?php echo $transaction['transaction_type']; ?>"><?php echo ucfirst($transaction['transaction_type']); ?></span></td>
                                <td><?php echo strtoupper($transaction['coin_type']); ?></td>
                                <td><?php echo number_format($transaction['amount'], 8); ?></td>
                                <td>$<?php echo number_format($transaction['price_per_coin'], 2); ?></td>
                                <td>$<?php echo number_format($transaction['total_value'], 2); ?></td>
                                <td><span class="status-<?php echo $transaction['status']; ?>"><?php echo ucfirst($transaction['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No transactions yet. Start by buying some cryptocurrency!</p>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Auto-refresh prices every 30 seconds (in a real app, you'd use WebSocket or AJAX)
        setTimeout(function() {
            location.reload();
        }, 30000);
        
        // Calculate total cost/value when amount changes
        document.getElementById('buy_amount').addEventListener('input', function() {
            const coinType = document.getElementById('buy_coin_type').value;
            const amount = parseFloat(this.value) || 0;
            const prices = <?php echo json_encode($coin_prices); ?>;
            const total = amount * prices[coinType];
            
            // You can add a display element to show the total cost
            console.log('Total cost: $' + total.toFixed(2));
        });
        
        document.getElementById('sell_amount').addEventListener('input', function() {
            const coinType = document.getElementById('sell_coin_type').value;
            const amount = parseFloat(this.value) || 0;
            const prices = <?php echo json_encode($coin_prices); ?>;
            const total = amount * prices[coinType];
            
            // You can add a display element to show the total value
            console.log('Total value: $' + total.toFixed(2));
        });
    </script>
</body>
</html>