<?php
$title = $title ?? APP_NAME;
$currentUrl = (string) ($_GET['url'] ?? 'home/index');
$lang = $lang ?? [];
$currentLang = $currentLang ?? 'fr';
$availableLangs = $availableLangs ?? ['fr', 'en', 'ar'];
$isRtl = ($currentLang === 'ar');
$isAuthPage = str_starts_with($currentUrl, 'login') || str_starts_with($currentUrl, 'register') || str_starts_with($currentUrl, 'admin/login');
$role = $_SESSION['role'] ?? null;
$bodyClasses = [];

$bodyClasses[] = 'neo-brand';

if (
    str_starts_with($currentUrl, 'student/evenements') || str_starts_with($currentUrl, 'student/evenement')
    || $currentUrl === 'student-quiz/quiz'
    || str_starts_with($currentUrl, 'student-quiz/quiz-history')
    || str_starts_with($currentUrl, 'student/quiz-history')
    || str_starts_with($currentUrl, 'student/profile')
    || str_starts_with($currentUrl, 'student/edit-profile')
    || str_starts_with($currentUrl, 'student/groupes')
    || str_starts_with($currentUrl, 'student/discussions')
) {
    $bodyClasses[] = 'theme-student-events';
}

$studentQuizProSurface = (
    str_starts_with($currentUrl, 'student/quiz')
    || str_starts_with($currentUrl, 'student-quiz/')
    || str_starts_with($currentUrl, 'student/coach')
    || str_starts_with($currentUrl, 'student/chapitres')
    || str_starts_with($currentUrl, 'student/questions-bank')
    || str_starts_with($currentUrl, 'student/training')
    || str_starts_with($currentUrl, 'student/remedial')
    || str_starts_with($currentUrl, 'student/quiz-history')
    || str_starts_with($currentUrl, 'teacher/quiz')
    || str_starts_with($currentUrl, 'teacher/quiz-stats')
    || str_starts_with($currentUrl, 'teacher/add-quiz')
    || str_starts_with($currentUrl, 'teacher/store-quiz')
    || str_starts_with($currentUrl, 'teacher/edit-quiz')
    || str_starts_with($currentUrl, 'teacher/update-quiz')
    || str_starts_with($currentUrl, 'teacher/delete-quiz')
    || str_starts_with($currentUrl, 'teacher/questions')
    || str_starts_with($currentUrl, 'teacher/add-question')
    || str_starts_with($currentUrl, 'teacher/edit-question')
    || str_starts_with($currentUrl, 'teacher-quiz/')
    || str_starts_with($currentUrl, 'admin-quiz/')
);

if ($studentQuizProSurface) {
    $isStudentQuizBrowse = ($currentUrl === 'student-quiz/quiz')
        || str_starts_with($currentUrl, 'student-quiz/quiz-history')
        || str_starts_with($currentUrl, 'student/quiz-history');
    if (!$isStudentQuizBrowse) {
        $bodyClasses[] = 'theme-quiz-pro';
    }
}

if (str_starts_with($currentUrl, 'home/index')) {
    $bodyClasses[] = 'theme-home-lite';
    $bodyClasses[] = 'neo-home-public';
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
<html lang="<?= htmlspecialchars($currentLang) ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/View/assets/images/branding/favicon.png">
    <link rel="apple-touch-icon" href="<?= APP_URL ?>/View/assets/images/branding/favicon.png">
    <title><?= htmlspecialchars($title) ?></title>
    <script>
        (function () {
            try {
                if (localStorage.getItem('appolios-theme') === 'dark') {
                    document.documentElement.classList.add('appolios-preload-dark');
                }
            } catch (e) {}
        })();
    </script>
    <style>html.appolios-preload-dark { background: #0f172a !important; }</style>

    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/vendor/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/vendor/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/css/style.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/css/appolios.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/css/mvc-pro.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/css/neo-ui.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/css/dark-mode.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/View/assets/css/module-focus.css">
    <style id="student-premium-force">
        /* Hard override loaded last to guarantee visible student premium theme */
        body .student-space-sidebar .student-sidebar-panel {
            background: linear-gradient(180deg, #0b1f3a 0%, #132b4f 55%, #1a3967 100%) !important;
            border: 1px solid #2a4d7d !important;
            box-shadow: 0 20px 40px rgba(12, 24, 45, 0.42) !important;
            opacity: 1 !important;
        }

        body .student-space-sidebar .student-sidebar-panel * {
            opacity: 1 !important;
        }

        body .student-space-sidebar .student-sidebar-brand-text {
            color: #f7fbff !important;
            font-weight: 900 !important;
            letter-spacing: 0.04em !important;
            text-shadow: 0 1px 0 rgba(0, 0, 0, 0.25) !important;
        }

        body .student-space-sidebar .student-sidebar-section {
            color: rgba(225, 238, 255, 0.74) !important;
            font-weight: 700 !important;
        }

        body .student-space-sidebar .admin-side-link {
            color: rgba(240, 247, 255, 0.98) !important;
            padding: 0.8rem 0.9rem !important;
            border-radius: 12px !important;
            border: 1px solid rgba(131, 168, 217, 0.24) !important;
            background: rgba(96, 137, 194, 0.14) !important;
            font-weight: 750 !important;
            font-size: 0.99rem !important;
            text-shadow: 0 1px 0 rgba(0, 0, 0, 0.2) !important;
            position: relative !important;
        }

        body .student-space-sidebar .admin-side-link .admin-side-icon {
            color: #cfe1fb !important;
        }

        body .student-space-sidebar .admin-side-link:hover {
            background: rgba(87, 151, 242, 0.32) !important;
            border-color: rgba(159, 208, 255, 0.62) !important;
            color: #ffffff !important;
            transform: translateX(2px) !important;
        }

        body .student-space-sidebar .admin-side-link.active {
            background: linear-gradient(135deg, #2f6fed 0%, #5ca0ff 55%, #72c9ff 100%) !important;
            border-color: #a8d5ff !important;
            color: #ffffff !important;
            box-shadow: 0 14px 28px rgba(47, 111, 237, 0.38) !important;
        }

        body .student-space-sidebar .admin-side-link.active::before {
            content: "" !important;
            position: absolute !important;
            left: 8px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            width: 4px !important;
            height: 22px !important;
            border-radius: 999px !important;
            background: rgba(255, 255, 255, 0.88) !important;
        }

        body .student-learning-page .dashboard-header {
            background: linear-gradient(140deg, #ffffff 0%, #f6f9ff 64%, #eef5ff 100%) !important;
            border: 1px solid #d9e5f4 !important;
            border-radius: 18px !important;
            box-shadow: 0 12px 28px rgba(19, 43, 79, 0.1) !important;
            padding: 1.1rem 1.2rem !important;
        }

        body .student-learning-page .dashboard-header h1 {
            color: #173b6d !important;
            font-weight: 900 !important;
            letter-spacing: -0.02em !important;
            margin: 0 !important;
        }

        body .student-learning-page .dashboard-header p {
            color: #4a607e !important;
        }

        body .student-learning-page .student-page-card,
        body .student-learning-page .student-course-card,
        body .student-learning-page .table-container.student-panel {
            background: #ffffff !important;
            border: 1px solid #d9e5f4 !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08) !important;
        }

        body .student-learning-page .student-course-card {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%) !important;
        }

        body .student-learning-page .student-soft-box {
            background: #f7fbff !important;
            border: 1px solid #dce8f8 !important;
            border-radius: 12px !important;
            color: #1f3555 !important;
        }

        body .student-learning-page .student-quiz-table th {
            background: #f2f7ff !important;
            color: #2b4a74 !important;
            font-weight: 800 !important;
        }

        body .student-learning-page .student-quiz-table td {
            color: #1f2f46 !important;
        }

        body .student-learning-page .student-quiz-table tr:hover {
            background: #f8fbff !important;
        }

        body .student-learning-page .student-pill {
            display: inline-flex !important;
            align-items: center !important;
            padding: 0.22rem 0.58rem !important;
            border-radius: 999px !important;
            border: 1px solid #d0e3fa !important;
            background: #eaf4ff !important;
            color: #24538d !important;
            font-weight: 700 !important;
            font-size: 0.78rem !important;
        }

        body .student-learning-page .student-pill--muted {
            border-color: #dfe7f2 !important;
            background: #f8fbff !important;
            color: #4d617c !important;
        }

        body .student-learning-page .student-chapter-details {
            border: 1px solid #dbe8f7 !important;
            border-radius: 14px !important;
            background: #ffffff !important;
            overflow: hidden !important;
            margin: 0.65rem 0.9rem !important;
        }

        body .student-learning-page .student-chapter-details > summary {
            background: linear-gradient(180deg, #ffffff 0%, #f6faff 100%) !important;
            border-bottom: 1px solid #e5eef9 !important;
            padding: 0.86rem 1rem !important;
        }

        body .student-learning-page .student-chapter-title {
            color: #1f3555 !important;
            font-weight: 800 !important;
        }

        body .student-learning-page .student-qbank-item {
            border: 1px solid #dbe8f7 !important;
            border-radius: 14px !important;
            background: #ffffff !important;
        }

        body .student-learning-page .student-qbank-summary {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%) !important;
            border-bottom: 1px solid #e4edf8 !important;
            padding: 0.9rem 1rem !important;
        }

        body .student-learning-page .student-qbank-num {
            background: linear-gradient(135deg, #dcedff 0%, #e3f3ff 100%) !important;
            border: 1px solid #cde0f7 !important;
            color: #1f4f8b !important;
            border-radius: 11px !important;
            min-width: 42px !important;
            height: 42px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-weight: 800 !important;
        }

        body .student-learning-page .btn.btn-primary {
            background: linear-gradient(135deg, #2f6fed 0%, #5ca0ff 55%, #72c9ff 100%) !important;
            border: 1px solid #7fbfff !important;
            color: #ffffff !important;
            font-weight: 800 !important;
            border-radius: 11px !important;
            box-shadow: 0 12px 24px rgba(47, 111, 237, 0.3) !important;
        }

        body .student-learning-page .btn.btn-outline {
            background: #f7fbff !important;
            border: 1px solid #cde0f6 !important;
            color: #274877 !important;
            border-radius: 11px !important;
            font-weight: 700 !important;
        }

        /* Final polish: spacing, hierarchy, premium composition */
        body .student-learning-page {
            background:
                radial-gradient(900px 420px at -8% -12%, rgba(113, 172, 255, 0.2) 0%, rgba(113, 172, 255, 0) 58%),
                radial-gradient(780px 360px at 105% 0%, rgba(114, 206, 255, 0.16) 0%, rgba(114, 206, 255, 0) 56%),
                #f5f8fd !important;
        }

        body .student-learning-page .admin-dashboard-container {
            width: min(1380px, 97vw) !important;
            padding-top: 0.7rem !important;
            padding-bottom: 0.5rem !important;
        }

        body .student-learning-page .admin-layout {
            display: grid !important;
            grid-template-columns: 300px minmax(0, 1fr) !important;
            gap: 1.2rem !important;
            align-items: start !important;
        }

        body .student-learning-page .admin-main {
            display: grid !important;
            gap: 1.25rem !important;
        }

        body .student-learning-page .student-page-card,
        body .student-learning-page .student-course-card,
        body .student-learning-page .table-container.student-panel {
            transition: transform 0.2s ease, box-shadow 0.2s ease !important;
        }

        body .student-learning-page .student-page-card:hover,
        body .student-learning-page .student-course-card:hover,
        body .student-learning-page .table-container.student-panel:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.12) !important;
        }

        body .student-learning-page .student-chapter-content,
        body .student-learning-page .student-qbank-options,
        body .student-learning-page .student-quiz-qtext {
            color: #2d3f5a !important;
            line-height: 1.68 !important;
        }

        body .student-learning-page .student-quiz-links li {
            background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%) !important;
            border: 1px solid #d9e7f7 !important;
            border-radius: 12px !important;
            padding: 0.6rem 0.72rem !important;
        }

        body .student-learning-page .student-quiz-links a {
            color: #245389 !important;
            font-weight: 800 !important;
            text-decoration: none !important;
        }

        body .student-learning-page .student-quiz-links a:hover {
            text-decoration: underline !important;
        }

        body .student-learning-page .student-quiz-meta {
            color: #60748f !important;
            font-size: 0.84rem !important;
        }

        @media (max-width: 992px) {
            body .student-learning-page .admin-layout {
                grid-template-columns: 1fr !important;
            }
            body .student-learning-page .student-space-sidebar {
                position: static !important;
                top: auto !important;
                height: auto !important;
            }
        }
    </style>
</head>
<body class="<?= htmlspecialchars($bodyClassAttr) ?>">
<script>
    (function () {
        try {
            if (localStorage.getItem('appolios-theme') === 'dark') {
                document.body.classList.add('dark-mode');
            }
        } catch (e) {}
    })();
</script>
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

.neo-theme-toggle {
    width: 44px;
    height: 44px;
    padding: 0;
    border-radius: 12px;
    border: 1.5px solid #e2e8f0;
    background: #fff;
    color: #475569;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: border-color 0.2s ease, color 0.2s ease, background 0.2s ease, transform 0.15s ease;
    flex-shrink: 0;
}

.neo-theme-toggle:hover {
    border-color: #548CA8;
    color: #2B4865;
    background: #f8fafc;
    transform: translateY(-1px);
}

.neo-theme-toggle:focus-visible {
    outline: 2px solid #E19864;
    outline-offset: 2px;
}

.neo-theme-toggle svg {
    display: block;
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
            <a href="<?= APP_ENTRY ?>?url=home/index" class="<?= $currentUrl === 'home/index' ? 'active' : '' ?>"><?= htmlspecialchars($lang['nav_home'] ?? 'Home') ?></a>
            <a href="<?= APP_ENTRY ?>?url=courses" class="<?= $currentUrl === 'courses' ? 'active' : '' ?>"><?= htmlspecialchars($lang['nav_courses'] ?? 'Courses') ?></a>
            <a href="<?= APP_ENTRY ?>?url=home/about" class="<?= $currentUrl === 'home/about' ? 'active' : '' ?>"><?= htmlspecialchars($lang['nav_about'] ?? 'About') ?></a>
            <a href="<?= APP_ENTRY ?>?url=home/contact" class="<?= $currentUrl === 'home/contact' ? 'active' : '' ?>"><?= htmlspecialchars($lang['nav_contact'] ?? 'Contact') ?></a>
            <?php if ($role === 'admin'): ?>
                <a href="<?= APP_ENTRY ?>?url=admin/dashboard" class="<?= str_starts_with($currentUrl, 'admin') ? 'active' : '' ?>"><?= htmlspecialchars($lang['nav_dashboard'] ?? 'Dashboard') ?></a>
            <?php elseif ($role === 'teacher'): ?>
                <a href="<?= APP_ENTRY ?>?url=teacher/dashboard" class="<?= str_starts_with($currentUrl, 'teacher') ? 'active' : '' ?>"><?= htmlspecialchars($lang['nav_dashboard'] ?? 'Dashboard') ?></a>
            <?php elseif ($role === 'student'): ?>
                <?php
                    $studentDashActive = $currentUrl === 'student/dashboard'
                        || str_starts_with($currentUrl, 'student/dashboard/');
                ?>
                <a href="<?= APP_ENTRY ?>?url=student/dashboard" class="<?= $studentDashActive ? 'active' : '' ?>"><?= htmlspecialchars($lang['nav_dashboard'] ?? 'Dashboard') ?></a>
            <?php endif; ?>
        </nav>

        <div class="neo-actions">
            <button type="button" class="neo-theme-toggle" id="appolios-theme-toggle" aria-label="<?= htmlspecialchars($lang['theme_toggle_aria'] ?? 'Toggle theme') ?>" aria-pressed="false" title="<?= htmlspecialchars($lang['theme_toggle_title'] ?? 'Dark mode') ?>">
                <span class="neo-theme-toggle__icon neo-theme-toggle__icon--to-dark" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                </span>
                <span class="neo-theme-toggle__icon neo-theme-toggle__icon--to-light" aria-hidden="true" hidden>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path></svg>
                </span>
            </button>
            <?php if (!empty($_SESSION['logged_in'])): ?>
                <a class="neo-btn-outline" href="<?= APP_ENTRY ?>?url=logout">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    <?= htmlspecialchars($lang['btn_logout'] ?? 'Logout') ?>
                </a>
            <?php else: ?>
                <a class="neo-btn-primary" href="<?= APP_ENTRY ?>?url=login">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                    <?= htmlspecialchars($lang['btn_sign_in'] ?? 'Sign In') ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
<?php endif; ?>
<main class="app-main <?= $isAuthPage ? 'app-main-auth' : '' ?>">
