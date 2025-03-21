<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Check if the form was submitted
if (isset($_POST['logout'])) {
    // Optionally, remove the session cookie
    if (isset($_COOKIE['goCookToken'])) {
        // Expire the cookie by setting the expiration time to one hour ago
        setcookie("goCookToken", '', time() - 3600, '/');
    }

    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Clear the output buffer
    ob_end_clean();

    // Redirect the user to the login page or homepage
    header("Location: index.php");
    exit; // Always call exit after a header redirect
} else {
    echo "No logout request found.";
}
?>