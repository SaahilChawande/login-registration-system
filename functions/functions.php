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
    if (issset($_SESSION['message']))   {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
}

function token_generator()  {
    $token = $_SESSION['token'] = md5(uniqid(mt_rand(), true));
    return $token;
}

// Validation Functions

function validate_user_registration()   {
    $errors = [];

    $min = 3;
    $max = 20;

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

        if (strlen($username) < $min || strlen($username) > $max)    {
            $errors[] = "Your username should be between {$min} and {$max} characters";
        }

        if (empty($username))   {
            $errors[] = "Your username cannot be empty";
        }

        if (!empty($errors))    {
            foreach ($errors as $error) {
                echo $error;
            }
        }
    }
}