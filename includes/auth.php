<?php
// Start session if not already started
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    // You're on localhost
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
}


if (session_status() == PHP_SESSION_NONE) {
    ob_start();
    session_start();
}

// Function to check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Function to redirect if not logged in
function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

// Function to get current user ID
function getCurrentUserId()
{
    return $_SESSION['user_id'] ?? null;
}

// Function to get current username
function getCurrentUsername()
{
    return $_SESSION['username'] ?? null;
}

// Function to check if current user is a manager
function isManager()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'manager' ||  isset($_SESSION['role']) && $_SESSION['role'] === 'super';
}
function isSuper()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'super';
}
// Function to require manager role
function requireManager()
{
    if (!isLoggedIn() || !isManager()) {
        header("Location: dashboard.php");
        exit;
    }
}

// Function to get current user role
function getCurrentUserRole()
{
    return $_SESSION['role'] ?? 'user';
}

function sendMailViaPersonal()
{
    return 1;
}
