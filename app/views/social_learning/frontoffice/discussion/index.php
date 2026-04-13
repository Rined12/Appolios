<?php
$title = 'Discussions — APPOLIOS';
$description = 'Discussions récentes';
$studentSidebarActive = 'discussions';
$slBase = APP_URL . '/index.php?url=social-learning/';
require __DIR__ . '/../../../partials/header.php';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/../../../student/partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;margin-bottom:30px;">
                    <div>
                        <h1 style="margin:0;font-size:1.8rem;">
                            <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:8px;color:var(--secondary-color)"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2z"/></svg>
                            Discussions récentes
                        </h1>
                        <p style="margin:5px 0 0;color:var(--gray-dark);">Échangez avec la communauté</p>
                    </div>
                    <a class="btn btn-primary" href="<?= $slBase ?>discussion/create">Créer une discussion</a>
                </div>

                <div class="dashboard-grid" style="margin-bottom:30px;">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                            <svg viewBox="0 0 24 24" fill="white" width="24" height="24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <div class="stat-info"><h3><?= count($discussions) ?></h3><p>Sur cette page</p></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:linear-gradient(135deg,#0ea5e9,#38bdf8);">
                            <svg viewBox="0 0 24 24" fill="white" width="24" height="24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        </div>
                        <div class="stat-info"><h3><?= (int)($total ?? 0) ?></h3><p>Total discussions</p></div>
                    </div>
                </div>

                <?php if (empty($discussions)): ?>
                <div class="table-container" style="padding:60px;text-align:center;">
                    <svg viewBox="0 0 24 24" width="64" height="64" fill="none" stroke="#ccc" stroke-width="1.5" style="margin-bottom:20px;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2z"/></svg>
                    <p style="color:var(--gray-dark);font-size:1.1rem;margin-bottom:20px;">Aucune discussion pour le moment.</p>
                    <a href="<?= $slBase ?>discussion/create" class="btn btn-primary">Créer une discussion</a>
                </div>
                <?php else: ?>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:24px;">
                    <?php foreach ($discussions as $d): ?>
                    <div class="sl-card">
                        <div class="sl-card-header">
                            <div style="flex:1;min-width:0;">
                                <h3 style="margin:0;font-size:1rem;"><?= htmlspecialchars($d['titre']) ?></h3>
                                <small style="color:var(--gray-dark);">
                                    <?php if (!empty($d['nom_groupe'])): ?><span style="font-weight:500;"><?= htmlspecialchars($d['nom_groupe']) ?></span> · <?php endif; ?>
                                    <?= !empty($d['date_creation']) ? date('d/m/Y H:i', strtotime($d['date_creation'])) : '' ?>
                                </small>
                            </div>
                        </div>
                        <p style="color:var(--gray-dark);font-size:0.9rem;margin:12px 0;line-height:1.5;">
                            <?= nl2br(htmlspecialchars(mb_substr($d['contenu'] ?? '', 0, 200))) ?><?= mb_strlen($d['contenu'] ?? '') > 200 ? '…' : '' ?>
                        </p>
                        <a href="<?= $slBase ?>discussion/show/<?= (int)$d['id_discussion'] ?>" class="btn btn-secondary" style="width:100%;text-align:center;padding:8px 12px;font-size:0.85rem;">Lire la discussion</a>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($totalPages) && $totalPages > 1): ?>
                <div class="sl-pagination" style="margin-top:24px;">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="<?= APP_URL ?>/index.php?url=social-learning/discussion&page=<?= $p ?>" class="sl-page-btn <?= isset($currentPage) && $p === $currentPage ? 'active' : '' ?>"><?= $p ?></a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../partials/footer.php'; ?>
