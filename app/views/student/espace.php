<?php
$studentSidebarActive = 'espace';
?>
<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                    <div>
                        <h1>Mon espace</h1>
                        <p>Bienvenue, <?= htmlspecialchars($userName ?? ($_SESSION['user_name'] ?? '')) ?>.</p>
                    </div>
                    <a href="<?= APP_URL ?>/index.php?url=logout" class="btn btn-outline" style="border-color:#dc3545;color:#dc3545;">Déconnexion</a>
                </div>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
                <div class="cards-grid student-espace-stats" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));margin:24px 0;">
                    <a href="<?= APP_URL ?>/index.php?url=student/my-courses" class="card student-stat-card" style="text-align:center;text-decoration:none;color:inherit;">
                        <h3 style="font-size:1.8rem;color:var(--primary-color);"><?= (int) ($enrollmentCount ?? 0) ?></h3>
                        <p style="color:var(--gray-dark);margin:0;">Cours suivis</p>
                    </a>
                    <a href="<?= APP_URL ?>/index.php?url=student/chapitres" class="card student-stat-card" style="text-align:center;text-decoration:none;color:inherit;">
                        <h3 style="font-size:1.8rem;color:var(--primary-color);"><?= (int) ($chapterCount ?? 0) ?></h3>
                        <p style="color:var(--gray-dark);margin:0;">Chapitres</p>
                    </a>
                    <a href="<?= APP_URL ?>/index.php?url=student/quiz" class="card student-stat-card" style="text-align:center;text-decoration:none;color:inherit;">
                        <h3 style="font-size:1.8rem;color:var(--primary-color);"><?= (int) ($quizCount ?? 0) ?></h3>
                        <p style="color:var(--gray-dark);margin:0;">Quiz disponibles</p>
                    </a>
                    <a href="<?= APP_URL ?>/index.php?url=student/evenements" class="card student-stat-card" style="text-align:center;text-decoration:none;color:inherit;">
                        <h3 style="font-size:1.8rem;color:var(--primary-color);"><?= is_array($evenements ?? null) ? count($evenements) : 0 ?></h3>
                        <p style="color:var(--gray-dark);margin:0;">Événements à venir</p>
                    </a>
                </div>
                <div class="table-container" style="margin-bottom:20px;">
                    <div class="table-header"><h3 style="margin:0;">Raccourcis</h3></div>
                    <div style="padding:20px;display:flex;flex-wrap:wrap;gap:12px;">
                        <a href="<?= APP_URL ?>/index.php?url=student/my-courses" class="btn btn-primary">Mes cours</a>
                        <a href="<?= APP_URL ?>/index.php?url=student/courses" class="btn btn-outline">Catalogue</a>
                        <a href="<?= APP_URL ?>/index.php?url=student/chapitres" class="btn btn-secondary">Chapitres</a>
                        <a href="<?= APP_URL ?>/index.php?url=student/quiz" class="btn btn-secondary">Quiz</a>
                        <a href="<?= APP_URL ?>/index.php?url=student/questions" class="btn btn-secondary">Banque de questions</a>
                        <a href="<?= APP_URL ?>/index.php?url=student/quiz-history" class="btn btn-outline">Historique quiz</a>
                        <a href="<?= APP_URL ?>/index.php?url=student/evenements" class="btn btn-yellow">Événements</a>
                    </div>
                </div>
                <?php if (!empty($evenements)): ?>
                    <div class="table-header" style="margin-top:8px;"><h3 style="margin:0;">Prochains événements</h3></div>
                    <div class="student-events-grid" style="margin-top:16px;">
                        <?php foreach (array_slice($evenements, 0, 3) as $event): ?>
                            <article class="student-event-card">
                                <h3><?= htmlspecialchars(($event['titre'] ?? '') ?: ($event['title'] ?? 'Événement')) ?></h3>
                                <a href="<?= APP_URL ?>/index.php?url=student/evenement/<?= (int) $event['id'] ?>" class="btn btn-primary btn-block">Détails</a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
