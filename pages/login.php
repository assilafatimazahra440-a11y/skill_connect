<?php

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please enter your email and password.";

    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, name, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            header("Location: ../index.php");
            exit();
        } else {
            $error = "Incorrect email or password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Skill Connect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container" style="display:flex; justify-content:center; align-items:flex-start; padding-top:60px;">
    <div class="form-card" style="max-width:440px; width:100%;">

        <!-- Logo -->
        <div class="text-center" style="margin-bottom:28px;">
            <a href="#" style="font-family:'Poppins',sans-serif; font-size:1.7rem; font-weight:700; color:#6C63FF; text-decoration:none;">
                <i class="fa-solid fa-bolt"></i> SkillConnect
            </a>
            <p style="color:#718096; margin-top:4px; font-size:0.9rem;">Exchange skills. Grow together.</p>
        </div>

        <h1>Welcome Back!</h1>
        <p class="subtitle">Login to your account to continue.</p>

        <!-- Error message -->
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">
                <label><i class="fa-solid fa-envelope"></i> Email</label>
                <input type="email" name="email" placeholder="your@email.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-lock"></i> Password</label>
                <input type="password" name="password" placeholder="Your password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding:12px; margin-top:6px;">
                <i class="fa-solid fa-right-to-bracket"></i> Login
            </button>

        </form>

        <!-- Quick test login hint -->
        <div style="background:#f7f5ff; border-radius:10px; padding:12px 16px; margin-top:20px; font-size:0.83rem; color:#6C63FF;">
            <i class="fa-solid fa-circle-info"></i>
            <strong>Test account:</strong> fatima@skillconnect.ma / 123456
        </div>

        <hr class="divider">
        <p class="text-center text-muted">
            Don't have an account?
            <a href="register.php" style="font-weight:600;">Register here</a>
        </p>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
