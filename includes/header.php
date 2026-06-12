<?php

$is_root = !isset($is_subpage); // set $is_subpage = true in pages inside /pages/
$root    = $is_root ? './' : '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - Skill Connect' : 'Skill Connect' ?></title>

    <!-- Google Fonts: Poppins + Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Font Awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Our stylesheet -->
    <link rel="stylesheet" href="<?= $root ?>assets/css/style.css">
</head>
<body>

<!--
     NAVBAR
 -->
<nav class="navbar">
    <!-- Logo -->
    <a href="<?= $root ?>index.php" class="logo">
        <i class="fa-solid fa-bolt"></i> Skill<span>Connect</span>
    </a>

    <!-- Hamburger button (visible on mobile only) -->
    <button class="nav-toggle" id="nav-toggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="nav-menu">
        <i class="fa-solid fa-bars" id="nav-toggle-icon"></i>
    </button>

    <!-- Nav links -->
    <ul class="nav-links" id="nav-menu">
        <li>
            <a href="<?= $root ?>index.php"
               class="<?= (isset($active_page) && $active_page === 'home') ? 'active' : '' ?>">
                <i class="fa-solid fa-house"></i> Home
            </a>
        </li>
        <li>
            <a href="<?= $root ?>pages/users.php"
               class="<?= (isset($active_page) && $active_page === 'users') ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> Browse
            </a>
        </li>
        <li>
            <a href="<?= $root ?>pages/requests.php"
               class="<?= (isset($active_page) && $active_page === 'requests') ? 'active' : '' ?>">
                <i class="fa-solid fa-envelope"></i> Requests
            </a>
        </li>
        <li>
            <a href="<?= $root ?>pages/leaderboard.php"
               class="<?= (isset($active_page) && $active_page === 'leaderboard') ? 'active' : '' ?>">
                <i class="fa-solid fa-trophy"></i> Leaderboard
            </a>
        </li>
        <li>
            <a href="<?= $root ?>pages/profile.php"
               class="<?= (isset($active_page) && $active_page === 'profile') ? 'active' : '' ?>">
                <i class="fa-solid fa-user"></i> Profile
            </a>
        </li>
        <li>
            <a href="<?= $root ?>actions/logout.php" class="logout">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </li>
    </ul>
</nav>

<script>
(function () {
    var toggle = document.getElementById('nav-toggle');
    var menu   = document.getElementById('nav-menu');
    var icon   = document.getElementById('nav-toggle-icon');
    if (!toggle || !menu) return;
    toggle.addEventListener('click', function () {
        var isOpen = menu.classList.toggle('nav-open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        icon.className = isOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
    });
    // Close menu when a link is clicked
    menu.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            menu.classList.remove('nav-open');
            toggle.setAttribute('aria-expanded', 'false');
            icon.className = 'fa-solid fa-bars';
        });
    });
}());
</script>

