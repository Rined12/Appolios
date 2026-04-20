<?php
/**
 * APPOLIOS - Student Evenements Catalog
 */
$studentSidebarActive = 'evenements';
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <div>
                        <h1>Upcoming Evenements</h1>
                        <p>Welcome <?= htmlspecialchars($userName ?? ($_SESSION['user_name'] ?? 'Student')) ?>, discover event details and resources.</p>
                    </div>
                    <a href="<?= APP_URL ?>/index.php?url=logout" class="btn btn-outline" style="border-color: #dc3545; color: #dc3545;">Logout</a>
                </div>

                <?php if (!empty($evenements)): ?>
                    <div class="student-events-grid">
                        <?php foreach ($evenements as $event): ?>
                            <article class="student-event-card">
                                <div class="student-event-topline">
                                    <span class="student-event-status"><?= htmlspecialchars(strtoupper($event['statut'] ?? 'PLANIFIE')) ?></span>
                                    <span class="student-event-type"><?= htmlspecialchars($event['type'] ?? 'General') ?></span>
                                </div>
                                <h3><?= htmlspecialchars(($event['titre'] ?? '') ?: ($event['title'] ?? 'Evenement')) ?></h3>
                                <p><?= htmlspecialchars(substr((string) ($event['description'] ?? ''), 0, 140)) ?>...</p>

                                <div class="student-event-meta">
                                    <span><strong>Date:</strong> <?= htmlspecialchars((string) (($event['date_debut'] ?? '') ?: date('Y-m-d', strtotime((string) ($event['event_date'] ?? 'now'))))) ?></span>
                                    <span><strong>Time:</strong> <?= htmlspecialchars((string) (!empty($event['heure_debut']) ? substr((string) $event['heure_debut'], 0, 5) : date('H:i', strtotime((string) ($event['event_date'] ?? 'now'))))) ?></span>
                                    <span><strong>Lieu:</strong> <?= htmlspecialchars((string) (($event['lieu'] ?? '') ?: ($event['location'] ?? 'TBA'))) ?></span>
                                    <span><strong>Ressources:</strong> <?= (int) ($event['resource_count'] ?? 0) ?></span>
                                </div>

                                <a href="<?= APP_URL ?>/index.php?url=student/evenement/<?= (int) $event['id'] ?>" class="btn btn-primary btn-block" style="margin-top: 16px;">View Details</a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="table-container" style="padding: 34px; text-align: center;">
                        <h3 style="margin-bottom: 8px;">No Evenements Yet</h3>
                        <p style="margin-bottom: 0;">You will see upcoming events here as soon as they are published.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
