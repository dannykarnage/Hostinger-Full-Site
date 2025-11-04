<?php
// This script handles the data submitted from contact.php

// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Sanitize and retrieve form data
    // Use filter_var for basic sanitization
    $name = filter_var(trim($_POST["name"]), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = filter_var(trim($_POST["subject"]), FILTER_SANITIZE_STRING);
    $message_body = filter_var(trim($_POST["message"]), FILTER_SANITIZE_STRING);

    // 2. Validate data (simple validation)
    if (empty($name) || empty($email) || empty($subject) || empty($message_body) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // If data is invalid, redirect back to contact page with an error
        // Note: A more robust solution would use sessions to pass error messages
        header("Location: contact-status.php?success=0&error=invalid");
        exit;
    }

    // 3. Set email parameters
    $recipient = "info@poolpracticetracker.com";
    $email_subject = "New Contact Form Submission: " . $subject;
    
    // 4. Build the email body
    $email_content = "You have received a new message from your website contact form.\n\n";
    $email_content .= "Name: $name\n";
    $email_content .= "Email: $email\n\n";
    $email_content .= "Subject: $subject\n";
    $email_content .= "Message:\n$message_body\n";

    // 5. Build the email headers
    // We use a "From" address on the same domain to improve deliverability
    $email_headers = "From: no-reply@poolpracticetracker.com\r\n";
    // Set the Reply-To header to the user's email
    $email_headers .= "Reply-To: $email\r\n";
    $email_headers .= "X-Mailer: PHP/" . phpversion();

    // 6. Send the email
    // The mail() function's success depends on the server's email configuration.
    // Make sure your hosting (Hostinger) has PHP mail enabled.
    if (mail($recipient, $email_subject, $email_content, $email_headers)) {
        // Email sent successfully
        header("Location: contact-status.php?success=1");
        exit;
    } else {
        // Email sending failed
        header("Location: contact-status.php?success=0&error=sendfail");
        exit;
    }

} else {
    // If not a POST request, redirect to the contact page
    header("Location: contact.php");
    exit;
}
?>