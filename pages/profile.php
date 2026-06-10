<?php
// =============================================
// PROFILE PAGE
// =============================================
// Shows the logged-in user's profile and
// lets them edit their name, bio, and skills.
// =============================================

$page_title  = "My Profile";
$active_page = "profile";
$is_subpage  = true;

require_once '../functions/auth.php';
require_login();

require_once '../config/db.php';

$my_id  = $_SESSION['user_id'];
$error   = "";
$success = "";

// -----------------------------------------------
// Handle profile update form submission
// -----------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name        = trim($_POST['name']);
    $bio         = trim($_POST['bio']);
    $skill_teach = trim($_POST['skill_teach']);
    $skill_learn = trim($_POST['skill_learn']);

    if (empty($name) || empty($skill_teach) || empty($skill_learn)) {
        $error = "Name, Skill to Teach and Skill to Learn are required.";
    } else {
        $stmt = mysqli_prepare($conn,
            "UPDATE users SET name = ?, bio = ?, skill_teach = ?, skill_learn = ? WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, "ssssi", $name, $bio, $skill_teach, $skill_learn, $my_id);

        if (mysqli_stmt_execute($stmt)) {
            // Update the session name too
            $_SESSION['user_name'] = $name;
            $success = "Profile updated successfully!";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}

// Fetch fresh user data (after possible update)
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $my_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Badge CSS helper
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

// Progress towards next badge
function badge_progress($completed) {
    if ($completed >= 30) return ['Expert', 100, 30, 30];
    if ($completed >= 20) return ['Mentor', (($completed - 20) / 10) * 100, $completed - 20, 10];
    if ($completed >= 10) return ['Active Helper', (($completed - 10) / 10) * 100, $completed - 10, 10];
    if ($completed >= 5)  return ['Helper', (($completed - 5) / 5) * 100, $completed - 5, 5];
    return ['Beginner', ($completed / 5) * 100, $completed, 5];
}

[$current_badge, $progress_pct, $progress_done, $progress_total] = badge_progress($user['completed']);
$avatar_letter = strtoupper(substr($user['name'], 0, 1));

require_once '../includes/header.php';
?>

<div class="container">

    <!-- Profile Header -->
    <div class="profile-header">
        <!-- Avatar -->
        <div class="profile-avatar">
            <?= $avatar_letter ?>
        </div>

        <!-- Info -->
        <div style="flex:1;">
            <h2><?= htmlspecialchars($user['name']) ?></h2>
            <span class="badge <?= badge_class($user['badge']) ?>" style="margin-bottom:8px; display:inline-block;">
                <i class="fa-solid fa-medal"></i> <?= $user['badge'] ?>
            </span>
            <?php if ($user['bio']): ?>
                <p class="text-muted" style="margin-top:6px;"><?= htmlspecialchars($user['bio']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Stats -->
        <div style="display:flex; gap:20px; flex-wrap:wrap;">
            <div class="text-center">
                <div style="font-size:1.6rem; font-weight:700; color:#6C63FF; font-family:'Poppins',sans-serif;">
                    <?= $user['points'] ?>
                </div>
                <div class="text-muted" style="font-size:0.8rem;">Points</div>
            </div>
            <div class="text-center">
                <div style="font-size:1.6rem; font-weight:700; color:#FFC857; font-family:'Poppins',sans-serif;">
                    <?= number_format($user['reputation'], 1) ?>⭐
                </div>
                <div class="text-muted" style="font-size:0.8rem;">Reputation</div>
            </div>
            <div class="text-center">
                <div style="font-size:1.6rem; font-weight:700; color:#4CAF50; font-family:'Poppins',sans-serif;">
                    <?= $user['completed'] ?>
                </div>
                <div class="text-muted" style="font-size:0.8rem;">Completed</div>
            </div>
        </div>
    </div>

    <!-- Badge Progress -->
    <div class="card" style="margin-bottom:24px;">
        <h3><i class="fa-solid fa-medal" style="color:#FFC857;"></i> Badge Progress</h3>
        <hr class="divider">
        <p class="text-muted" style="margin-bottom:10px; font-size:0.88rem;">
            <?php if ($progress_pct >= 100): ?>
                🎉 You have reached the <strong><?= $user['badge'] ?></strong> badge!
            <?php else: ?>
                <?= $progress_done ?> / <?= $progress_total ?> exchanges to next badge
            <?php endif; ?>
        </p>
        <!-- Progress bar -->
        <div style="background:#e2e8f0; border-radius:20px; height:12px; overflow:hidden;">
            <div style="background: linear-gradient(90deg, #6C63FF, #a29bfe);
                        width: <?= min($progress_pct, 100) ?>%;
                        height:100%;
                        border-radius:20px;
                        transition: width 0.4s ease;">
            </div>
        </div>

        <!-- All badge levels -->
        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:14px;">
            <span class="badge badge-beginner">Beginner (0)</span>
            <span class="badge badge-helper">Helper (5)</span>
            <span class="badge badge-active-helper">Active Helper (10)</span>
            <span class="badge badge-mentor">Mentor (20)</span>
            <span class="badge badge-expert">Expert (30)</span>
        </div>
    </div>

    <!-- Edit Profile Form -->
    <div class="card">
        <h3><i class="fa-solid fa-user-pen"></i> Edit Profile</h3>
        <hr class="divider">

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">
                <label><i class="fa-solid fa-user"></i> Full Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-chalkboard-user"></i> Skill I Teach *</label>
                <input type="text" name="skill_teach" value="<?= htmlspecialchars($user['skill_teach']) ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-graduation-cap"></i> Skill I Want to Learn *</label>
                <input type="text" name="skill_learn" value="<?= htmlspecialchars($user['skill_learn']) ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-pen"></i> Bio <span class="text-muted">(optional)</span></label>
                <textarea name="bio" placeholder="Tell the community about yourself..."><?= htmlspecialchars($user['bio']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Save Changes
            </button>

        </form>
    </div>

</div>

<?php require_once '../includes/footer.php'; ?>
