<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Load Composer's autoloader or include files manually
// UPDATED: Pointing to the 'phpMailer/' directory (no /src)
require 'phpMailer/Exception.php';
require 'phpMailer/PHPMailer.php';
require 'phpMailer/SMTP.php';

// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Sanitize and retrieve form data
    $name = filter_var(trim($_POST["name"]), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = filter_var(trim($_POST["subject"]), FILTER_SANITIZE_STRING);
    $message_body = filter_var(trim($_POST["message"]), FILTER_SANITIZE_STRING);

    // 2. Validate data
    if (empty($name) || empty($email) || empty($subject) || empty($message_body) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Redirect to status page with an error
        header("Location: contact-status.php?success=0&error=invalid");
        exit;
    }

    // 3. Set email parameters
    $recipient = "info@poolpracticetracker.com";
    $email_subject = "New Contact Form Submission: " . $subject;
    
    // 4. Build the email body
    $email_content = "You have received a new message from your website contact form.<br><br>";
    $email_content .= "<b>Name:</b> " . htmlspecialchars($name) . "<br>";
    $email_content .= "<b>Email:</b> " . htmlspecialchars($email) . "<br><br>";
    $email_content .= "<b>Subject:</b> " . htmlspecialchars($subject) . "<br>";
    $email_content .= "<b>Message:</b><br>" . nl2br(htmlspecialchars($message_body)); // nl2br to preserve line breaks

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true); // Enable exceptions

    try {
        // 5. Configure Server settings (SMTP)
        // Get SMTP credentials from environment variables (set in .htaccess)
        $smtp_host = getenv('SMTP_HOST');
        $smtp_user = getenv('SMTP_USER');
        $smtp_pass = getenv('SMTP_PASS');
        $smtp_port = getenv('SMTP_PORT'); // e.g., 587 or 465

        // Enable verbose debug output (optional, remove for production)
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
        
        $mail->isSMTP();                                    // Send using SMTP
        $mail->Host       = $smtp_host;                   // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                           // Enable SMTP authentication
        $mail->Username   = $smtp_user;                   // SMTP username
        $mail->Password   = $smtp_pass;                   // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also an option
        $mail->Port       = (int)$smtp_port;                // TCP port to connect to

        // 6. Set Recipients
        $mail->setFrom('no-reply@poolpracticetracker.com', 'PPT Contact Form'); // "From" address
        $mail->addAddress($recipient, 'PoolPracticeTracker Admin');     // "To" address
        $mail->addReplyTo($email, $name); // Set the "Reply-To" to the user who submitted

        // 7. Set Content
        $mail->isHTML(true);                                // Set email format to HTML
        $mail->Subject = $email_subject;
        $mail->Body    = $email_content;
        $mail->AltBody = strip_tags($email_content); // Plain text version for non-HTML clients

        // 8. Send the email
        $mail->send();
        
        // Redirect to success page
        header("Location: contact-status.php?success=1");
        exit;

    } catch (Exception $e) {
        // Email sending failed
        // Log the error (optional, but recommended)
        // error_log("PHPMailer Error: {$mail->ErrorInfo}");
        
        header("Location: contact-status.php?success=0&error=sendfail");
        exit;
    }

} else {
    // If not a POST request, redirect to the contact page
    header("Location: contact.php");
    exit;
}
?>