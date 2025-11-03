<?php
    // CRITICAL SECURITY WARNING:
    // Database credentials are hardcoded and stored in a web-accessible directory.
    // They MUST be moved to environment variables or a configuration file outside the web root.
    $dbhost = "localhost";
    $dbuser = "u449903691_eooaokt";
    $dbpass = "DKarnag3*ppt";
    $dbname = "u449903691_eooaokt";

    // Initialize $conn using Object-Oriented mysqli
    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

    // Check connection
    if ($conn->connect_error) {
        // Log the error detail internally, but only show a generic message to the user
        error_log("Connection failed: " . $conn->connect_error);
        die("Database connection failed. Please try again later.");
    }

    // Set character set to UTF-8 for security and compatibility
    $conn->set_charset("utf8mb4");
?>
