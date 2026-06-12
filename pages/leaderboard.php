<?php

$page_title  = "Leaderboard";
$active_page = "leaderboard";
$is_subpage  = true;

require_once '../functions/auth.php';
require_login();

require_once '../config/db.php';

$my_id = $_SESSION['user_id'];

// Which sort mode? Default is 'points'
// ?sort=reputation switches to reputation mode
$sort = $_GET['sort'] ?? 'points';
if ($sort !== 'reputation') $sort = 'points'; // safety — only allow these two values

// Fetch top 20 users ordered by chosen column
$column = ($sort === 'reputation') ? 'reputation' : 'points';

$stmt = mysqli_prepare($conn,
    "SELECT id, name, skill_teach, skill_learn, points, reputation, badge, completed
     FROM users
     ORDER BY $column DESC
     LIMIT 20"
);
mysqli_stmt_execute($stmt);
$top_users = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

// Medal icons for top 3
function rank_icon($rank) {
    if ($rank === 1) return '🥇';
    if ($rank === 2) return '🥈';
    if ($rank === 3) return '🥉';
    return $rank;
}

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

    <h1 class="page-title"><i class="fa-solid fa-trophy" style="color:#FFC857;"></i> Leaderboard</h1>
    <p class="page-subtitle">The most active and skilled members of the community.</p>

    <!-- Sort toggle -->
    <div class="sort-toggle">
        <a href="leaderboard.php?sort=points"
           class="btn <?= $sort === 'points' ? 'btn-primary' : 'btn-secondary' ?>">
            <i class="fa-solid fa-coins"></i> By Points
        </a>
        <a href="leaderboard.php?sort=reputation"
           class="btn <?= $sort === 'reputation' ? 'btn-primary' : 'btn-secondary' ?>">
            <i class="fa-solid fa-star"></i> By Reputation
        </a>
    </div>

    <!-- Leaderboard Table -->
    <div class="table-scroll-wrapper">
    <table class="leaderboard-table">
        <thead>
            <tr>
                <th style="width:60px;">#</th>
                <th>User</th>
                <th>Teaches</th>
                <th>Badge</th>
                <th><?= $sort === 'reputation' ? '⭐ Reputation' : '💰 Points' ?></th>
                <th>✅ Done</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($top_users as $index => $u):
                $rank = $index + 1;
                $is_me = ($u['id'] == $my_id);
            ?>
            <tr style="<?= $is_me ? 'background:#f0eeff; font-weight:600;' : '' ?>">

                <!-- Rank -->
                <td class="rank rank-<?= $rank ?>">
                    <?= rank_icon($rank) ?>
                </td>

                <!-- Name -->
                <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <!-- Mini avatar -->
                        <div style="width:36px; height:36px; border-radius:50%;
                                    background: linear-gradient(135deg, #6C63FF, #a29bfe);
                                    display:flex; align-items:center; justify-content:center;
                                    color:#fff; font-weight:700; font-size:0.9rem; flex-shrink:0;">
                            <?= strtoupper(substr($u['name'], 0, 1)) ?>
                        </div>
                        <div>
                            <?= htmlspecialchars($u['name']) ?>
                            <?php if ($is_me): ?>
                                <span style="font-size:0.75rem; color:#6C63FF; margin-left:4px;">(You)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>

                <!-- Skill -->
                <td>
                    <span class="skill-tag" style="font-size:0.78rem;">
                        <?= htmlspecialchars($u['skill_teach']) ?>
                    </span>
                </td>

                <!-- Badge -->
                <td>
                    <span class="badge <?= badge_class($u['badge']) ?>">
                        <?= $u['badge'] ?>
                    </span>
                </td>

                <!-- Score -->
                <td style="font-weight:700; color:#6C63FF; font-size:1rem;">
                    <?php if ($sort === 'reputation'): ?>
                        <?= number_format($u['reputation'], 1) ?> ⭐
                    <?php else: ?>
                        <?= $u['points'] ?> pts
                    <?php endif; ?>
                </td>

                <!-- Completed -->
                <td style="color:#4CAF50; font-weight:600;">
                    <?= $u['completed'] ?>
                </td>

            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div><!-- /.table-scroll-wrapper -->

</div>

<?php require_once '../includes/footer.php'; ?>
