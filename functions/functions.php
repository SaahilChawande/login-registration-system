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
    if ($_SERVER['REQUEST_METHOD'] == "POST")   {
        $first_name = clean($_POST['first_name']);
        $last_name = clean($_POST['last_name']);
        $username = clean($_POST['username']);
        $email = clean($_POST['email']);
        $password = clean($_POST['password']);
        $confirm_password = clean($_POST['confirm_password']);
    }
}