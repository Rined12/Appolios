<?php
$title = $title ?? APP_NAME;
$currentUrl = (string) ($_GET['url'] ?? 'home/index');
$isAuthPage = str_starts_with($currentUrl, 'login') || str_starts_with($currentUrl, 'register') || str_starts_with($currentUrl, 'admin/login');
$role = $_SESSION['role'] ?? null;
$bodyClasses = [];

$bodyClasses[] = 'neo-brand';

if (str_starts_with($currentUrl, 'student/evenements') || str_starts_with($currentUrl, 'student/evenement')) {
    $bodyClasses[] = 'theme-student-events';
}

if (str_starts_with($currentUrl, 'home/index')) {
    $bodyClasses[] = 'theme-home-lite';
}

// apply the home-lite theme to admin/backoffice pages as requested
if (str_starts_with($currentUrl, 'admin')) {
    $bodyClasses[] = 'theme-home-lite';
}

if (
    str_starts_with($currentUrl, 'student/dashboard') ||
    str_starts_with($currentUrl, 'student/course') ||
    str_starts_with($currentUrl, 'login') ||
    str_starts_with($currentUrl, 'register')
) {
    $bodyClasses[] = 'neo-dark-ui';
}

if ($isAuthPage) {
    $bodyClasses[] = 'auth-page';
}

// apply the home-lite theme to all non-auth pages (site-wide) so front/back pages share the same palette
if (!$isAuthPage && !in_array('theme-home-lite', $bodyClasses, true)) {
    $bodyClasses[] = 'theme-home-lite';
}

$bodyClassAttr = implode(' ', $bodyClasses);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>

    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/vendor/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/vendor/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/css/style.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/css/appolios.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/css/mvc-pro.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/css/neo-ui.css">
</head>
<body class="<?= htmlspecialchars($bodyClassAttr) ?>">
<?php if (!$isAuthPage): ?>
<style>
/* Neo Header Styles */
.neo-header {
    position: sticky;
    top: 0;
    z-index: 1000;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-bottom: 1px solid rgba(233, 241, 250, 0.8);
    box-shadow: 0 4px 30px rgba(43, 72, 101, 0.05);
    transition: all 0.3s ease;
    font-family: 'Inter', sans-serif;
}

.neo-header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.8rem 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.neo-brand-logo {
    font-size: 1.5rem;
    font-weight: 900;
    letter-spacing: -0.03em;
    background: linear-gradient(135deg, #2B4865 0%, #548CA8 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: transform 0.2s ease;
}

.neo-brand-logo:hover {
    transform: scale(1.02);
}

.neo-nav {
    display: flex;
    gap: 2.5rem;
    align-items: center;
}

.neo-nav a {
    text-decoration: none;
    color: #475569;
    font-weight: 600;
    font-size: 0.95rem;
    position: relative;
    padding: 0.5rem 0;
    transition: color 0.2s ease;
}

.neo-nav a::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0%;
    height: 2px;
    background: #E19864;
    border-radius: 2px;
    transition: width 0.3s ease;
}

.neo-nav a:hover {
    color: #2B4865;
}

.neo-nav a:hover::after {
    width: 100%;
}

.neo-nav a.active {
    color: #E19864;
}

.neo-nav a.active::after {
    width: 100%;
}

.neo-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.neo-btn-primary {
    background: linear-gradient(135deg, #E19864 0%, #d9804b 100%);
    color: #fff !important;
    padding: 10px 24px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.95rem;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(225, 152, 100, 0.3);
    transition: all 0.2s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.neo-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(225, 152, 100, 0.4);
}

.neo-btn-outline {
    background: #fff;
    color: #64748b !important;
    padding: 10px 24px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.95rem;
    text-decoration: none;
    border: 1.5px solid #e2e8f0;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.neo-btn-outline:hover {
    border-color: #548CA8;
    color: #548CA8 !important;
    background: #f8fafc;
}

@media (max-width: 768px) {
    .neo-nav { display: none; }
    .neo-header-inner { padding: 0.8rem 1rem; }
}
</style>

<header class="neo-header">
    <div class="neo-header-inner">
        <a class="neo-brand-logo" href="<?= APP_ENTRY ?>?url=home/index">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #E19864;"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>
            APPOLIOS
        </a>

        <nav class="neo-nav" aria-label="Main navigation">
            <a href="<?= APP_ENTRY ?>?url=home/index" class="<?= $currentUrl === 'home/index' ? 'active' : '' ?>">Home</a>
            <a href="<?= APP_ENTRY ?>?url=courses" class="<?= $currentUrl === 'courses' ? 'active' : '' ?>">Courses</a>
            <a href="<?= APP_ENTRY ?>?url=home/about" class="<?= $currentUrl === 'home/about' ? 'active' : '' ?>">About</a>
            <a href="<?= APP_ENTRY ?>?url=home/contact" class="<?= $currentUrl === 'home/contact' ? 'active' : '' ?>">Contact</a>
            <?php if ($role === 'admin'): ?>
                <a href="<?= APP_ENTRY ?>?url=admin/dashboard" class="<?= str_starts_with($currentUrl, 'admin') ? 'active' : '' ?>">Dashboard</a>
            <?php elseif ($role === 'teacher'): ?>
                <a href="<?= APP_ENTRY ?>?url=teacher/dashboard" class="<?= str_starts_with($currentUrl, 'teacher') ? 'active' : '' ?>">Dashboard</a>
            <?php elseif ($role === 'student'): ?>
                <a href="<?= APP_ENTRY ?>?url=student/dashboard" class="<?= str_starts_with($currentUrl, 'student') ? 'active' : '' ?>">Dashboard</a>
            <?php endif; ?>
        </nav>

        <div class="neo-actions">
            <?php if (!empty($_SESSION['logged_in'])): ?>
                <a class="neo-btn-outline" href="<?= APP_ENTRY ?>?url=logout">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Logout
                </a>
            <?php else: ?>
                <a class="neo-btn-primary" href="<?= APP_ENTRY ?>?url=login">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                    Sign In
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
<?php endif; ?>
<main class="app-main <?= $isAuthPage ? 'app-main-auth' : '' ?>">
