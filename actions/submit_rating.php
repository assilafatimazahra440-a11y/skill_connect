<?php
// SUBMIT RATING
// Handles the star rating form submission.
// Steps:
// 1. Save the rating in the ratings table
// 2. Recalculate the teacher's reputation
//    (average of all their ratings)
// 3. Update the teacher's reputation in users
require_once '../functions/auth.php';
require_login();

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/requests.php");
    exit();
}

$rater_id   = $_SESSION['user_id']; // the learner giving the rating
$request_id = intval($_POST['request_id']);
$teacher_id = intval($_POST['teacher_id']); // the teacher being rated
$stars      = intval($_POST['stars']);
$comment    = trim($_POST['comment'] ?? '');

// --- Validate stars value (must be 1 to 5) ---
if ($stars < 1 || $stars > 5) {
    header("Location: ../pages/rate.php?request_id=$request_id&teacher_id=$teacher_id&message=Please+select+a+star+rating.");
    exit();
}

// --- Make sure the request is valid ---
// The rater must be the sender, and the request must be completed
$check = mysqli_prepare($conn,
    "SELECT id FROM skill_requests
     WHERE id = ? AND sender_id = ? AND receiver_id = ? AND status = 'completed'"
);
mysqli_stmt_bind_param($check, "iii", $request_id, $rater_id, $teacher_id);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) === 0) {
    header("Location: ../pages/requests.php?message=Invalid+rating.&type=error");
    exit();
}

// --- Check for duplicate rating ---
$dup = mysqli_prepare($conn,
    "SELECT id FROM ratings WHERE request_id = ? AND rater_id = ?"
);
mysqli_stmt_bind_param($dup, "ii", $request_id, $rater_id);
mysqli_stmt_execute($dup);
mysqli_stmt_store_result($dup);

if (mysqli_stmt_num_rows($dup) > 0) {
    header("Location: ../pages/requests.php?message=You+already+rated+this+exchange.&type=error");
    exit();
}

// --- Step 1: Insert the rating ---
$insert = mysqli_prepare($conn,
    "INSERT INTO ratings (request_id, rater_id, rated_id, stars, comment)
     VALUES (?, ?, ?, ?, ?)"
);
mysqli_stmt_bind_param($insert, "iiiis", $request_id, $rater_id, $teacher_id, $stars, $comment);
mysqli_stmt_execute($insert);

// --- Step 2: Recalculate teacher's reputation ---
// We take the AVERAGE of all star ratings they ever received
$avg_stmt = mysqli_prepare($conn,
    "SELECT AVG(stars) AS avg_stars FROM ratings WHERE rated_id = ?"
);
mysqli_stmt_bind_param($avg_stmt, "i", $teacher_id);
mysqli_stmt_execute($avg_stmt);
$avg_result  = mysqli_stmt_get_result($avg_stmt);
$avg_row     = mysqli_fetch_assoc($avg_result);
$new_rep     = round($avg_row['avg_stars'], 1); // round to 1 decimal: e.g. 4.7

// --- Step 3: Update the teacher's reputation in users table ---
$update_rep = mysqli_prepare($conn,
    "UPDATE users SET reputation = ? WHERE id = ?"
);
mysqli_stmt_bind_param($update_rep, "di", $new_rep, $teacher_id);
mysqli_stmt_execute($update_rep);

// Success!
header("Location: ../pages/requests.php?message=Thank+you+for+your+rating!&type=success");
exit();
?>
