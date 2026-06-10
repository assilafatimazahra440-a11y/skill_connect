<?php
// =============================================
// REQUESTS PAGE
// =============================================

$page_title  = "My Requests";
$active_page = "requests";
$is_subpage  = true;

require_once '../functions/auth.php';
require_login();

require_once '../config/db.php';

$my_id    = $_SESSION['user_id'];
$message  = $_GET['message'] ?? '';
$msg_type = $_GET['type'] ?? '';

// --- Section 1: Incoming PENDING (I am receiver) ---
$incoming_stmt = mysqli_prepare($conn,
    "SELECT sr.*, u.name AS sender_name, u.skill_teach, u.skill_learn, u.badge, u.points
     FROM skill_requests sr
     JOIN users u ON sr.sender_id = u.id
     WHERE sr.receiver_id = ? AND sr.status = 'pending'
     ORDER BY sr.created_at DESC"
);
mysqli_stmt_bind_param($incoming_stmt, "i", $my_id);
mysqli_stmt_execute($incoming_stmt);
$incoming = mysqli_fetch_all(mysqli_stmt_get_result($incoming_stmt), MYSQLI_ASSOC);

// --- Section 2: ACCEPTED / Ongoing ---
$accepted_stmt = mysqli_prepare($conn,
    "SELECT sr.*, s.name AS sender_name, r.name AS receiver_name
     FROM skill_requests sr
     JOIN users s ON sr.sender_id   = s.id
     JOIN users r ON sr.receiver_id = r.id
     WHERE (sr.sender_id = ? OR sr.receiver_id = ?) AND sr.status = 'accepted'
     ORDER BY sr.created_at DESC"
);
mysqli_stmt_bind_param($accepted_stmt, "ii", $my_id, $my_id);
mysqli_stmt_execute($accepted_stmt);
$accepted = mysqli_fetch_all(mysqli_stmt_get_result($accepted_stmt), MYSQLI_ASSOC);

// --- Section 3: My SENT pending requests ---
$sent_stmt = mysqli_prepare($conn,
    "SELECT sr.*, u.name AS receiver_name, u.skill_teach, u.badge
     FROM skill_requests sr
     JOIN users u ON sr.receiver_id = u.id
     WHERE sr.sender_id = ? AND sr.status = 'pending'
     ORDER BY sr.created_at DESC"
);
mysqli_stmt_bind_param($sent_stmt, "i", $my_id);
mysqli_stmt_execute($sent_stmt);
$sent = mysqli_fetch_all(mysqli_stmt_get_result($sent_stmt), MYSQLI_ASSOC);

// --- Section 4: COMPLETED history ---
$completed_stmt = mysqli_prepare($conn,
    "SELECT sr.*, s.name AS sender_name, r.name AS receiver_name
     FROM skill_requests sr
     JOIN users s ON sr.sender_id   = s.id
     JOIN users r ON sr.receiver_id = r.id
     WHERE (sr.sender_id = ? OR sr.receiver_id = ?) AND sr.status = 'completed'
     ORDER BY sr.created_at DESC"
);
mysqli_stmt_bind_param($completed_stmt, "ii", $my_id, $my_id);
mysqli_stmt_execute($completed_stmt);
$completed = mysqli_fetch_all(mysqli_stmt_get_result($completed_stmt), MYSQLI_ASSOC);

// --- Already rated request IDs ---
$rated_stmt = mysqli_prepare($conn, "SELECT request_id FROM ratings WHERE rater_id = ?");
mysqli_stmt_bind_param($rated_stmt, "i", $my_id);
mysqli_stmt_execute($rated_stmt);
$already_rated = array_column(
    mysqli_fetch_all(mysqli_stmt_get_result($rated_stmt), MYSQLI_ASSOC),
    'request_id'
);

require_once '../includes/header.php';
?>

<div class="container">

    <h1 class="page-title"><i class="fa-solid fa-envelope"></i> My Requests</h1>
    <p class="page-subtitle">Manage all your skill exchange requests.</p>

    <!-- Feedback alert -->
    <?php if ($message): ?>
        <div class="alert alert-<?= $msg_type === 'success' ? 'success' : 'error' ?>">
            <i class="fa-solid fa-<?= $msg_type === 'success' ? 'circle-check' : 'circle-exclamation' ?>"></i>
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>


    <!-- =========================================
         SECTION 1: INCOMING PENDING
    ========================================= -->
    <div class="section-title">
        <i class="fa-solid fa-inbox" style="color:#FFC857;"></i>
        Incoming Requests
        <span class="count"><?= count($incoming) ?></span>
    </div>

    <?php if (empty($incoming)): ?>
        <div class="card" style="color:#718096; text-align:center; padding:28px;">
            <i class="fa-solid fa-inbox" style="font-size:2rem; margin-bottom:8px; display:block; opacity:0.3;"></i>
            No incoming requests right now.
        </div>
    <?php else: ?>
        <?php foreach ($incoming as $req): ?>
        <div class="request-card incoming">
            <div class="request-header">
                <div>
                    <strong style="font-size:1rem;"><?= htmlspecialchars($req['sender_name']) ?></strong>
                    wants to exchange skills with you!
                    <br>
                    <span class="text-muted" style="font-size:0.82rem;">
                        <i class="fa-solid fa-clock"></i>
                        <?= date('d M Y', strtotime($req['created_at'])) ?>
                    </span>
                </div>
            </div>

            <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:10px;">
                <span class="skill-tag">
                    <i class="fa-solid fa-chalkboard-user"></i> <?= htmlspecialchars($req['skill_teach']) ?>
                </span>
                <span class="skill-tag learn">
                    <i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars($req['skill_learn']) ?>
                </span>
            </div>

            <?php if ($req['message']): ?>
                <p style="font-size:0.88rem; color:#4a5568; background:#f7f5ff; padding:10px 14px; border-radius:8px; margin-bottom:10px;">
                    <i class="fa-solid fa-comment"></i>
                    "<?= htmlspecialchars($req['message']) ?>"
                </p>
            <?php endif; ?>

            <div class="request-actions">
                <!-- Accept -->
                <form method="POST" action="../actions/accept_request.php">
                    <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                    <button class="btn btn-success btn-sm">
                        <i class="fa-solid fa-check"></i> Accept
                    </button>
                </form>
                <!-- Decline -->
                <form method="POST" action="../actions/cancel_request.php">
                    <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                    <button class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-xmark"></i> Decline
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>


    <!-- =========================================
         SECTION 2: ONGOING EXCHANGES
    ========================================= -->
    <div class="section-title" style="margin-top:32px;">
        <i class="fa-solid fa-handshake" style="color:#6C63FF;"></i>
        Ongoing Exchanges
        <span class="count"><?= count($accepted) ?></span>
    </div>

    <?php if (empty($accepted)): ?>
        <div class="card" style="color:#718096; text-align:center; padding:28px;">
            <i class="fa-solid fa-handshake" style="font-size:2rem; margin-bottom:8px; display:block; opacity:0.3;"></i>
            No ongoing exchanges.
        </div>
    <?php else: ?>
        <?php foreach ($accepted as $req): ?>
        <div class="request-card accepted">
            <div class="request-header">
                <div>
                    <strong><?= htmlspecialchars($req['sender_name']) ?></strong>
                    <i class="fa-solid fa-arrows-left-right" style="color:#6C63FF; margin:0 6px;"></i>
                    <strong><?= htmlspecialchars($req['receiver_name']) ?></strong>
                </div>
                <span style="background:#e8e7ff; color:#6C63FF; padding:3px 10px; border-radius:20px; font-size:0.8rem; font-weight:600;">
                    Ongoing ✅
                </span>
            </div>

            <?php if ($req['receiver_id'] == $my_id): ?>
                <div class="request-actions">
                    <form method="POST" action="../actions/complete_request.php">
                        <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                        <button class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-flag-checkered"></i> Mark as Completed
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <p class="text-muted" style="margin-top:8px; font-size:0.88rem;">
                    <i class="fa-solid fa-hourglass-half"></i>
                    Waiting for the teacher to mark as completed.
                </p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>


    <!-- =========================================
         SECTION 3: MY SENT PENDING
    ========================================= -->
    <div class="section-title" style="margin-top:32px;">
        <i class="fa-solid fa-paper-plane" style="color:#718096;"></i>
        Sent Requests
        <span class="count"><?= count($sent) ?></span>
    </div>

    <?php if (empty($sent)): ?>
        <div class="card" style="color:#718096; text-align:center; padding:28px;">
            <i class="fa-solid fa-paper-plane" style="font-size:2rem; margin-bottom:8px; display:block; opacity:0.3;"></i>
            No pending sent requests.
        </div>
    <?php else: ?>
        <?php foreach ($sent as $req): ?>
        <div class="request-card sent">
            <div class="request-header">
                <div>
                    Request sent to
                    <strong><?= htmlspecialchars($req['receiver_name']) ?></strong>
                    <br>
                    <span class="text-muted" style="font-size:0.82rem;">
                        <i class="fa-solid fa-clock"></i>
                        <?= date('d M Y', strtotime($req['created_at'])) ?>
                    </span>
                </div>
                <span style="background:#f0f0f0; color:#718096; padding:3px 10px; border-radius:20px; font-size:0.8rem; font-weight:600;">
                    Pending ⏳
                </span>
            </div>

            <div class="request-actions">
                <form method="POST" action="../actions/cancel_request.php">
                    <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                    <button class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-ban"></i> Cancel <span style="opacity:0.8;">(-5 pts)</span>
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>


    <!-- =========================================
         SECTION 4: COMPLETED HISTORY
    ========================================= -->
    <div class="section-title" style="margin-top:32px;">
        <i class="fa-solid fa-circle-check" style="color:#4CAF50;"></i>
        Completed Exchanges
        <span class="count" style="background:#4CAF50;"><?= count($completed) ?></span>
    </div>

    <?php if (empty($completed)): ?>
        <div class="card" style="color:#718096; text-align:center; padding:28px;">
            <i class="fa-solid fa-circle-check" style="font-size:2rem; margin-bottom:8px; display:block; opacity:0.3;"></i>
            No completed exchanges yet. Keep going!
        </div>
    <?php else: ?>
        <?php foreach ($completed as $req): ?>
        <div class="request-card completed">
            <div class="request-header">
                <div>
                    <strong><?= htmlspecialchars($req['sender_name']) ?></strong>
                    <i class="fa-solid fa-arrows-left-right" style="color:#4CAF50; margin:0 6px;"></i>
                    <strong><?= htmlspecialchars($req['receiver_name']) ?></strong>
                    <br>
                    <span class="text-muted" style="font-size:0.82rem;">
                        <?= date('d M Y', strtotime($req['created_at'])) ?>
                    </span>
                </div>
                <span style="background:#e8f5e9; color:#2e7d32; padding:3px 10px; border-radius:20px; font-size:0.8rem; font-weight:600;">
                    Completed ✅
                </span>
            </div>

            <!-- Rate button — only for learner (sender), only once -->
            <?php if ($req['sender_id'] == $my_id): ?>
                <?php if (!in_array($req['id'], $already_rated)): ?>
                    <div class="request-actions">
                        <a href="rate.php?request_id=<?= $req['id'] ?>&teacher_id=<?= $req['receiver_id'] ?>"
                           class="btn btn-warning btn-sm">
                            <i class="fa-solid fa-star"></i> Rate the Teacher
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-muted" style="margin-top:8px; font-size:0.85rem;">
                        <i class="fa-solid fa-star" style="color:#FFC857;"></i>
                        You already rated this exchange.
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<?php require_once '../includes/footer.php'; ?>
