<?php
<<<<<<< HEAD

    $success = false;

=======
>>>>>>> 55f38f8 (Adding password visibility toggles to various pages)
    session_start();

    // FIX: Changed absolute path to relative path
    //include('../../functions.php');
    include('../db_files/connection.php');
    $login_error = "";
    
    //determine if a user is already logged in and, if so, redirect to the homepage
    $user_already_logged_in = !empty($_SESSION['username']);
    if($user_already_logged_in)
    {
        $login_error = "A user is already logged in. To change user, please logout first.";
    }
    elseif($_SERVER['REQUEST_METHOD'] == "POST")
    {
        // No need for mysqli_real_escape_string or md5() here as we use prepared statements and password_verify()
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if(!empty($username) && !empty($password))
        {
            // 1. Check for verified user status and fetch stored hash (using prepared statements)
            $stmt = $conn->prepare("SELECT `password`, `password_reset_required` FROM `users` WHERE `username` = ? AND `verified` = 1 limit 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result && $result->num_rows == 1)
            {
                $user_data = $result->fetch_assoc();

                if($user_data['password_reset_required'] == 1)
                {
                    $login_error = "The password on that account has been disabled. Please click on the <b>Having Trouble?</b> link below to request your password be reset.";
                }
                // CRITICAL SECURITY FIX: Use password_verify() instead of md5() comparison
                elseif(password_verify($password, $user_data['password']))
                {
                    $_SESSION['username'] = $username; // Use the cleaned username
                    header("Location: /index.php");
                    die();
                }
            }
            $stmt->close();
            
            if(empty($login_error))
            {
                // 2. If login failed, check if the unverified user exists to provide a specific error message (using prepared statements)
                $stmt = $conn->prepare("SELECT `username` FROM `users` WHERE `username` = ? AND `verified` = 0 limit 1");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if($result && $result->num_rows == 1)
                {
                    $login_error = "That account has not yet been verified. Please click the link in the verification email to verify your account before logging in.";
                }
                else
                {
                    $login_error = "Incorrect password or user not found.";
                }
                $stmt->close();
            }
        }
        else
        {
            $login_error = "Invalid login credentials. Please try again.";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Login - Pool Practice Tracker</title>
        <link rel="stylesheet" type="text/css" href="/styles/general.css">
        <link rel="stylesheet" type="text/css" href="/styles/header.css">
        <link rel="stylesheet" type="text/css" href="/styles/user-handling.css">
    </head>
    <body>
        
        <?php 
        // FIX: Changed absolute path to relative path
        include('../temps/header.php'); 
        ?>

        <main class="main-section">
            <div class="user-heading">
                <h2>Login</h2>
            </div>
            <form class="user-form" action="/accounts/login.php " method="post">
                <?php if(!empty($login_error)): ?>
                    <div style="width: 92%; margin: 0px auto; padding: 10px; border: 1px solid #a94442; color: #a94442; background: #f2dede; border-radius: 5px; text-align: left;">
                        <p><?php echo htmlspecialchars($login_error); ?></p>
                    </div>
                <?php endif; ?>
                <div class="input-group">
                    <label for="">Username</label>
                    <input id="text" type="text" name="username">
                </div>
<<<<<<< HEAD
                
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
=======
                <div class="input-group">
                    <label for="">Password</label>
                    <input id="text" type="password" name="password">
                </div>
                <div class="input-group">
                    <button id="button" type="submit" value="login" name="login" class="user-form-btn" style="cursor: pointer;">Login</button>
>>>>>>> 55f38f8 (Adding password visibility toggles to various pages)
                </div>
                <p>
                    New here? <a href="/accounts/register.php">Sign up!</a>
                </p>
                <p style="font-size: 12px">
                    Having trouble? <a href="/accounts/manage_login/request_password_reset.php">Click here.</a>
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