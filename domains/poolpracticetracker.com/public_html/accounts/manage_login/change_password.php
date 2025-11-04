<?php

    session_start();
    include('../../db_files/connection.php');
    $error_message = "";
    $outcome = "";
    $username = ""; // Initialize username

    if(isset($_GET['pkey']))
    {
        // Path 1: Password reset request via email link
        $pkey = $_GET['pkey'];

        // Check pkey validity and verification status (using prepared statements)
        $stmt = $conn->prepare("SELECT `username` FROM `users` WHERE `verified` = 1 AND `pkey` = ?");
        $stmt->bind_param("s", $pkey);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result)
        {
            $error_message = "Database issue accessing account. Please try again later.";
        }
        elseif($result->num_rows != 1)
        {
            $error_message = "Could not locate a matching password reset request. Please try resetting your password again.";
        }
        else
        {
            $row = $result->fetch_assoc();
            $username = $row['username'];
            
            // Check for expiration (using prepared statements)
            $stmt_time = $conn->prepare("SELECT TIMESTAMPDIFF(SECOND, `password_reset_request_timestamp`, CURRENT_TIMESTAMP) AS `difference` FROM `users` WHERE `pkey` = ?");
            $stmt_time->bind_param("s", $pkey);
            $stmt_time->execute();
            $result_time = $stmt_time->get_result();

            if (!$result_time)
            {
                $error_message = "Database issue accessing time verification. Please try again later or use the Contact Us link above to report the issue.";
            }
            else
            {
                $row_time = $result_time->fetch_assoc();
                $timediff = (int) $row_time['difference'];
                if($timediff > 86400) // 24 hours in seconds
                {
                    $error_message = "That password reset request has expired. Please use attempt to reset your password again.";
                }
                else
                {
                    // Set password_reset_required flag (using prepared statements)
                    $stmt_update = $conn->prepare("UPDATE `users` SET `password_reset_required` = 1 WHERE `pkey` = ?");
                    $stmt_update->bind_param("s", $pkey);
                    $result_update = $stmt_update->execute();
                    
                    if(!$result_update)
                    {
                        $error_message = "An error occurred with your request. Please try to reset your password again.";
                    }
                }
                $stmt_time->close();
            }
        }
        $stmt->close();
    }
    elseif(isset(($_POST['submit'])))
    {
        // Path 2: Form submission to change or reset password
        if(isset($_SESSION['username']))
        {
            // Logged-in user is changing their password
            $username = $_SESSION['username'];
            $current_password = trim($_POST['current_password']);
            
            // Fetch stored hash for comparison (using prepared statements)
            $stmt = $conn->prepare("SELECT `password` FROM `users` WHERE `username` = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result)
            {
                $row = $result->fetch_assoc();
                
                if(!password_verify($current_password, $row['password']))
                {
                    $error_message = "Current password is incorrect. Please try again.";
                }
            }
            $stmt->close();
        }
        else
        {
            // Anonymous user is resetting password via pkey
            $pkey = $_POST['pkey'];
            
            // Fetch username using pkey (using prepared statements)
            $stmt = $conn->prepare("SELECT `username` FROM `users` WHERE `pkey` = ?");
            $stmt->bind_param("s", $pkey);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result && $result->num_rows == 1)
            {
                $row = $result->fetch_assoc();
                $username = $row['username'];
            }
            else
            {
                $error_message = "An error occurred while resetting the password. Please try again or contact a site admin.";
            }
            $stmt->close();
        }

        if(empty($error_message))
        {
            $password_1 = trim($_POST['new_password']);
            $password_2 = trim($_POST['confirm_password']);
            
            if($password_1 !== $password_2)
            {
                $error_message = "Passwords do not match. Please try again.";
            }
            elseif(strlen($password_1) < 8 || strlen($password_1) > 32)
            {
                $error_message = "Password must be between 8 and 32 characters in length.";
            }
            elseif(!preg_match('/^[a-zA-Z0-9._!@#$%^&*()-]*$/', $password_1) || preg_match('/^[\/]*$/', $password_1))
            {
                $error_message = "Password must only contain letters, numbers, or symbols ._!@#$%^&*()-";
            }
            else
            {
                $new_password_hash = password_hash($password_1, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("UPDATE `users` SET `password` = ?, `pkey` = NULL, `password_reset_required` = 0 WHERE `username` = ?");
                $stmt->bind_param("ss", $new_password_hash, $username);
                $result = $stmt->execute();
                
                if($result)
                {
                    header("Location: /accounts/manage_login/password_changed.php");
                }
                else
                {
                    $error_message = "Something went wrong while changing the password. Please try again.";
                }
                $stmt->close();
            }
        }

    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>
            Reset Password - Pool Practice Tracker
        </title>
        <link rel="stylesheet" type="text/css" href="/styles/general.css">
        <link rel="stylesheet" type="text/css" href="/styles/header.css">
        <link rel="stylesheet" type="text/css" href="/styles/user-handling.css">

    </head>
    <body>
        
        <?php 
        include('../../temps/header.php'); 
        ?>

        <main class="main-section">
            <div class="user-heading">
                <h2>
                    <?php if(!isset($_GET['pkey'])): ?>
                        Change Password
                    <?php else: ?>
                        Reset Password
                    <?php endif; ?>
                </h2>
            </div>
            <form class="user-form" action="/accounts/manage_login/change_password.php " method="post">
                <?php if(!empty($error_message)): ?>
                    <div style="width: 92%; margin: 0px auto; padding: 10px; border: 1px solid #a94442; color: #a94442; background: #f2dede; border-radius: 5px; text-align: left;">
                        <p><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                <?php endif; ?>
                <?php if(!(isset($_GET['pkey']) && !empty($error_message))): ?>
                    <?php if(!isset($_GET['pkey']) && isset($_SESSION['username'])): ?>
                        <!-- Current Password Field for Logged-In User -->
                        <div class="input-group password-container">
                            <label for="current_password-input">Current Password</label>
                            <input id="current_password-input" class="password-input" type="password" name="current_password">
                            <span class="password-toggle" data-target="all-passwords">
                                <!-- Eye Icons -->
                                <svg class="eye-icon eye-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M288 144a144 144 0 1 1 0 288 144 144 0 1 1 0-288zm0 180a36 36 0 1 0 0-72 36 36 0 1 0 0 72zm280.9-111.4c-81.2-132.8-212.5-220.3-360.9-220.3S81.3 119.8 0.1 252.6c-1.1 1.7-1.1 4 0 5.7C81.3 388.2 212.5 475.7 360.9 475.7S552.2 388.2 573.1 258.3c1.1-1.7 1.1-4 0-5.7zM360.9 435.7C212.5 435.7 81.3 348.2 24.1 256c57.2-92.2 188.5-179.7 336.9-179.7S552.2 163.8 573.1 256c-20.9 92.2-152.2 179.7-300.6 179.7z"></path></svg>
                                <svg class="eye-icon eye-slash hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M542.4 23.1C553.8 30.2 559 42.1 559 55v402c0 10.3-6.5 21.2-17.9 28.3-11.4 7.1-25 7.1-36.4 0l-487-303C1.6 179.2-3.6 167.3 3 155c11.4-7.1 25-7.1 36.4 0l487 303c11.4 7.1 25 7.1 36.4 0l-487-303zM566.1 423c-1.1 1.7-1.1 4 0 5.7 81.2 132.8 212.5 220.3 360.9 220.3S552.2 388.2 573.1 258.3c1.1-1.7 1.1-4 0-5.7-20.9-106.9-152.2-179.7-300.6-179.7S81.3 163.8 24.1 256c57.2 92.2 188.5 179.7 336.9 179.7zM288 144c-31.9 0-61.9-10.3-86.8-29.3l15.9-12.4c17.5 13.7 39.5 21.7 64.9 21.7 64 0 112-51.2 112-115.2 0-25.4-8-47.4-21.7-64.9l12.4-15.9c18.9 24.8 29.3 54.8 29.3 86.8 0 88.4-71.6 160-160 160zM144 288a144 144 0 0 1 45.1-105.7l-35.1-27.6c-4.4-3.5-10.8-3.5-15.2 0s-3.5 10.8 0 15.2l35.1 27.6z"></path></svg>
                            </span>
                        </div>
                    <?php else: ?>
                        <div>
                            <input id="pkey" type="hidden" name="pkey" value="<?php echo htmlspecialchars($pkey ?? ''); ?>">
                        </div>
                    <?php endif; ?>
                    
                    <!-- New Password Field -->
                    <div class="input-group password-container">
                        <label for="new_password-input">New Password</label>
                        <input id="new_password-input" class="password-input" type="password" name="new_password">
                        <span class="password-toggle" data-target="all-passwords">
                            <!-- Eye Icons -->
                            <svg class="eye-icon eye-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M288 144a144 144 0 1 1 0 288 144 144 0 1 1 0-288zm0 180a36 36 0 1 0 0-72 36 36 0 1 0 0 72zm280.9-111.4c-81.2-132.8-212.5-220.3-360.9-220.3S81.3 119.8 0.1 252.6c-1.1 1.7-1.1 4 0 5.7C81.3 388.2 212.5 475.7 360.9 475.7S552.2 388.2 573.1 258.3c1.1-1.7 1.1-4 0-5.7zM360.9 435.7C212.5 435.7 81.3 348.2 24.1 256c57.2-92.2 188.5-179.7 336.9-179.7S552.2 163.8 573.1 256c-20.9 92.2-152.2 179.7-300.6 179.7z"></path></svg>
                            <svg class="eye-icon eye-slash hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M542.4 23.1C553.8 30.2 559 42.1 559 55v402c0 10.3-6.5 21.2-17.9 28.3-11.4 7.1-25 7.1-36.4 0l-487-303C1.6 179.2-3.6 167.3 3 155c11.4-7.1 25-7.1 36.4 0l487 303c11.4 7.1 25 7.1 36.4 0l-487-303zM566.1 423c-1.1 1.7-1.1 4 0 5.7 81.2 132.8 212.5 220.3 360.9 220.3S552.2 388.2 573.1 258.3c1.1-1.7 1.1-4 0-5.7-20.9-106.9-152.2-179.7-300.6-179.7S81.3 163.8 24.1 256c57.2 92.2 188.5 179.7 336.9 179.7zM288 144c-31.9 0-61.9-10.3-86.8-29.3l15.9-12.4c17.5 13.7 39.5 21.7 64.9 21.7 64 0 112-51.2 112-115.2 0-25.4-8-47.4-21.7-64.9l12.4-15.9c18.9 24.8 29.3 54.8 29.3 86.8 0 88.4-71.6 160-160 160zM144 288a144 144 0 0 1 45.1-105.7l-35.1-27.6c-4.4-3.5-10.8-3.5-15.2 0s-3.5 10.8 0 15.2l35.1 27.6z"></path></svg>
                        </span>
                    </div>
                    
                    <!-- Confirm Password Field -->
                    <div class="input-group password-container">
                        <!-- Note: The label in the original file was "Current Password", which is confusing. I changed it to "Confirm Password" -->
                        <label for="confirm_password-input">Confirm Password</label>
                        <input id="confirm_password-input" class="password-input" type="password" name="confirm_password">
                        <span class="password-toggle" data-target="all-passwords">
                            <!-- Eye Icons -->
                            <svg class="eye-icon eye-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M288 144a144 144 0 1 1 0 288 144 144 0 1 1 0-288zm0 180a36 36 0 1 0 0-72 36 36 0 1 0 0 72zm280.9-111.4c-81.2-132.8-212.5-220.3-360.9-220.3S81.3 119.8 0.1 252.6c-1.1 1.7-1.1 4 0 5.7C81.3 388.2 212.5 475.7 360.9 475.7S552.2 388.2 573.1 258.3c1.1-1.7 1.1-4 0-5.7zM360.9 435.7C212.5 435.7 81.3 348.2 24.1 256c57.2-92.2 188.5-179.7 336.9-179.7S552.2 163.8 573.1 256c-20.9 92.2-152.2 179.7-300.6 179.7z"></path></svg>
                            <svg class="eye-icon eye-slash hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M542.4 23.1C553.8 30.2 559 42.1 559 55v402c0 10.3-6.5 21.2-17.9 28.3-11.4 7.1-25 7.1-36.4 0l-487-303C1.6 179.2-3.6 167.3 3 155c11.4-7.1 25-7.1 36.4 0l487 303c11.4 7.1 25 7.1 36.4 0l-487-303zM566.1 423c-1.1 1.7-1.1 4 0 5.7 81.2 132.8 212.5 220.3 360.9 220.3S552.2 388.2 573.1 258.3c1.1-1.7 1.1-4 0-5.7-20.9-106.9-152.2-179.7-300.6-179.7S81.3 163.8 24.1 256c57.2 92.2 188.5 179.7 336.9 179.7zM288 144c-31.9 0-61.9-10.3-86.8-29.3l15.9-12.4c17.5 13.7 39.5 21.7 64.9 21.7 64 0 112-51.2 112-115.2 0-25.4-8-47.4-21.7-64.9l12.4-15.9c18.9 24.8 29.3 54.8 29.3 86.8 0 88.4-71.6 160-160 160zM144 288a144 144 0 0 1 45.1-105.7l-35.1-27.6c-4.4-3.5-10.8-3.5-15.2 0s-3.5 10.8 0 15.2l35.1 27.6z"></path></svg>
                        </span>
                    </div>
                    
                    <div class="input-group">
                        <button id="button" type="submit" value="change_password" name="submit" class="user-form-btn" style="cursor: pointer;">Submit</button>
                    </div>
                <?php endif; ?>
            </form>
        </main>

        <?php 
        include('../../temps/footer.php'); 
        ?>

        <!-- JavaScript for toggling password visibility across all fields -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const passwordToggles = document.querySelectorAll('.password-toggle[data-target="all-passwords"]');
                const passwordInputs = document.querySelectorAll('.password-input');
                
                // Collect all eye icons across the document
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
                        if (passwordInputs.length > 0) {
                            const isPassword = passwordInputs[0].getAttribute('type') === 'password';
                            updateVisibility(isPassword); // If current is password, show (isPassword=true, shouldShow=true)
                        }
                    });
                });

                // Initial setup: ensure the slash icon is hidden on load
                updateVisibility(false);
            });
        </script>

    </body>
</html>