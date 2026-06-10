<?php
// =============================================
// REGISTER PAGE
// =============================================

session_start();

// If already logged in, go to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db.php';

$error   = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name        = trim($_POST['name']);
    $email       = trim($_POST['email']);
    $password    = $_POST['password'];
    $skill_teach = trim($_POST['skill_teach']);
    $skill_learn = trim($_POST['skill_learn']);
    $bio         = trim($_POST['bio']);

    if (empty($name) || empty($email) || empty($password) || empty($skill_teach) || empty($skill_learn)) {
        $error = "Please fill in all required fields.";

    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";

    } else {
        // Check if email already exists
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "This email is already registered. Please login.";
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql   = "INSERT INTO users (name, email, password, skill_teach, skill_learn, bio) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt2 = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt2, "ssssss", $name, $email, $hashed_password, $skill_teach, $skill_learn, $bio);

            if (mysqli_stmt_execute($stmt2)) {
                $success = "Account created successfully! You can now login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Skill Connect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container" style="display:flex; justify-content:center; align-items:flex-start; padding-top:40px;">
    <div class="form-card" style="max-width:520px; width:100%;">

        <!-- Logo -->
        <div class="text-center" style="margin-bottom:24px;">
            <a href="../pages/login.php" style="font-family:'Poppins',sans-serif; font-size:1.5rem; font-weight:700; color:#6C63FF; text-decoration:none;">
                <i class="fa-solid fa-bolt"></i> SkillConnect
            </a>
        </div>

        <h1>Create Account</h1>
        <p class="subtitle">Join the community and start exchanging skills!</p>

        <!-- Error / Success messages -->
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <?= htmlspecialchars($success) ?>
                <a href="login.php" style="margin-left:8px; font-weight:700;">Login →</a>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST" action="">

            <div class="form-group">
                <label><i class="fa-solid fa-user"></i> Full Name *</label>
                <input type="text" name="name" placeholder="e.g. Fatima Zahra El Idrissi"
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-envelope"></i> Email *</label>
                <input type="email" name="email" placeholder="your@email.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-lock"></i> Password * <span class="text-muted">(min 6 characters)</span></label>
                <input type="password" name="password" placeholder="Create a strong password" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-chalkboard-user"></i> Skill I Can Teach *</label>
                <input type="text" name="skill_teach" placeholder="e.g. English, Python, Guitar"
                       value="<?= htmlspecialchars($_POST['skill_teach'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-graduation-cap"></i> Skill I Want to Learn *</label>
                <input type="text" name="skill_learn" placeholder="e.g. Graphic Design, Excel"
                       value="<?= htmlspecialchars($_POST['skill_learn'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-pen"></i> Short Bio <span class="text-muted">(optional)</span></label>
                <textarea name="bio" placeholder="Tell others a bit about yourself..."><?= htmlspecialchars($_POST['bio'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding:12px;">
                <i class="fa-solid fa-user-plus"></i> Create Account
            </button>

        </form>
        <?php endif; ?>

        <hr class="divider">
        <p class="text-center text-muted">
            Already have an account?
            <a href="login.php" style="font-weight:600;">Login here</a>
        </p>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
