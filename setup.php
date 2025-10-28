<?php
// Database setup script for Flight Booking System
// Run this once to initialize the database

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = ''; // keep empty for XAMPP
$DB_NAME = 'flight_booking';

echo "<h2>Flight Booking System - Database Setup</h2>";

// Connect to MySQL server (without database)
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
if ($mysqli->connect_errno) {
    die('Connection Error: ' . $mysqli->connect_error);
}

echo "<p>✅ Connected to MySQL server</p>";

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $DB_NAME";
if ($mysqli->query($sql)) {
    echo "<p>✅ Database '$DB_NAME' created/verified</p>";
} else {
    die('Error creating database: ' . $mysqli->error);
}

// Select database
$mysqli->select_db($DB_NAME);

// Read and execute SQL file
$sql_file = 'init.sql';
if (!file_exists($sql_file)) {
    die("Error: $sql_file not found");
}

$sql_content = file_get_contents($sql_file);
$sql_statements = explode(';', $sql_content);

$success_count = 0;
foreach ($sql_statements as $statement) {
    $statement = trim($statement);
    if (!empty($statement)) {
        if ($mysqli->query($statement)) {
            $success_count++;
        } else {
            echo "<p>⚠️ Warning: " . $mysqli->error . "</p>";
        }
    }
}

echo "<p>✅ Executed $success_count SQL statements</p>";

// Test connection with config.php
require_once 'config.php';

// Test if tables exist
$tables = ['users', 'flights', 'bookings'];
foreach ($tables as $table) {
    $result = $mysqli->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "<p>✅ Table '$table' exists</p>";
    } else {
        echo "<p>❌ Table '$table' missing</p>";
    }
}

// Test sample data
$users_count = $mysqli->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$flights_count = $mysqli->query("SELECT COUNT(*) as count FROM flights")->fetch_assoc()['count'];

echo "<p>✅ Users in database: $users_count</p>";
echo "<p>✅ Flights in database: $flights_count</p>";

echo "<h3>Setup Complete!</h3>";
echo "<p><strong>Default Admin Login:</strong></p>";
echo "<ul>";
echo "<li>Email: admin@example.com</li>";
echo "<li>Password: admin123</li>";
echo "</ul>";

echo "<p><strong>Sample User Logins:</strong></p>";
echo "<ul>";
echo "<li>Email: alice@example.com, Password: password123</li>";
echo "<li>Email: bob@example.com, Password: password123</li>";
echo "</ul>";

echo "<p><a href='index.php'>Go to Flight Booking System</a></p>";
?>

