<?php
// SEND SKILL EXCHANGE REQUEST
// This file runs when the user clicks
// "Send Request" on the Users page.
// It inserts a new row into skill_requests.
require_once '../functions/auth.php';
require_login();

require_once '../config/db.php';

// This page only accepts POST requests
// If someone tries to visit it directly → redirect
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/users.php");
    exit();
}

$sender_id   = $_SESSION['user_id'];
$receiver_id = intval($_POST['receiver_id']); // intval() makes sure it's a number
$message     = trim($_POST['message'] ?? '');

// --- Safety check: can't send to yourself ---
if ($sender_id === $receiver_id) {
    header("Location: ../pages/users.php?message=You+cannot+send+a+request+to+yourself.&type=error");
    exit();
}

// --- Check if a request already exists (pending or accepted) ---
$check = mysqli_prepare($conn,
    "SELECT id FROM skill_requests 
     WHERE sender_id = ? AND receiver_id = ? AND status IN ('pending', 'accepted')"
);
mysqli_stmt_bind_param($check, "ii", $sender_id, $receiver_id);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {
    header("Location: ../pages/users.php?message=You+already+sent+a+request+to+this+user.&type=error");
    exit();
}

// --- Insert the new request ---
$stmt = mysqli_prepare($conn,
    "INSERT INTO skill_requests (sender_id, receiver_id, message) VALUES (?, ?, ?)"
);
mysqli_stmt_bind_param($stmt, "iis", $sender_id, $receiver_id, $message);

if (mysqli_stmt_execute($stmt)) {
    header("Location: ../pages/users.php?message=Request+sent+successfully!&type=success");
} else {
    header("Location: ../pages/users.php?message=Something+went+wrong.+Try+again.&type=error");
}
exit();
?>
