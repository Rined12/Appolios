<?php
/**
 * APPOLIOS - Header Partial
 * Common header for all pages
 */
if (!isset($slGroupesUrlPrefix)) {
    $slGroupesUrlPrefix = (!empty($_SESSION['logged_in']) && ($_SESSION['role'] ?? '') === 'teacher')
        ? 'teacher/groupes'
        : 'student/groupes';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($description ?? 'APPOLIOS E-Learning Platform') ?>">
    <title><?= htmlspecialchars($title ?? 'APPOLIOS') ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="<?= APP_URL ?>" class="logo">
                APP<span>OLIOS</span>
            </a>

            <div class="nav-menu" id="navMenu">
                <ul class="nav-links">
                    <li><a href="<?= APP_URL ?>/index.php">Home</a></li>
                    <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
                        <li><a href="<?= APP_URL ?>/index.php?url=courses">Courses</a></li>
                    <?php endif; ?>
                    <li><a href="<?= APP_URL ?>/index.php?url=about">About</a></li>
                    <li><a href="<?= APP_URL ?>/index.php?url=contact">Contact</a></li>
                </ul>

                <div class="nav-buttons">
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <a href="<?= APP_URL ?>/index.php?url=admin/dashboard" class="btn btn-yellow">Dashboard</a>
                        <?php elseif ($_SESSION['role'] === 'teacher'): ?>
                            <a href="<?= APP_URL ?>/index.php?url=teacher/dashboard" class="btn btn-yellow">My Dashboard</a>
                        <?php else: ?>
                            <a href="<?= APP_URL ?>/index.php?url=student/espace" class="btn btn-primary">Espace</a>
                        <?php endif; ?>
                        <a href="<?= APP_URL ?>/index.php?url=logout" class="btn btn-outline">Logout</a>
                    <?php else: ?>
                        <a href="<?= APP_URL ?>/index.php?url=admin/login" class="btn btn-yellow">Administrator</a>
                        <a href="<?= APP_URL ?>/index.php?url=login" class="btn btn-secondary">Sign In</a>
                        <a href="<?= APP_URL ?>/index.php?url=register" class="btn btn-primary">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Theme Toggle Switch -->
            <div class="theme-toggle" id="themeToggle" title="Toggle Dark/Light Mode">
                <span class="icon-sun">☀️</span>
                <span class="icon-moon">🌙</span>
            </div>

            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (isset($flash) && $flash): ?>
        <div class="flash-message <?= htmlspecialchars($flash['type']) ?>" style="margin-top: 70px; margin-bottom: 0;">
            <span><?= htmlspecialchars($flash['message']) ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
        <div class="flash-message error" style="margin-top: 70px; margin-bottom: 0;">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>