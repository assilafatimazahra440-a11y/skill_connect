<?php
$page_title  = "Rate the Teacher";
$active_page = "requests";
$is_subpage  = true;

require_once '../functions/auth.php';
require_login();

require_once '../config/db.php';

$my_id      = $_SESSION['user_id'];
$request_id = intval($_GET['request_id'] ?? 0);
$teacher_id = intval($_GET['teacher_id'] ?? 0);


// Validate: the request must exist, be completed,
// and I must be the SENDER (learner)

$stmt = mysqli_prepare($conn,
    "SELECT sr.*, u.name AS teacher_name
     FROM skill_requests sr
     JOIN users u ON sr.receiver_id = u.id
     WHERE sr.id = ? AND sr.sender_id = ? AND sr.receiver_id = ? AND sr.status = 'completed'"
);
mysqli_stmt_bind_param($stmt, "iii", $request_id, $my_id, $teacher_id);
mysqli_stmt_execute($stmt);
$result  = mysqli_stmt_get_result($stmt);
$request = mysqli_fetch_assoc($result);

// If request doesn't match — kick back
if (!$request) {
    header("Location: requests.php?message=Invalid+rating+request.&type=error");
    exit();
}

// Check if I already rated this exchange
$already = mysqli_prepare($conn,
    "SELECT id FROM ratings WHERE request_id = ? AND rater_id = ?"
);
mysqli_stmt_bind_param($already, "ii", $request_id, $my_id);
mysqli_stmt_execute($already);
mysqli_stmt_store_result($already);

if (mysqli_stmt_num_rows($already) > 0) {
    header("Location: requests.php?message=You+already+rated+this+exchange.&type=error");
    exit();
}

$teacher_name = $request['teacher_name'];

require_once '../includes/header.php';
?>

<div class="container">
    <div class="form-card" style="max-width: 520px;">

        <h1>⭐ Rate Your Teacher</h1>
        <p class="subtitle">
            How was your learning experience with
            <strong><?= htmlspecialchars($teacher_name) ?></strong>?
        </p>

        <hr class="divider">

        <form method="POST" action="../actions/submit_rating.php">
            <!-- Pass the IDs as hidden fields -->
            <input type="hidden" name="request_id" value="<?= $request_id ?>">
            <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">

            <!-- Star Rating -->
            <div class="form-group">
                <label>Your Rating *</label>
                <div class="star-rating">
                    <input type="radio" id="star5" name="stars" value="5" required>
                    <label for="star5" title="5 stars"><i class="fa-solid fa-star"></i></label>

                    <input type="radio" id="star4" name="stars" value="4">
                    <label for="star4" title="4 stars"><i class="fa-solid fa-star"></i></label>

                    <input type="radio" id="star3" name="stars" value="3">
                    <label for="star3" title="3 stars"><i class="fa-solid fa-star"></i></label>

                    <input type="radio" id="star2" name="stars" value="2">
                    <label for="star2" title="2 stars"><i class="fa-solid fa-star"></i></label>

                    <input type="radio" id="star1" name="stars" value="1">
                    <label for="star1" title="1 star"><i class="fa-solid fa-star"></i></label>
                </div>
            </div>

            <!-- Comment -->
            <div class="form-group">
                <label>Comment <span class="text-muted">(optional)</span></label>
                <textarea name="comment" placeholder='e.g. "Very helpful and patient teacher!"'></textarea>
            </div>

            <button type="submit" class="btn btn-warning btn-block">
                <i class="fa-solid fa-star"></i> Submit Rating
            </button>
        </form>

        <div class="mt-10">
            <a href="requests.php" class="text-muted">
                <i class="fa-solid fa-arrow-left"></i> Back to Requests
            </a>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
