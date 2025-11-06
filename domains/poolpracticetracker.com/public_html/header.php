<?php
// File: header.php
// REUSABLE HEADER
// UPDATED to include session_start() and dynamic navigation links.
// UPDATED to pass redirect_url to login.php
// UPDATED to remove "Dashboard" link for logged-in users

// Start the session at the very top of the script, before any HTML output.
// Using @ to suppress "session already started" warnings if it's called elsewhere.
@session_start();

// Define a default title and allow overriding on pages that include this header
$page_title = isset($page_title) ? $page_title : 'Home';

// --- NEW ---
// Get the current page's URI to pass to the login page
$current_page_uri = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PoolPracticeTracker.com | <?php echo htmlspecialchars($page_title); ?></title>
    <!-- Load Tailwind CSS via CDN for base utility and responsive scaffolding -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Load the main external stylesheet -->
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

<!-- START OF REUSABLE HEADER -->
<header class="main-header">
    <div class="header-container">
        <div class="flex-shrink-0">
            <!-- Left Section: Logo (Clickable to Home) -->
            <a href="index.php" class="header-logo">
                <span class="logo-icon" role="img" aria-label="Billiards Eight Ball">🎱</span>
                <span class="logo-text">Tracker</span>
            </a>
        </div>

        <!-- Center Section: Navigation Links (NOW DYNAMIC) -->
        <nav class="desktop-nav">
            <a href="index.php" class="nav-link">Home</a>
            
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) : ?>
                <!-- Links for LOGGED-IN users -->
                <a href="drills.php" class="nav-link">Drills</a>
                <a href="my-account.php" class="nav-link">My Account</a> <!-- Changed from Dashboard -->
                <a href="logout.php" class="nav-link">Logout</a>
            <?php else : ?>
                <!-- Links for GUESTS -->
                <a href="drills.php" class="nav-link">Drills</a>
                <a href="contact.php" class="nav-link">Contact Us</a>
                <a href="login.php?redirect_url=<?php echo urlencode($current_page_uri); ?>" class="nav-link">Login</a>
            <?php endif; ?>
            
        </nav>

        <!-- Right Section: Social Media Icons -->
        <div class="social-icons-group">
            <!-- Facebook Icon Placeholder -->
            <a href="#" target="_blank" aria-label="Facebook" class="social-icon-link facebook">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14 11.5a2.5 2.5 0 1 0 5 0 2.5 2.5 0 0 0-5 0zm1-5.5a1 1 0 1 0 2 0 1 1 0 0 0-2 0zM12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6.942 16.028c-.144.174-.188.423-.083.639.26.54.436 1.157.518 1.83.082.673-.016 1.39-.333 1.944a.846.846 0 0 1-.776.452h-9.988a.846.846 0 0 1-.776-.452c-.317-.554-.415-1.271-.333-1.944.082-.673.258-1.29.518-1.83.105-.216.061-.465-.083-.639-.303-.365-.77-.525-1.298-.485-1.076.082-1.936-.888-1.936-2.029v-5.278c0-1.141.86-2.111 1.936-2.029.528.04.995-.12 1.298-.485.144-.174.188.423.083-.639-.26-.54-.436-1.157-.518-1.83-.082-.673.016-1.39.333-1.944a.846.846 0 0 1 .776-.452h10.038a.846.846 0 0 1 .776.452c.317.554.415 1.271.333 1.944-.082.673-.258 1.29-.518 1.83-.105-.216-.061.465.083.639.303.365.77.525 1.298-.485 1.076-.082 1.936.888 1.936 2.029v5.278c0 1.141-.86 2.111-1.936 2.029-.528.04-.995-.12-1.298-.485z"/></svg>
            </a>
            <!-- YouTube Icon Placeholder -->
            <a href="#" target="_blank" aria-label="YouTube" class="social-icon-link youtube">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.213-7.254-.316-10.925.01-3.667.327-7.26.852-10.82 1.625-.19.043-.306.23-.277.419 2.008 1.488 4.053 2.936 6.136 4.34 2.078 1.402 4.195 2.768 6.353 4.1.189.117.43.083.585-.084 1.765-1.895 3.518-3.79 5.253-5.698 1.735-1.91 3.447-3.834 5.127-5.772.036-.04.055-.09.055-.145.006-.057-.015-.116-.05-.165-.13-.195-.316-.29-.533-.312zM12.002 11.233c-2.455-1.61-4.908-3.22-7.362-4.831-.383-.248-.87-.247-1.253.003-1.674 1.096-3.35 2.193-5.023 3.287-.384.25-.386.652-.002.902 2.45 1.608 4.904 3.218 7.356 4.829 2.452 1.611 4.904 3.221 7.355 4.832.383.25.87.248 1.253-.002 1.674-1.096 3.349-2.193 5.023-3.287.384-.25.385-.653.002-.903-2.45-1.609-4.905-3.218-7.356-4.829z"/></svg>
            </a>
        </div>

        <!-- Mobile Menu Button (hidden on desktop) -->
        <div class="md:hidden flex items-center">
            <!-- Hamburger menu icon placeholder -->
            <!-- Note: You would add JavaScript here to toggle the mobile menu -->
            <button class="text-white hover:text-primary p-2 focus:outline-none rounded-full">
                <svg class="icon w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation (hidden by default) -->
    <div class="md:hidden bg-secondary border-t border-gray-700 hidden" id="mobile-menu">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <!-- Note: Retained Tailwind classes here for basic mobile menu structure -->
            <a href="index.php" class="text-white hover:bg-gray-700 block px-3 py-2 rounded-md text-base font-medium">Home</a>
            
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) : ?>
                <!-- Links for LOGGED-IN users -->
                <a href="drills.php" class="text-white hover:bg-gray-700 block px-3 py-2 rounded-md text-base font-medium">Drills</a>
                <a href="my-account.php" class="text-white hover:bg-gray-700 block px-3 py-2 rounded-md text-base font-medium">My Account</a> <!-- Changed from Dashboard -->
                <a href="logout.php" class="text-white hover:bg-gray-700 block px-3 py-2 rounded-md text-base font-medium">Logout</a>
            <?php else : ?>
                <!-- Links for GUESTS -->
                <a href="drills.php" class="text-white hover:bg-gray-700 block px-3 py-2 rounded-md text-base font-medium">Drills</a>
                <a href="contact.php" class="text-white hover:bg-gray-700 block px-3 py-2 rounded-md text-base font-medium">Contact Us</a>
                <a href="login.php?redirect_url=<?php echo urlencode($current_page_uri); ?>" class="text-white hover:bg-gray-700 block px-3 py-2 rounded-md text-base font-medium">Login</a>
            <?php endif; ?>

        </div>
    </div>
</header>
<!-- END OF REUSABLE HEADER -->