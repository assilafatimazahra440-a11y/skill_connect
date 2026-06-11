<?php
// CANCEL / DECLINE REQUEST
// Used for two situations:
// 1. SENDER cancels their own pending request  → -5 points penalty
// 2. RECEIVER declines an incoming request     → no penalty

require_once '../functions/auth.php';
require_login();

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/requests.php");
    exit();
}

$my_id      = $_SESSION['user_id'];
$request_id = intval($_POST['request_id']);

// Fetch the request — make sure I am involved (sender OR receiver)
$stmt = mysqli_prepare($conn,
    "SELECT * FROM skill_requests 
     WHERE id = ? AND status = 'pending' AND (sender_id = ? OR receiver_id = ?)"
);
mysqli_stmt_bind_param($stmt, "iii", $request_id, $my_id, $my_id);
mysqli_stmt_execute($stmt);
$result  = mysqli_stmt_get_result($stmt);
$request = mysqli_fetch_assoc($result);

if (!$request) {
    header("Location: ../pages/requests.php?message=Invalid+request.&type=error");
    exit();
}

// Cancel the request
$cancel = mysqli_prepare($conn,
    "UPDATE skill_requests SET status = 'cancelled' WHERE id = ?"
);
mysqli_stmt_bind_param($cancel, "i", $request_id);
mysqli_stmt_execute($cancel);

// Apply -5 point penalty ONLY if the SENDER
// is the one cancelling (not the receiver declining)

if ($request['sender_id'] == $my_id) {
    $penalty = mysqli_prepare($conn,
        "UPDATE users SET points = points - 5 WHERE id = ?"
    );
    mysqli_stmt_bind_param($penalty, "i", $my_id);
    mysqli_stmt_execute($penalty);

    header("Location: ../pages/requests.php?message=Request+cancelled.+You+lost+5+points.&type=error");
} else {
    // Receiver declined — no penalty
    header("Location: ../pages/requests.php?message=Request+declined.&type=success");
}
exit();
?>
