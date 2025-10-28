<?php
// Error reporting configuration
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Database configuration
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = ''; // keep empty for XAMPP
$DB_NAME = 'flight_booking';

// Database connection with error handling
try {
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    
    if ($mysqli->connect_errno) {
        throw new Exception('Database connection failed: ' . $mysqli->connect_error);
    }
    
    // Set charset to UTF-8
    $mysqli->set_charset('utf8mb4');
    
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    die('Database connection failed. Please try again later.');
}

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    // Configure session security
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    
    session_start();
}

// Check for session timeout (30 minutes)
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 1800) {
    session_unset();
    session_destroy();
    session_start();
}

/**
 * Enhanced sanitization function with validation
 */
function sanitize($s) {
    global $mysqli;
    if (is_null($s)) return null;
    return $mysqli->real_escape_string(trim($s));
}

/**
 * Log application events
 * TODO: Maybe use a proper logging library later
 */
function logEvent($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
    error_log($logMessage, 3, __DIR__ . '/app.log');
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

/**
 * Redirect with message
 */
function redirectWithMessage($url, $message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header("Location: $url");
    exit;
}

/**
 * Display and clear session messages
 */
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['message_type'] ?? 'info';
        $message = $_SESSION['message'];
        unset($_SESSION['message'], $_SESSION['message_type']);
        
        $alertClass = match($type) {
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            default => 'alert-info'
        };
        
        echo "<div class='alert $alertClass alert-dismissible fade show' role='alert'>";
        echo htmlspecialchars($message);
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
        echo "</div>";
    }
}
