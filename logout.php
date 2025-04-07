<?php
// Start the session
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the form was submitted
if (isset($_POST['logout'])) {
    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Clear the session cookie by setting its expiration time to the past
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/'); // Delete the cookie
    }

    // Clear the output buffer
    ob_end_clean();

    // Redirect the user to the login page or homepage
    header("Location: index.php");
    exit; // Always call exit after a header redirect
} else {
    echo "No logout request found.";
}