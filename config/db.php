<?php

// Your database settings
$host     = "localhost";   // where MySQL is running
$dbname   = "skill_connect"; // the database we created
$username = "root";        // default XAMPP username
$password = "";            // default XAMPP password (empty)

// Connect to MySQL using mysqli
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check if connection worked
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set character encoding to UTF-8
// (important for Arabic names and special characters)
mysqli_set_charset($conn, "utf8");
?>
