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