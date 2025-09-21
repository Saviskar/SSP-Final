<?php
// Configuration file for Pet Haven

// Database Configuration
define('DB_CONFIG', [
    'host' => 'localhost',
    'dbname' => 'pethaven',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
]);

// Application Configuration
define('APP_CONFIG', [
    'name' => 'Pet Haven',
    'version' => '1.0.0',
    'timezone' => 'Asia/Colombo',
    'session_timeout' => 3600, // 1 hour
    'max_cart_items' => 50,
    'default_shipping_cost' => 5.00,
    'low_stock_threshold' => 10
]);

// Security Configuration
define('SECURITY_CONFIG', [
    'password_min_length' => 6,
    'session_name' => 'PETHAVEN_SESSION',
    'csrf_token_expire' => 1800, // 30 minutes
    'max_login_attempts' => 5,
    'lockout_duration' => 900 // 15 minutes
]);

// File Upload Configuration
define('UPLOAD_CONFIG', [
    'max_file_size'       => 5 * 1024 * 1024, // 5MB
    // allow modern formats you’re already using
    'allowed_extensions'  => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'], // [CHANGED]
    // absolute filesystem path to /assets/uploads (robust across include locations)
    'upload_path'         => dirname(__DIR__, 2) . '/assets/uploads',       // [CHANGED]
    'max_files_per_product' => 5
]);

// Email Configuration (for future use)
define('EMAIL_CONFIG', [
    'smtp_host' => '',
    'smtp_port' => 587,
    'smtp_username' => '',
    'smtp_password' => '',
    'from_email' => 'noreply@pethaven.com',
    'from_name' => 'Pet Haven'
]);

// Set timezone
date_default_timezone_set(APP_CONFIG['timezone']);

// Start session with custom configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.name', SECURITY_CONFIG['session_name']);
    ini_set('session.gc_maxlifetime', APP_CONFIG['session_timeout']);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}

// Helper functions
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN';
}

function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function redirectIfNotLoggedIn($redirect_url = '../public/login.html') {
    if (!isLoggedIn()) {
        header("Location: $redirect_url");
        exit();
    }
}

function redirectIfNotAdmin($redirect_url = '../public/login.html') {
    if (!isAdmin()) {
        header("Location: $redirect_url");
        exit();
    }
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    if (time() - $_SESSION['csrf_token_time'] > SECURITY_CONFIG['csrf_token_expire']) {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

function formatPrice($price) {
    return number_format($price, 2);
}

function calculateDiscountedPrice($originalPrice, $discountPercentage) {
    return $originalPrice * (1 - $discountPercentage / 100);
}

// Error reporting (disable in production)
if ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', '../logs/error.log');
}
?>