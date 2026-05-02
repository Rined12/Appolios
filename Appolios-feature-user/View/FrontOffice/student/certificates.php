<?php
/**
 * APPOLIOS - Student Certificates Page
 */

$studentSidebarActive = 'certificates';

require_once __DIR__ . '/../../../Service/CertificateService.php';
$certService = new CertificateService();
$certificates = $certService->getUserCertificates($_SESSION['user_id'] ?? 0);
?>

<style>
.certificates-page .admin-layout { gap: 5px !important; }
.certificates-page .admin-main { gap: 5px !important; display: block !important; }
.certificates-page h1 { margin-bottom: 5px !important; }
.certificates-page p { margin-bottom: 10px !important; }
.certificates-page .card { min-height: 160px; }
.certificate-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; border-radius: 16px; }
.certificate-card h3 { color: white; }
.certificate-card .cert-code { background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 8px; font-family: monospace; }
</style>

<div class="dashboard student-events-page certificates-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main">
                <h1>My Certificates</h1>
                <p>Certificates you've earned by completing courses</p>

                <?php if (!empty($certificates)): ?>
                    <div class="cards-grid">
                        <?php foreach ($certificates as $cert): ?>
                            <div class="certificate-card">
                                <div style="font-size: 2.5rem; margin-bottom: 1rem;">🎓</div>
                                <h3><?= htmlspecialchars($cert['course_title']) ?></h3>
                                <p style="opacity: 0.9;"><?= htmlspecialchars(substr($cert['course_description'] ?? '', 0, 100)) ?>...</p>
                                <div class="cert-code"><?= htmlspecialchars($cert['certificate_code']) ?></div>
                                <p style="margin-top: 1rem; font-size: 0.85rem; opacity: 0.8;">
                                    Issued: <?= date('M d, Y', strtotime($cert['issued_at'])) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="card" style="text-align: center; padding: 2rem;">
                        <div style="font-size: 3rem;">🎓</div>
                        <h3>No Certificates Yet</h3>
                        <p>Complete courses to earn certificates!</p>
                        <a href="<?= APP_ENTRY ?>?url=student/courses" class="btn btn-primary">Browse Courses</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>