<?php
/**
 * APPOLIOS - My Participated Events
 */
$studentSidebarActive = 'my-events';
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="section student-events-hero-top">
                    <div class="student-events-hero-copy">
                        <span class="student-events-hero-kicker">My Space</span>
                        <h1>My Events</h1>
                        <p>Manage and track events you have joined or requested to join.</p>
                    </div>

                    <div class="student-events-hero-media" aria-hidden="true">
                        <div class="article student-events-visual-card student-events-visual-card-main">
                            <img src="<?= APP_URL ?>/View/assets/images/about/06.jpg" alt="Students collaborating" class="student-events-visual-img">
                        </div>
                    </div>
                </div>

                <div class="dashboard-header student-events-header">
                    <div>
                        <h2>Participations</h2>
                        <p>Total: <?= count($participations) ?> event(s)</p>
                    </div>
                </div>

                <?php if (!empty($participations)): ?>
                    <div class="student-events-grid">
                        <?php foreach ($participations as $p): ?>
                            <div class="article student-event-card">
                                <div class="student-event-topline">
                                    <?php 
                                        $statusClass = '';
                                        $statusLabel = strtoupper($p['p_status'] ?? 'PENDING');
                                        if ($p['p_status'] === 'approved') $statusClass = 'status-approved';
                                        elseif ($p['p_status'] === 'rejected') $statusClass = 'status-rejected';
                                        else $statusClass = 'status-pending';
                                    ?>
                                    <span class="student-event-status <?= $statusClass ?>"><?= htmlspecialchars($statusLabel) ?></span>
                                    <span class="student-event-type"><?= htmlspecialchars($p['type'] ?? 'General') ?></span>
                                </div>
                                
                                <h3><?= htmlspecialchars($p['titre'] ?: $p['title']) ?></h3>
                                <p><?= htmlspecialchars(substr($p['description'], 0, 100)) ?>...</p>

                                <div class="student-event-meta">
                                    <span><strong>Date:</strong> <?= htmlspecialchars($p['date_debut'] ?: 'TBA') ?></span>
                                    <span><strong>Lieu:</strong> <?= htmlspecialchars($p['lieu'] ?: 'TBA') ?></span>
                                    <span><strong>Requested on:</strong> <?= date('d M Y', strtotime($p['p_date'])) ?></span>
                                </div>

                                <div style="margin-top: 15px; display: flex; gap: 10px;">
                                    <a href="<?= APP_ENTRY ?>?url=student/evenement/<?= (int) $p['id'] ?>" class="btn btn-outline btn-block" style="flex: 1; text-align: center; text-decoration: none;">Details</a>
                                    
                                    <?php if ($p['p_status'] === 'approved'): ?>
                                        <a href="<?= APP_ENTRY ?>?url=student/download-ticket/<?= (int)$p['p_id'] ?>" 
                                           class="btn btn-block" 
                                           target="_blank"
                                           style="flex: 1; text-align: center; text-decoration: none; background: #22c55e; color: white;">
                                           Download Ticket
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($p['p_status'] === 'pending'): ?>
                                        <form method="POST" action="<?= APP_ENTRY ?>?url=student/cancel-participation/<?= (int)$p['id'] ?>" style="margin:0; flex: 1;">
                                            <button type="submit" class="btn btn-danger btn-block" style="width: 100%;" onclick="return confirm('Cancel this participation?')">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="table-container student-events-empty" style="text-align: center; padding: 3rem;">
                        <div style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                        <h3>No Participations Found</h3>
                        <p>You haven't joined any events yet. Explore the catalog to find interesting sessions!</p>
                        <a href="<?= APP_ENTRY ?>?url=student/evenements" class="btn btn-primary" style="margin-top: 1.5rem; display: inline-block;">Browse Events</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.status-approved { background-color: #dcfce7; color: #166534; }
.status-rejected { background-color: #fee2e2; color: #991b1b; }
.status-pending { background-color: #fef9c3; color: #854d0e; }

.btn-danger {
    background: #ef4444;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.btn-danger:hover {
    background: #dc2626;
}
</style>
