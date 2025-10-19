<?php
// Authentication helper functions
session_start();

// Prevent caching on authenticated pages
function preventCache() {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: 0");
}

function requireAuth() {
    preventCache();
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: signin.php");
        exit();
    }
}

function requireGuest() {
    if (isset($_SESSION['user_id'])) {
        header("Location: mus_home.php");
        exit();
    }
}

// Add this new function to require admin access
function requireAdmin() {
    preventCache();
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: signin.php");
        exit();
    }
    
    if ($_SESSION['role'] !== 'admin') {
        header("Location: mus_home.php");
        exit();
    }
}

// Add this function to check if user is admin
function isAdmin() {
    return isset($_SESSION['user_id']) && ($_SESSION['role'] === 'admin');
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'user_id' => $_SESSION['user_id'],
            'name' => $_SESSION['name'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role'] ?? 'user'
        ];
    }
    return null;
}
?>