<?php

// When the receiver clicks "Accept":
// → change request status to 'accepted'
require_once '../functions/auth.php';
require_login();

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/requests.php");
    exit();
}

$my_id      = $_SESSION['user_id'];
$request_id = intval($_POST['request_id']);

// Safety: make sure this request belongs to ME (I am the receiver)
// and it is still pending — no accepting already-accepted requests
$stmt = mysqli_prepare($conn,
    "SELECT id FROM skill_requests 
     WHERE id = ? AND receiver_id = ? AND status = 'pending'"
);
mysqli_stmt_bind_param($stmt, "ii", $request_id, $my_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) === 0) {
    header("Location: ../pages/requests.php?message=Invalid+request.&type=error");
    exit();
}

// Update status to 'accepted'
$update = mysqli_prepare($conn,
    "UPDATE skill_requests SET status = 'accepted' WHERE id = ?"
);
mysqli_stmt_bind_param($update, "i", $request_id);

if (mysqli_stmt_execute($update)) {
    header("Location: ../pages/requests.php?message=Request+accepted!+You+can+now+start+the+exchange.&type=success");
} else {
    header("Location: ../pages/requests.php?message=Something+went+wrong.&type=error");
}
exit();
?>
