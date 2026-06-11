<?php
$page_title  = "Browse Users";
$active_page = "users";
$is_subpage  = true;

require_once '../functions/auth.php';
require_login();

require_once '../config/db.php';

$my_id = $_SESSION['user_id'];

// Fetch all users except me, ordered by points
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id != ? ORDER BY points DESC");
mysqli_stmt_bind_param($stmt, "i", $my_id);
mysqli_stmt_execute($stmt);
$users = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

// Get IDs I already sent a pending/accepted request to
$sent_stmt = mysqli_prepare($conn,
    "SELECT receiver_id FROM skill_requests WHERE sender_id = ? AND status IN ('pending','accepted')"
);
mysqli_stmt_bind_param($sent_stmt, "i", $my_id);
mysqli_stmt_execute($sent_stmt);
$already_sent = [];
foreach (mysqli_fetch_all(mysqli_stmt_get_result($sent_stmt), MYSQLI_ASSOC) as $row) {
    $already_sent[] = $row['receiver_id'];
}

// Feedback message
$message  = $_GET['message'] ?? '';
$msg_type = $_GET['type'] ?? '';

// Badge CSS class helper
function badge_class($badge) {
    $map = [
        'Beginner'      => 'badge-beginner',
        'Helper'        => 'badge-helper',
        'Active Helper' => 'badge-active-helper',
        'Mentor'        => 'badge-mentor',
        'Expert'        => 'badge-expert',
    ];
    return $map[$badge] ?? 'badge-beginner';
}

require_once '../includes/header.php';
?>

<div class="container">

    <h1 class="page-title"><i class="fa-solid fa-users"></i> Browse Users</h1>
    <p class="page-subtitle">Find someone to exchange skills with and grow together!</p>

    <!-- Feedback alert -->
    <?php if ($message): ?>
        <div class="alert alert-<?= $msg_type === 'success' ? 'success' : 'error' ?>">
            <i class="fa-solid fa-<?= $msg_type === 'success' ? 'circle-check' : 'circle-exclamation' ?>"></i>
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Users Grid -->
    <div class="users-grid">

        <?php foreach ($users as $u):
            $letter = strtoupper(substr($u['name'], 0, 1));
            $already = in_array($u['id'], $already_sent);
        ?>

        <div class="user-card">

            <!-- Card Top: Avatar + Name + Badge -->
            <div style="display:flex; align-items:center; gap:14px; margin-bottom:6px;">
                <div class="profile-avatar" style="width:52px; height:52px; font-size:1.2rem; flex-shrink:0;">
                    <?= $letter ?>
                </div>
                <div>
                    <div class="user-name"><?= htmlspecialchars($u['name']) ?></div>
                    <span class="badge <?= badge_class($u['badge']) ?>">
                        <i class="fa-solid fa-medal"></i> <?= $u['badge'] ?>
                    </span>
                </div>
            </div>

            <!-- Skills -->
            <div style="display:flex; flex-wrap:wrap; gap:6px; margin:8px 0;">
                <span class="skill-tag">
                    <i class="fa-solid fa-chalkboard-user"></i> <?= htmlspecialchars($u['skill_teach']) ?>
                </span>
                <span class="skill-tag learn">
                    <i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars($u['skill_learn']) ?>
                </span>
            </div>

            <!-- Bio -->
            <?php if ($u['bio']): ?>
                <p class="bio"><?= htmlspecialchars(mb_strimwidth($u['bio'], 0, 90, '...')) ?></p>
            <?php endif; ?>

            <!-- Stats row -->
            <div class="stats-row">
                <span><i class="fa-solid fa-coins" style="color:#6C63FF;"></i> <?= $u['points'] ?> pts</span>
                <span><i class="fa-solid fa-star" style="color:#FFC857;"></i> <?= number_format($u['reputation'],1) ?></span>
                <span><i class="fa-solid fa-circle-check" style="color:#4CAF50;"></i> <?= $u['completed'] ?> done</span>
            </div>

            <hr class="divider">

            <!-- Send Request -->
            <?php if ($already): ?>
                <button class="btn btn-disabled btn-block" disabled>
                    <i class="fa-solid fa-clock"></i> Request Sent
                </button>
            <?php else: ?>
                <!-- Toggle form on button click -->
                <button class="btn btn-primary btn-block"
                        onclick="toggleForm(<?= $u['id'] ?>)"
                        id="btn-<?= $u['id'] ?>">
                    <i class="fa-solid fa-paper-plane"></i> Send Request
                </button>

                <!-- Hidden form, shown on click -->
                <div id="form-<?= $u['id'] ?>" style="display:none; margin-top:12px;">
                    <form method="POST" action="../actions/send_request.php">
                        <input type="hidden" name="receiver_id" value="<?= $u['id'] ?>">
                        <div class="form-group" style="margin-bottom:10px;">
                            <input type="text" name="message"
                                   placeholder="Add a message (optional)"
                                   style="font-size:0.85rem;">
                        </div>
                        <div style="display:flex; gap:8px;">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fa-solid fa-check"></i> Confirm
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm"
                                    onclick="toggleForm(<?= $u['id'] ?>)">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

        </div>

        <?php endforeach; ?>

    </div><!-- end .users-grid -->

</div>

<script>
// Simple toggle to show/hide the request form inside a card
function toggleForm(userId) {
    var form = document.getElementById('form-' + userId);
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
