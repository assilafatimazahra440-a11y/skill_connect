<?php
// =============================================
// AUTH HELPER
// =============================================
// This file has one simple function:
// require_login() — if the user is NOT logged
// in, it kicks them to the login page.
//
// We will include this at the top of every
// page that requires a logged-in user.
// =============================================

function require_login() {
    // Start the session if it hasn't started yet
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if the user_id is saved in the session
    // If not → they are not logged in → send to login page
    if (!isset($_SESSION['user_id'])) {
        header("Location: /skill_connect/pages/login.php");
        exit();
    }
}
?>
