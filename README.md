# Crypto Trading Platform

A modern web application for cryptocurrency trading built with HTML5, CSS3, and PHP. Features user registration, email verification, secure login, and a complete trading dashboard.

## Features

- **User Authentication**
  - Registration with email verification
  - Secure login system
  - Password hashing
  - Session management
  - Forgot password with email reset

- **User Registration Fields**
  - Username
  - Full Name
  - Email
  - Password with confirmation
  - Bitcoin Address
  - USDT Address
  - Litecoin Address

- **Trading Dashboard**
  - Real-time portfolio balance
  - Current cryptocurrency prices
  - Buy/Sell functionality for Bitcoin, USDT, and Litecoin
  - Add funds feature
  - Transaction history
  - Responsive design

- **Security Features**
  - SQL injection protection with prepared statements
  - XSS protection with input sanitization
  - Secure password hashing
  - Email verification system

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- PHP extensions:
  - PDO MySQL
  - Mail functions

## Installation

1. **Clone or download the project files**
   ```bash
   git clone <repository-url>
   cd crypto-trading-platform
   ```

2. **Database Setup**
   
   **Option A: Setup with test data (recommended for testing)**
   ```bash
   mysql -u username -p < setup_with_test_data.sql
   ```
   
   **Option B: Setup without test data**
   ```bash
   mysql -u username -p database_name < database.sql
   # Optionally add test data later:
   mysql -u username -p database_name < test_data.sql
   ```

3. **Configure Database Connection**
   - Edit `config/database.php`
   - Update database credentials:
   ```php
   private $host = 'localhost';
   private $db_name = 'crypto_trading';
   private $username = 'your_username';
   private $password = 'your_password';
   ```

4. **Web Server Configuration**
   - Place files in your web server document root
   - Ensure PHP has write permissions for session handling
   - Configure email settings for verification emails

5. **Email Configuration**
   - Configure your server's mail function
   - Update the email settings in `includes/auth.php`
   - Modify the verification URL to match your domain

## File Structure

```
crypto-trading-platform/
├── config/
│   └── database.php          # Database configuration
├── includes/
│   ├── auth.php             # Authentication functions
│   └── trading.php          # Trading functionality
├── assets/
│   └── css/
│       └── style.css        # Modern CSS styling
├── index.php                # Login page
├── register.php             # Registration page
├── verify.php               # Email verification page
├── forgot_password.php      # Forgot password request page
├── reset_password.php       # Password reset page
├── dashboard.php            # Main trading dashboard
├── logout.php               # Logout handler
├── database.sql             # Database schema
├── database_update.sql      # Database update for existing installations
├── test_data.sql           # Test users and sample data
├── setup_with_test_data.sql # Complete setup with test data
└── README.md               # This file
```

## Usage

1. **Registration**
   - Navigate to `register.php`
   - Fill in all required fields
   - Check email for verification link
   - Click verification link to activate account

2. **Login**
   - Navigate to `index.php` 
   - Enter username/email and password
   - Successfully login redirects to dashboard

3. **Trading**
   - Add funds to your USD balance
   - Buy cryptocurrencies using USD balance
   - Sell cryptocurrencies to convert back to USD
   - View transaction history

4. **Password Recovery**
   - Navigate to `forgot_password.php` from login page
   - Enter your email address
   - Check email for reset link (expires in 1 hour)
   - Click link and set new password

## Test Users

The application includes test users for easy testing (if you used `setup_with_test_data.sql`):

| Username | Password | Email | Status | Balances |
|----------|----------|-------|--------|----------|
| `testuser` | `password` | testuser@example.com | ✅ Verified | $1,000 USD, 0.15 BTC, 500 USDT, 2.5 LTC |
| `admin` | `password` | admin@cryptotrading.com | ✅ Verified | $5,000 USD, 0.05 BTC, 1000 USDT, 5 LTC |
| `unverified` | `password` | unverified@example.com | ❌ Unverified | $0 USD, 0 BTC, 0 USDT, 0 LTC |

**Quick Start Testing:**
1. Login with `testuser` / `password`
2. Explore the dashboard with existing balances
3. Try buying/selling cryptocurrencies
4. View transaction history
5. Test forgot password feature

## Database Schema

### Users Table
- `id` - Primary key
- `username` - Unique username
- `full_name` - User's full name
- `email` - Unique email address
- `password` - Hashed password
- `bitcoin_address` - Bitcoin wallet address
- `usdt_address` - USDT wallet address
- `litecoin_address` - Litecoin wallet address
- `is_verified` - Email verification status
- `verification_token` - Email verification token
- `reset_token` - Password reset token
- `reset_token_expires` - Password reset token expiration
- `created_at` - Account creation timestamp

### User Balances Table
- `id` - Primary key
- `user_id` - Foreign key to users
- `bitcoin_balance` - Bitcoin balance
- `usdt_balance` - USDT balance
- `litecoin_balance` - Litecoin balance
- `usd_balance` - USD balance

### Transactions Table
- `id` - Primary key
- `user_id` - Foreign key to users
- `transaction_type` - Buy or sell
- `coin_type` - Cryptocurrency type
- `amount` - Amount of cryptocurrency
- `price_per_coin` - Price at transaction time
- `total_value` - Total transaction value
- `status` - Transaction status
- `created_at` - Transaction timestamp

## Security Considerations

1. **Production Deployment**
   - Change default database credentials
   - Use HTTPS for all pages
   - Configure proper email server
   - Set secure session parameters
   - Implement rate limiting
   - Add CSRF protection

2. **Email Verification**
   - Verification emails are sent automatically
   - Users must verify email before login
   - Verification tokens expire (implement if needed)

3. **Password Security**
   - Passwords are hashed using PHP's password_hash()
   - Minimum 6 character requirement
   - Consider implementing stronger password policies

## Customization

- **Styling**: Modify `assets/css/style.css` for custom styling
- **Coins**: Add more cryptocurrencies by updating the database schema and trading functions
- **Prices**: Currently uses simulated prices - integrate with real cryptocurrency APIs
- **Features**: Add more trading features like stop-loss orders, limit orders, etc.

## Troubleshooting

1. **Database Connection Issues**
   - Verify database credentials in `config/database.php`
   - Ensure MySQL server is running
   - Check database permissions

2. **Email Verification Not Working**
   - Configure PHP mail function
   - Check spam folder
   - Verify email server settings

3. **Login Issues**
   - Ensure account is email verified
   - Check username and password
   - Verify session configuration

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For support and questions, please create an issue in the project repository.
