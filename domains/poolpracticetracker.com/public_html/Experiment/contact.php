<?php

    if (isset($_POST['submit']))
    {
        $name = $_POST['name'];
        $subject = $_POST['subject'];
        $mailFrom = $_POST['mail'];
        $message = $_POST['message'];
        
        $mailTo = "ppt_Contact@poolpracticetracker.com";
        $headers = "From: " . $mailFrom;
        $txt = "You have received an email from " . $name . ".\n\n" . $message;

        mail($mailTo, $subject, $txt, $headers);
        header("Location: conact.php?mailsend");
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Contact Us</title>
    </head>
    <body>
        <main>
            <p>Contact Us</p>
            <form class="" action="contact.php" method="post">
                <input type="text" name="name" paceholder="Full Name">
                <input type="text" name="mail" paceholder="Email address">
                <input type="text" name="subject" paceholder="Subject">
                <textarea name="message" placeholder="Message"></textarea><!--id="" cols="30" rows="10"-->
                <button type="submit" name="submit">SEND EMAIL</button>
            </form>
        </main>
    </body>
</html>