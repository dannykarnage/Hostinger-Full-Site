<?php
// Set the page title before including the header
$page_title = 'Contact Us';
// Include the standard header for the site
include 'header.php';
?>

<!-- Main Content for the Contact Page -->
<main class="main-container main-content">

    <!-- Contact Form Card -->
    <section class="contact-card-container">
        <div class="contact-card">
            <h1 class="card-title">Get In Touch</h1>
            <p class="card-text">
                Have a question, suggestion, or feedback? We'd love to hear from you. Fill out the form below, and we'll get back to you as soon as possible.
            </p>

            <!-- Contact Form -->
            <!-- Note: This form is for display. You will need a backend script (e.g., mail.php) to process the form data. -->
            <form action="mail-handler.php" method="POST" class="contact-form">

                <!-- Form Group: Name -->
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="John Doe" required>
                </div>

                <!-- Form Group: Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="you@example.com" required>
                </div>

                <!-- Form Group: Subject -->
                <div class="form-group">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" id="subject" name="subject" class="form-input" placeholder="Question about drills" required>
                </div>

                <!-- Form Group: Message -->
                <div class="form-group">
                    <label for="message" class="form-label">Message</label>
                    <textarea id="message" name="message" class="form-textarea" rows="6" placeholder="Your message here..." required></textarea>
                </div>

                <!-- Submit Button -->
                <div class="form-submit-group">
                    <button type="submit" class="primary-button">
                        Send Message
                    </button>
                </div>

            </form>
        </div>
    </section>

</main>

<?php
// Include the standard footer for the site
include 'footer.php';
?>