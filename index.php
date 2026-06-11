<?php
$page_title  = "Dashboard";
$active_page = "home";

require_once 'functions/auth.php';
require_login();

require_once 'config/db.php';

$user_id = $_SESSION['user_id'];

// Fetch the logged-in user's full data
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Count pending incoming requests (for the badge on nav)
$pending_stmt = mysqli_prepare($conn,
    "SELECT COUNT(*) AS cnt FROM skill_requests WHERE receiver_id = ? AND status = 'pending'"
);
mysqli_stmt_bind_param($pending_stmt, "i", $user_id);
mysqli_stmt_execute($pending_stmt);
$pending_row   = mysqli_fetch_assoc(mysqli_stmt_get_result($pending_stmt));
$pending_count = $pending_row['cnt'];

// Helper: get badge CSS class from badge name
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

// First letter of name for avatar
$avatar_letter = strtoupper(substr($user['name'], 0, 1));

require_once 'includes/header.php';
?>

<div class="container">

    <!-- Welcome Banner -->
    <div class="card" style="background: linear-gradient(135deg, #6C63FF, #a29bfe); color: #fff; margin-bottom: 28px;">
        <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
            <!-- Avatar circle with first letter -->
            <div class="profile-avatar" style="background: rgba(255,255,255,0.25); font-size: 1.8rem;">
                <?= $avatar_letter ?>
            </div>
            <div>
                <h1 style="color:#fff; font-size:1.6rem; margin-bottom:4px;">
                    Welcome back, <?= htmlspecialchars(explode(' ', $user['name'])[0]) ?>! 👋
                </h1>
                <p style="opacity:0.9; font-size:0.95rem;">
                    Ready to exchange skills today?
                </p>
                <!-- Badge -->
                <span class="badge" style="background:rgba(255,255,255,0.25); color:#fff; margin-top:6px; display:inline-block;">
                    <i class="fa-solid fa-medal"></i> <?= $user['badge'] ?>
                </span>
            </div>

            <!-- Pending requests alert -->
            <?php if ($pending_count > 0): ?>
                <div style="margin-left:auto;">
                    <a href="pages/requests.php" style="background:rgba(255,255,255,0.2); color:#fff; padding:10px 18px; border-radius:10px; font-weight:600; text-decoration:none; display:inline-block;">
                        <i class="fa-solid fa-envelope"></i>
                        <?= $pending_count ?> new request<?= $pending_count > 1 ? 's' : '' ?>!
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-number" style="color:#6C63FF;">
                <i class="fa-solid fa-coins" style="font-size:1.4rem;"></i>
                <?= $user['points'] ?>
            </div>
            <div class="stat-label">Points</div>
        </div>

        <div class="stat-box">
            <div class="stat-number" style="color:#FFC857;">
                <i class="fa-solid fa-star" style="font-size:1.4rem;"></i>
                <?= number_format($user['reputation'], 1) ?>
            </div>
            <div class="stat-label">Reputation</div>
        </div>

        <div class="stat-box">
            <div class="stat-number" style="color:#4CAF50;">
                <?= $user['completed'] ?>
            </div>
            <div class="stat-label">Completed Exchanges</div>
        </div>

        <div class="stat-box">
            <div class="stat-number" style="color:#e74c3c;">
                <?= $pending_count ?>
            </div>
            <div class="stat-label">Pending Requests</div>
        </div>
    </div>

    <!-- Skills Row -->
    <div class="card">
        <h3><i class="fa-solid fa-book-open"></i> My Skills</h3>
        <hr class="divider">
        <div style="display:flex; gap:20px; flex-wrap:wrap; margin-top:10px;">
            <div>
                <p class="text-muted" style="margin-bottom:4px;">I TEACH</p>
                <span class="skill-tag">
                    <i class="fa-solid fa-chalkboard-user"></i>
                    <?= htmlspecialchars($user['skill_teach']) ?>
                </span>
            </div>
            <div>
                <p class="text-muted" style="margin-bottom:4px;">I WANT TO LEARN</p>
                <span class="skill-tag learn">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <?= htmlspecialchars($user['skill_learn']) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <h2 style="margin-bottom:16px;"><i class="fa-solid fa-bolt"></i> Quick Actions</h2>
    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px,1fr)); gap:16px; margin-bottom:30px;">

        <a href="pages/users.php" class="card" style="text-decoration:none; text-align:center; padding:28px 20px;">
            <i class="fa-solid fa-users" style="font-size:2rem; color:#6C63FF;"></i>
            <p style="font-weight:600; margin-top:10px; color:#2D3748;">Browse Users</p>
            <p class="text-muted">Find someone to exchange with</p>
        </a>

        <a href="pages/requests.php" class="card" style="text-decoration:none; text-align:center; padding:28px 20px;">
            <i class="fa-solid fa-envelope" style="font-size:2rem; color:#FFC857;"></i>
            <p style="font-weight:600; margin-top:10px; color:#2D3748;">My Requests</p>
            <p class="text-muted">View incoming & outgoing</p>
        </a>

        <a href="pages/leaderboard.php" class="card" style="text-decoration:none; text-align:center; padding:28px 20px;">
            <i class="fa-solid fa-trophy" style="font-size:2rem; color:#FFC857;"></i>
            <p style="font-weight:600; margin-top:10px; color:#2D3748;">Leaderboard</p>
            <p class="text-muted">See top users</p>
        </a>

        <a href="pages/profile.php" class="card" style="text-decoration:none; text-align:center; padding:28px 20px;">
            <i class="fa-solid fa-user-pen" style="font-size:2rem; color:#4CAF50;"></i>
            <p style="font-weight:600; margin-top:10px; color:#2D3748;">Edit Profile</p>
            <p class="text-muted">Update your skills & bio</p>
        </a>

    </div>

</div>

<?php require_once 'includes/footer.php'; ?>