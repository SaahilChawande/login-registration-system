<?php

// Helper Functions
function clean($string) {
    return htmlentities($string);
}

function redirect($location)    {
    header("Location: {$location}");
}

function set_message($message)  {
    if (!empty($message))   {
        $_SESSION['message'] = $message;
    }   else    {
        $message = "";
    }
}

function display_message()  {
    if (isset($_SESSION['message']))   {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
}

function token_generator()  {
    $token = $_SESSION['token'] = md5(uniqid(mt_rand(), true));
    return $token;
}

function validation_errors($error_message)    {
    return '
         <div class="alert alert-danger alert-dismissable" role="alert">
            <strong>Warning!</strong> ' . $error_message . '
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>';
}

function email_exists($email) {
    $sql = "SELECT id FROM users WHERE email = '$email';";
    $result = query($sql);
    if (row_count($result) == 1)    {
        return true;
    }   else    {
        return false;
    }
}

function username_exists($username) {
    $sql = "SELECT id FROM users WHERE username = '$username';";
    $result = query($sql);
    if (row_count($result) == 1)    {
        return true;
    }   else    {
        return false;
    }
}

function send_email($email, $subject, $message, $headers)   {
    return mail($email, $subject, $message, $headers);
}

// Validation Functions

function validate_user_registration()   {
    $errors = [];

    $min = 3;
    $max = 20;
    $email_max = 50;

    if ($_SERVER['REQUEST_METHOD'] == "POST")   {
        $first_name = clean($_POST['first_name']);
        $last_name = clean($_POST['last_name']);
        $username = clean($_POST['username']);
        $email = clean($_POST['email']);
        $password = clean($_POST['password']);
        $confirm_password = clean($_POST['confirm_password']);

        if (strlen($first_name) < $min || strlen($first_name) > $max)    {
            $errors[] = "Your first name should be between {$min} and {$max} characters";
        }

        if (empty($first_name)) {
            $errors[] = "Your first name cannot be empty";
        }

        if (strlen($last_name) < $min || strlen($last_name) > $max)    {
            $errors[] = "Your last name should be between {$min} and {$max} characters";
        }

        if (empty($last_name))  {
            $errors[] = "Your last name cannot be empty";
        }

        if (username_exists($username)) {
            $errors[] = "Username already taken";
        }

        if (strlen($username) < $min || strlen($username) > $max)    {
            $errors[] = "Your username should be between {$min} and {$max} characters";
        }

        if (empty($username))   {
            $errors[] = "Your username cannot be empty";
        }

        if (email_exists($email))   {
            $errors[] = "Email is already registered";
        }

        if (strlen($email) > $email_max)  {
            $errors[] = "Your email cannot be more than {$email_max} characters";
        }

        if ($password !== $confirm_password)    {
            $errors[] = "Your password fields do not match";
        }

        if (!empty($errors))    {
            foreach ($errors as $error) {
                echo validation_errors($error);
            }
        } else  {
            if (register_user($first_name, $last_name, $username, $email, $password))   {
                set_message("<p class='bg-success text-center'>Please check your email or spam folder for an activation link.</p>");
                redirect("index.php");
            } else  {
                set_message("<p class='bg-danger text-center'>Sorry, we could not register the user.</p>");
                redirect("index.php");
            }
        }
    }
}

// Register User Function

function register_user($first_name, $last_name, $username, $email, $password)    {
    // Escape the data
    $first_name = escape($first_name);
    $last_name = escape($last_name);
    $username = escape($username);
    $email = escape($email);
    $password = escape($password);

    if (email_exists($email))   {
        return false;
    }   else if (username_exists($username))    {
        return false;
    }   else    {
        $password = md5($password);
        $validation_code = md5($username . microtime());

        $sql = "INSERT INTO users(first_name, last_name, username, email, password, validation_code, active)";
        $sql .= " VALUES('{$first_name}', '{$last_name}', '{$username}', '{$email}', '{$password}', '{$validation_code}', 0);";

        $result = query($sql);
        confirm($result);

        // Send email
        $subject = "Activate Account";
        $message = "Please click the link below to activate your account. <br> http://localhost/login-registration-system/activate.php?email=$email&code=$validation_code";
        $headers = "From: no-reply@mywebsite.com";

        send_email($email, $subject, $message, $headers);

        return true;
    }
}

// Activate User Function

function activate_user()    {
    if ($_SERVER['REQUEST_METHOD'] == "GET")    {
        if (isset($_GET['email']))  {
            $email = clean($_GET['email']);
            $validation_code = clean($_GET['code']);

            $sql = "SELECT id FROM users WHERE email = '" . escape($_GET['email']) . "' AND validation_code = '" . escape($_GET['code']) . "';";
            $result = query($sql);
            confirm($result);

            if (row_count($result) == 1) {
                $sql2 = "UPDATE users SET active = 1, validation_code = '0' WHERE email = '" . escape($email) . "' AND validation_code = '" . escape($validation_code) . "';";
                $result2 = query($sql2);
                confirm($result2);

                set_message("<p class='bg-success text-center'>Your account has been activated. Please login.</p>");

                redirect("login.php");
            }   else    {
                set_message("<p class='bg-danger text-center'>Sorry, your account could not be activated.</p>");
                redirect("login.php");
            }
        }
    }
}