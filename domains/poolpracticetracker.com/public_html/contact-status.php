<?php
// Set the page title before including the header
$page_title = 'Message Status';
// Include the standard header for the site
include 'header.php';

// Check the 'success' parameter from the URL
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;

$status_title = '';
$status_message = '';
$status_class = '';

if ($success == '1') {
    // Success
    $status_title = 'Message Sent!';
    $status_message = 'Thank you for contacting us. We will get back to you as soon as possible.';
    $status_class = 'status-success';
} else {
    // Failure
    $status_title = 'Error Sending Message';
    $status_class = 'status-error';
    
    if ($error == 'invalid') {
        $status_message = 'There was a problem with your submission. Please make sure all fields are filled out correctly and try again.';
    } elseif ($error == 'sendfail') {
        $status_message = 'We could not send your message at this time. This is likely a server issue. Please try again later.';
    } else {
        $status_message = 'An unknown error occurred. Please try again later or contact us directly at info@poolpracticetracker.com.';
    }
}
?>

<!-- Main Content for the Contact Status Page -->
<main class="main-container main-content">

    <section class="status-card-container">
        <div class="contact-card status-card <?php echo $status_class; ?>">
            <h1 class="card-title"><?php echo $status_title; ?></h1>
            <p class="card-text">
                <?php echo $status_message; ?>
            </p>
            <a href="index.php" class="primary-button">Return to Home</a>
        </div>
    </section>

</main>

<?php
// Include the standard footer for the site
include 'footer.php';
?>