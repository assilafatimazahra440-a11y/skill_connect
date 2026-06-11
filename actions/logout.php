<?php
// LOGOUT
// When the user clicks "Logout":
// 1. We start the session (so we can access it)
// 2. We destroy ALL session data
// 3. We send them back to the login page
session_start();

// Erase all session variables
session_unset();

// Destroy the session completely
session_destroy();

// Send user back to login
header("Location: /skill_connect/pages/login.php");
exit();
?>
