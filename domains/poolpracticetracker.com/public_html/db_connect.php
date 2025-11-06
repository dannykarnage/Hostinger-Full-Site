<?php
// File: db_connect.php
// Establishes a connection to the MySQL database using credentials from .htaccess

// Retrieve database credentials set in .htaccess
$db_host = getenv('DB_HOST');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$db_name = getenv('DB_NAME');

// Set DSN (Data Source Name)
$dsn = 'mysql:host=' . $db_host . ';dbname=' . $db_name . ';charset=utf8';

// Set PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
];

try {
    // Create a new PDO instance
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // Handle connection error
    // In a production environment, you might log this error instead of displaying it
    die("Database connection failed: " . $e->getMessage());
}

// The $pdo variable is now available for any script that includes this file.
?>