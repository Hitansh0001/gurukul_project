<?php
// Start session
session_start();

// Set session timeout to 5 Hours
$session_timeout = 18000; // 5 Hours in seconds

// Check if session timeout variable exists
if (isset($_SESSION['last_activity'])) {
    // Calculate time passed
    $time_passed = time() - $_SESSION['last_activity'];

    // If time passed is greater than timeout, destroy session
    if ($time_passed > $session_timeout) {
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=1");
        exit;
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();
