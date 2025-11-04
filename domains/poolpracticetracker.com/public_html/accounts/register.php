<?php

    $success = false;

    session_start();

    $errors = array();
    $username = "";
    $email = "";
    $password_1 = "";
    $password_2 = "";

    //determine if a user is already logged in and, if so, redirect to the homepage
    $user_already_logged_in = !empty($_SESSION['username']);
    if($user_already_logged_in)
    {
        array_push($errors, "A user is already logged in. To change user, please logout first.");
    }
    elseif($_SERVER['REQUEST_METHOD'] == "POST")
    {
        if(isset($_POST['submit']))
        {            
            // Note: The /db_files/connection.php must be included first for $conn to be defined.
            // FIX: Changed absolute path to relative path
            include("../db_files/connection.php");

            // No need for mysqli_real_escape_string anymore, as prepared statements handle escaping.
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password_1 = trim($_POST['password_1']);
            $password_2 = trim($_POST['password_2']);

            if(empty($username))
            {
                array_push($errors, "Username is required.");
            }
            if(empty($email))
            {
                array_push($errors, "Email is required");
            }
            else
            {
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                    array_push($errors, 'Email must be a valid email address.');
                }    
            }
            if(empty($password_1))
            {
                array_push($errors, "Password is required");
            }
            if($password_1 != $password_2)
            {
                array_push($errors, "Passwords do not match");  
            }
            if(!preg_match('/^[a-zA-Z0-9_.-]*$/', $username))
            {
                array_push($errors, "Username must only contain letters, numbers, underscore (_), period (.), or dash (-).");
            }
            if(!preg_match('/^[a-zA-Z]*$/', $username[0]))
            {
                array_push($errors, "Username must start with a letter.");
            }
            if(strlen($username) < 6 || strlen($username > 32))
            {
                array_push($errors, "Username must be between 6 and 32 characters in length.");
            }
            if(strlen($password_1) < 8 || strlen($password_1) > 32)
            {
                array_push($errors, "Password must be between 8 and 32 characters in length.");
            }

            if(count($errors) == 0)
            {
                if(!preg_match('/^[a-zA-Z0-9._!@#$%^&*()-]*$/', $password_1) || preg_match('/^[\/]*$/', $password_1))
                {
                    array_push($errors, "Password must only contain letters, numbers, or symbols ._!@#$%^&*()-");
                }
            }

            // Check if username already exists (using prepared statements)
            if (count($errors) == 0)
            {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM `users` WHERE `username` = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();
                
                if($count > 0)
                {
                    array_push($errors, "User already exists");
                }
            }

            // Check if email already exists (using prepared statements)
            if (count($errors) == 0)
            {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM `users` WHERE `email` = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();

                if($count > 0)
                {
                    array_push($errors, "A user with that email address already exists.");
                }
            }

            //were there any errors?
            if(count($errors) == 0) {
                // *** CRITICAL SECURITY FIX: Use password_hash() instead of md5() ***
                $password = password_hash($password_1, PASSWORD_DEFAULT);
                
                // Remove the debug echo line for security.
                
                $vkey = md5(time().$username);

                // Insert new user (using prepared statements)
                $stmt = $conn->prepare("INSERT INTO users (`username`, `email`, `password`, `vkey`) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $username, $email, $password, $vkey);
                $result = $stmt->execute();
                $stmt->close();

                if($result)
                {
                    //send email
                    $subject = "Email verification";
                    //$email_message is defined in the verification-email.php file
                    // FIX: Changed absolute path to relative path
                    include('verification-email.php');
                    $headers = "From: donotreply@poolpracticetracker.com \r\n";
                    $headers .= "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                    $success = mail($email,$subject,$email_message,$headers);
                }
                else
                {
                    array_push($errors, "Trouble connecting with database. Please try again later or use the 'Contact Us' form to contact a site admin.");
                }
            }
        }
    }

    if($success)
    {
        header("Location: /accounts/thankyou.php");
        die();
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Registration - Pool Practice Tracker</title>
        <link rel="stylesheet" type="text/css" href="/styles/general.css">
        <link rel="stylesheet" type="text/css" href="/styles/header.css">
        <link rel="stylesheet" type="text/css" href="/styles/user-handling.css">
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>
    <body>
        
        <?php 
        // FIX: Changed absolute path to relative path
        include('../temps/header.php'); 
        ?>

        <main class="main-section">
            <div class="user-heading">
                <h2>Register</h2>
            </div>
            <form class="user-form" action="/accounts/register.php " method="post">
                <?php if(count($errors) > 0): ?>
                <div style="width: 92%; margin: 0px auto; padding: 10px; border: 1px solid #a94442; color: #a94442; background: #f2dede; border-radius: 5px; text-align: left;">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div class="input-group">
                    <label for="">Username</label>
                    <input id="text" type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">
                </div>
                <div class="input-group">
                    <label for="">Email address</label>
                    <input id="text" type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
                </div>
                
                <!-- START: Password input with toggle icon -->
                <div class="input-group password-container">
                    <label for="password_1-input">Password</label>
                    <input id="password_1-input" class="password-input" type="password" name="password_1" value="<?php echo htmlspecialchars($password_1); ?>">
                    <span class="password-toggle" data-target="all-passwords">
                        <!-- Open Eye Icon (Visible by default: type="password") -->
                        <svg class="eye-icon eye-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                            <path fill="currentColor" d="M288 144a144 144 0 1 1 0 288 144 144 0 1 1 0-288zm0 180a36 36 0 1 0 0-72 36 36 0 1 0 0 72zm280.9-111.4c-81.2-132.8-212.5-220.3-360.9-220.3S81.3 119.8 0.1 252.6c-1.1 1.7-1.1 4 0 5.7C81.3 388.2 212.5 475.7 360.9 475.7S552.2 388.2 573.1 258.3c1.1-1.7 1.1-4 0-5.7zM360.9 435.7C212.5 435.7 81.3 348.2 24.1 256c57.2-92.2 188.5-179.7 336.9-179.7S552.2 163.8 573.1 256c-20.9 92.2-152.2 179.7-300.6 179.7z"></path>
                        </svg>
                        <!-- Closed/Slashed Eye Icon (Hidden by default: type="password") -->
                        <svg class="eye-icon eye-slash hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                            <path fill="currentColor" d="M542.4 23.1C553.8 30.2 559 42.1 559 55v402c0 10.3-6.5 21.2-17.9 28.3-11.4 7.1-25 7.1-36.4 0l-487-303C1.6 179.2-3.6 167.3 3 155c11.4-7.1 25-7.1 36.4 0l487 303c11.4 7.1 25 7.1 36.4 0l-487-303zM566.1 423c-1.1 1.7-1.1 4 0 5.7 81.2 132.8 212.5 220.3 360.9 220.3S552.2 388.2 573.1 258.3c1.1-1.7 1.1-4 0-5.7-20.9-106.9-152.2-179.7-300.6-179.7S81.3 163.8 24.1 256c57.2 92.2 188.5 179.7 336.9 179.7zM288 144c-31.9 0-61.9-10.3-86.8-29.3l15.9-12.4c17.5 13.7 39.5 21.7 64.9 21.7 64 0 112-51.2 112-115.2 0-25.4-8-47.4-21.7-64.9l12.4-15.9c18.9 24.8 29.3 54.8 29.3 86.8 0 88.4-71.6 160-160 160zM144 288a144 144 0 0 1 45.1-105.7l-35.1-27.6c-4.4-3.5-10.8-3.5-15.2 0s-3.5 10.8 0 15.2l35.1 27.6z"></path>
                        </svg>
                    </span>
                </div>
                <!-- END: Password input with toggle icon -->
                
                <!-- START: Confirm Password input with toggle icon -->
                <div class="input-group password-container">
                    <label for="password_2-input">Confirm password</label>
                    <input id="password_2-input" class="password-input" type="password" name="password_2" value="<?php echo htmlspecialchars($password_2); ?>">
                    <span class="password-toggle" data-target="all-passwords">
                        <!-- Open Eye Icon (Visible by default: type="password") -->
                        <svg class="eye-icon eye-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                            <path fill="currentColor" d="M288 144a144 144 0 1 1 0 288 144 144 0 1 1 0-288zm0 180a36 36 0 1 0 0-72 36 36 0 1 0 0 72zm280.9-111.4c-81.2-132.8-212.5-220.3-360.9-220.3S81.3 119.8 0.1 252.6c-1.1 1.7-1.1 4 0 5.7C81.3 388.2 212.5 475.7 360.9 475.7S552.2 388.2 573.1 258.3c1.1-1.7 1.1-4 0-5.7zM360.9 435.7C212.5 435.7 81.3 348.2 24.1 256c57.2-92.2 188.5-179.7 336.9-179.7S552.2 163.8 573.1 256c-20.9 92.2-152.2 179.7-300.6 179.7z"></path>
                        </svg>
                        <!-- Closed/Slashed Eye Icon (Hidden by default: type="password") -->
                        <svg class="eye-icon eye-slash hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                            <path fill="currentColor" d="M542.4 23.1C553.8 30.2 559 42.1 559 55v402c0 10.3-6.5 21.2-17.9 28.3-11.4 7.1-25 7.1-36.4 0l-487-303C1.6 179.2-3.6 167.3 3 155c11.4-7.1 25-7.1 36.4 0l487 303c11.4 7.1 25 7.1 36.4 0l-487-303zM566.1 423c-1.1 1.7-1.1 4 0 5.7 81.2 132.8 212.5 220.3 360.9 220.3S552.2 388.2 573.1 258.3c1.1-1.7 1.1-4 0-5.7-20.9-106.9-152.2-179.7-300.6-179.7S81.3 163.8 24.1 256c57.2 92.2 188.5 179.7 336.9 179.7zM288 144c-31.9 0-61.9-10.3-86.8-29.3l15.9-12.4c17.5 13.7 39.5 21.7 64.9 21.7 64 0 112-51.2 112-115.2 0-25.4-8-47.4-21.7-64.9l12.4-15.9c18.9 24.8 29.3 54.8 29.3 86.8 0 88.4-71.6 160-160 160zM144 288a144 144 0 0 1 45.1-105.7l-35.1-27.6c-4.4-3.5-10.8-3.5-15.2 0s-3.5 10.8 0 15.2l35.1 27.6z"></path>
                        </svg>
                    </span>
                </div>
                <!-- END: Confirm Password input with toggle icon -->
                
                <div class="g-recaptcha" data-sitekey="6LdhASUjAAAAAM8x8JpWFkP0Oe-JbiYr6GeUyVm4"></div>
                <div class="input-group">
                    <button id="button" type="submit" value="submit" name="submit" class="user-form-btn" style="cursor: pointer">Register</button>
                </div>
                <p>
                    Already a member? <a href="/accounts/login.php">Login!</a>
                </p>
            </form>
        </main>

        <?php 
        // FIX: Changed absolute path to relative path
        include('../temps/footer.php'); 
        ?>
        
        <!-- JavaScript for toggling password visibility -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const passwordToggles = document.querySelectorAll('.password-toggle[data-target="all-passwords"]');
                const passwordInputs = document.querySelectorAll('.password-input');
                const eyeOpenIcons = document.querySelectorAll('.eye-open');
                const eyeSlashIcons = document.querySelectorAll('.eye-slash');
                
                // Function to update the display state of all elements
                function updateVisibility(shouldShow) {
                    passwordInputs.forEach(input => {
                        input.setAttribute('type', shouldShow ? 'text' : 'password');
                    });

                    // Toggle icon visibility across all instances
                    eyeOpenIcons.forEach(icon => {
                        icon.classList.toggle('hidden', shouldShow);
                    });
                    eyeSlashIcons.forEach(icon => {
                        icon.classList.toggle('hidden', !shouldShow);
                    });
                }
                
                // Add event listener to each toggle icon
                passwordToggles.forEach(toggle => {
                    toggle.addEventListener('click', function () {
                        // Check the current state of the first input to determine the new state
                        const isPassword = passwordInputs[0].getAttribute('type') === 'password';
                        updateVisibility(isPassword); // If current is password, show (isPassword=true, shouldShow=true)
                    });
                });

                // Initial setup: ensure the slash icon is hidden on load
                updateVisibility(false);
            });
        </script>

    </body>
</html>
