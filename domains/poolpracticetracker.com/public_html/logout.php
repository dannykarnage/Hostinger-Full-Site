<?php
// File: logout.php
// Destroys the user session and logs them out.
// UPDATED: Redirects to homepage (index.php) instead of login.php

// 1. Start the session
session_start();
// ... existing code ... -->
// 4. Finally, destroy the session
session_destroy();

// 5. Redirect to the login page
// You could add a query parameter to show a "logged out" message
header("Location: index.php"); // <-- UPDATED from login.php
exit;
?>