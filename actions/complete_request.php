<?php
// COMPLETE REQUEST
// When the teacher (receiver) clicks
// "Mark as Completed":
// → Teacher gets +15 points
// → Learner (sender) loses -15 points
// → Both get +1 completed exchange count
// → Both badges are updated automatically

require_once '../functions/auth.php';
require_login();

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/requests.php");
    exit();
}

$my_id      = $_SESSION['user_id'];
$request_id = intval($_POST['request_id']);

// Fetch the request
// Only the RECEIVER (teacher) can complete it
$stmt = mysqli_prepare($conn,
    "SELECT * FROM skill_requests 
     WHERE id = ? AND receiver_id = ? AND status = 'accepted'"
);
mysqli_stmt_bind_param($stmt, "ii", $request_id, $my_id);
mysqli_stmt_execute($stmt);
$result  = mysqli_stmt_get_result($stmt);
$request = mysqli_fetch_assoc($result);

if (!$request) {
    header("Location: ../pages/requests.php?message=Invalid+request.&type=error");
    exit();
}

$teacher_id = $request['receiver_id']; // me — the teacher
$learner_id = $request['sender_id'];   // the learner

// --- Step 1: Mark the request as completed ---
$complete = mysqli_prepare($conn,
    "UPDATE skill_requests SET status = 'completed' WHERE id = ?"
);
mysqli_stmt_bind_param($complete, "i", $request_id);
mysqli_stmt_execute($complete);

// --- Step 2: Teacher gets +15 points and +1 completed ---
$teacher_update = mysqli_prepare($conn,
    "UPDATE users SET points = points + 15, completed = completed + 1 WHERE id = ?"
);
mysqli_stmt_bind_param($teacher_update, "i", $teacher_id);
mysqli_stmt_execute($teacher_update);

// --- Step 3: Learner loses -15 points and +1 completed ---
$learner_update = mysqli_prepare($conn,
    "UPDATE users SET points = points - 15, completed = completed + 1 WHERE id = ?"
);
mysqli_stmt_bind_param($learner_update, "i", $learner_id);
mysqli_stmt_execute($learner_update);

// --- Step 4: Update badges for BOTH users ---
// We call our badge function for each user
update_badge($conn, $teacher_id);
update_badge($conn, $learner_id);

// Redirect with success message
header("Location: ../pages/requests.php?message=Exchange+completed!+Points+have+been+updated.&type=success");
exit();

// BADGE UPDATE FUNCTION
// Reads the user's completed count and assigns
// the correct badge. Simple if/elseif chain.
function update_badge($conn, $user_id) {
    // Get current completed count
    $stmt = mysqli_prepare($conn, "SELECT completed FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);
    $count  = $user['completed'];

    // Decide badge based on count
    if ($count >= 30) {
        $badge = 'Expert';
    } elseif ($count >= 20) {
        $badge = 'Mentor';
    } elseif ($count >= 10) {
        $badge = 'Active Helper';
    } elseif ($count >= 5) {
        $badge = 'Helper';
    } else {
        $badge = 'Beginner';
    }

    // Save the new badge
    $update = mysqli_prepare($conn, "UPDATE users SET badge = ? WHERE id = ?");
    mysqli_stmt_bind_param($update, "si", $badge, $user_id);
    mysqli_stmt_execute($update);
}
?>
