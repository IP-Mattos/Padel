<?php
// Start the session
session_start();

// Check if the form was submitted
if (isset($_POST['logout'])) {
    // Optionally, remove the session cookie
    setcookie("goCookToken", '', time() - 3600, '/');

    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Redirect the user to the login page or homepage
    header("Location: index.html");
    exit; // Stop further script execution after the redirect
}
