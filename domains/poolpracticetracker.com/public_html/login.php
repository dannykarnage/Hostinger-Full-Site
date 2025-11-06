<?php
// File: login.php
// Handles user login form and authentication.
// UPDATED to handle redirect_url and default to index.php

// --- IMPORTANT SETUP ---
// This script assumes you have a 'users' table in your database with at least
// two columns: 'username' (or 'email') and 'password_hash'.
//
// The 'password_hash' column MUST store passwords that were hashed using
// password_hash() in PHP.
//
// Example table structure:
// CREATE TABLE users (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     username VARCHAR(100) NOT NULL UNIQUE,
//     email VARCHAR(255) NOT NULL UNIQUE,
//     password_hash VARCHAR(255) NOT NULL,
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
// );
//
// To create a hashed password for testing:
// $hashed_password = password_hash('your_password_here', PASSWORD_DEFAULT);
// echo $hashed_password; // Insert this hash into your database
// -----------------------


// Start the session at the very top, before any output.
// (header.php will also start one, but it's good practice here too)
session_start();

// Include the database connection
require 'db_connect.php';

$login_error = ''; // Variable to hold login error messages

// --- NEW: Handle Redirect URL ---
// Get the redirect URL from GET, or default to homepage
$redirect_url = 'index.php'; // Default redirect location
if (isset($_GET['redirect_url']) && !empty($_GET['redirect_url'])) {
    // Only set if it's not empty and not the login page itself
    if ($_GET['redirect_url'] !== '/login.php') {
         $redirect_url = $_GET['redirect_url'];
    }
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Get and sanitize form data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // --- NEW: Get redirect_url from the hidden form field ---
    $redirect_url_from_post = trim($_POST['redirect_url']);

    // 2. Validate data
    if (empty($username) || empty($password)) {
        $login_error = 'Please enter both username and password.';
    } else {
        try {
            // 3. Prepare and execute the database query
            // We fetch by 'username' OR 'email' to allow users to log in with either
            $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            
            // 4. Fetch the user
            $user = $stmt->fetch();

            // 5. Verify the user and password
            // password_verify() securely checks the submitted password against the stored hash
            if ($user && password_verify($password, $user['password_hash'])) {
                
                // 6. Login successful: Store user data in session
                // Regenerate session ID for security
                session_regenerate_id(true); 
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['logged_in'] = true;

                // 7. Redirect to the original page or homepage
                // --- UPDATED REDIRECT LOGIC ---
                $redirect_to = 'index.php'; // Default
                if (!empty($redirect_url_from_post)) {
                    // Basic security check: only redirect to pages on this site
                    // This prevents "Open Redirect" vulnerabilities
                    $host = parse_url($redirect_url_from_post, PHP_URL_HOST);
                    if (!$host || $host === $_SERVER['HTTP_HOST']) {
                         // Don't redirect back to login.php
                         if ($redirect_url_from_post !== '/login.php') {
                            $redirect_to = $redirect_url_from_post;
                         }
                    }
                }
                
                header("Location: " . $redirect_to);
                exit; // Stop script execution after redirect
                
            } else {
                // 8. Login failed
                $login_error = 'Invalid username or password.';
            }

        } catch (PDOException $e) {
            // Database error
            $login_error = 'A database error occurred. Please try again later.';
            // You should log $e->getMessage() for debugging, not show it to the user
        }
    }
}

// Set the page title before including the header
$page_title = 'Login';
// Include the standard header for the site
include 'header.php';
?>

<!-- Main Content for the Login Page -->
<main class="main-container main-content">

    <!-- Login Form Card -->
    <section class="login-card-container">
        <div class="login-card">
            <h1 class="card-title">Member Login</h1>
            <p class="card-text">
                Access your practice dashboard to log scores and track your progress.
            </p>

            <?php
            // Display login error message if it exists
            if (!empty($login_error)) {
                echo '<div class="login-error">' . htmlspecialchars($login_error) . '</div>';
            }
            
            // --- NEW: Show a message if user was logged out ---
            if (isset($_GET['status']) && $_GET['status'] === 'loggedout') {
                echo '<div class="login-success">You have been successfully logged out.</div>';
            }
            ?>

            <!-- Login Form -->
            <form action="login.php" method="POST" class="contact-form">
                
                <!-- NEW: Hidden field to store the redirect URL -->
                <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url); ?>">

                <!-- Form Group: Username/Email -->
                <div class="form-group">
                    <label for="username" class="form-label">Username or Email</label>
                    <input type="text" id="username" name="username" class="form-input" placeholder="you@example.com" required>
                </div>

                <!-- Form Group: Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
                </div>

                <!-- Submit Button -->
                <div class="form-submit-group">
                    <button type="submit" class="primary-button">
                        Login
                    </button>
                </div>
                
                <p class="card-text" style="text-align: center; margin-top: 1rem; font-size: 0.875rem;">
                    Don't have an account? <a href="register.php" class="text-link">Sign up here</a>.
                    <!-- Note: register.php does not exist yet -->
                </p>

            </form>
        </div>
    </section>

</main>

<?php
// Include the standard footer for the site
include 'footer.php';
?>