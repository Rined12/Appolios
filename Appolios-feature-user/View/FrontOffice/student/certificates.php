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

.certificate-card { 
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); 
    color: white; 
    padding: 2rem; 
    border-radius: 20px;
    border: 2px solid #eab308;
    position: relative;
    overflow: hidden;
}
.certificate-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60"><path d="M30 0L60 30L30 60L0 30Z" fill="rgba(234,179,8,0.03)"/></svg>');
    pointer-events: none;
}
.certificate-card h3 { color: #eab308; font-size: 1.5rem; margin-bottom: 0.5rem; }
.certificate-card .cert-code { 
    background: rgba(234,179,8,0.15); 
    padding: 0.5rem 1rem; 
    border-radius: 8px; 
    font-family: monospace; 
    color: #eab308;
    display: inline-block;
    font-size: 0.9rem;
    letter-spacing: 2px;
}
.certificate-card .cert-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 3rem;
    opacity: 0.3;
}

.cert-details {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 1rem;
}
.cert-detail {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    opacity: 0.9;
}
.cert-detail-icon { font-size: 1.1rem; }
</style>

<div class="dashboard student-events-page certificates-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main">
                <h1>My Certificates</h1>
                <p>Your earned achievements from completed courses</p>

                <?php if (!empty($certificates)): ?>
                    <div class="cards-grid">
                        <?php foreach ($certificates as $cert): ?>
                            <div class="certificate-card">
                                <div class="cert-badge">🏆</div>
                                <h3><?= htmlspecialchars($cert['course_title']) ?></h3>
                                <p style="opacity: 0.8; margin-bottom: 1rem; font-size: 0.95rem;">
                                    <?= htmlspecialchars(substr($cert['course_description'] ?? 'Course completed successfully', 0, 100)) ?>
                                </p>
                                
                                <div class="cert-details">
                                    <div class="cert-detail">
                                        <span class="cert-detail-icon">🎫</span>
                                        <span><?= htmlspecialchars($cert['certificate_code']) ?></span>
                                    </div>
                                    <div class="cert-detail">
                                        <span class="cert-detail-icon">📅</span>
                                        <span><?= date('M d, Y', strtotime($cert['issued_at'])) ?></span>
                                    </div>
                                    <a href="<?= APP_ENTRY ?>?url=home/verify" target="_blank" class="cert-detail" style="text-decoration:none;color:inherit;">
                                        <span class="cert-detail-icon">🔗</span>
                                        <span>Verify</span>
                                    </a>
                                    </div>
                                </div>
                                
                                <button class="view-cert-btn" 
                                    data-code="<?= htmlspecialchars($cert['certificate_code']) ?>"
                                    data-student="<?= htmlspecialchars($cert['student_name'] ?? 'Student') ?>"
                                    data-course="<?= htmlspecialchars($cert['course_title']) ?>"
                                    data-date="<?= date('M d, Y', strtotime($cert['issued_at'])) ?>"
                                    style="margin-top:1.5rem;background:linear-gradient(135deg,#eab308,#ca8a04);border:none;color:#1a1a2e;padding:10px 20px;border-radius:8px;cursor:pointer;font-weight:700;font-size:0.9rem;width:100%">View Certificate</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="card" style="text-align: center; padding: 3rem;">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">🎓</div>
                        <h3>No Certificates Yet</h3>
                        <p style="opacity: 0.7;">Complete courses to earn your certificates!</p>
                        <a href="<?= APP_ENTRY ?>?url=student/courses" class="btn btn-primary">Browse Courses</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.view-cert-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const certHtml = `
            <div style="
                background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
                border: 8px double #1a1a2e;
                border-radius: 4px;
                padding: 2.5rem 2rem;
                text-align: center;
                position: relative;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                font-family: 'Georgia', serif;
            ">
                <!-- Corner ornaments -->
                <div style="position:absolute;top:10px;left:10px;font-size:2rem;color:#1a1a2e;">❧</div>
                <div style="position:absolute;top:10px;right:10px;font-size:2rem;color:#1a1a2e;transform:rotate(90deg);">❧</div>
                <div style="position:absolute;bottom:10px;left:10px;font-size:2rem;color:#1a1a2e;transform:rotate(270deg);">❧</div>
                <div style="position:absolute;bottom:10px;right:10px;font-size:2rem;color:#1a1a2e;transform:rotate(180deg);">❧</div>
                
                <!-- Header -->
                <div style="color:#666;font-size:0.85rem;letter-spacing:3px;text-transform:uppercase;margin-bottom:0.5rem;">APPOLIOS Learning Platform</div>
                <h1 style="color:#1a1a2e;font-size:2.2rem;margin:0 0 0.5rem 0;font-family:'Times New Roman',serif;font-weight:700;">CERTIFICATE</h1>
                <div style="color:#8b5cf6;font-size:1.1rem;letter-spacing:2px;text-transform:uppercase;font-weight:600;">of Completion</div>
                
                <!-- Divider -->
                <div style="margin:1.5rem auto;width:60%;height:2px;background:linear-gradient(90deg,transparent,#1a1a2e,transparent);"></div>
                
                <!-- Body -->
                <p style="color:#666;font-size:1rem;margin-bottom:0.5rem;font-style:italic;">This is to certify that</p>
                <h2 style="color:#1a1a2e;font-size:1.8rem;margin:0.5rem 0;border-bottom:2px solid #eab308;padding-bottom:0.5rem;display:inline-block;font-family:'Brush Script MT',cursive;">${btn.dataset.student}</h2>
                <p style="color:#666;font-size:1rem;margin:1rem 0 0.5rem 0;font-style:italic;">has successfully completed the course</p>
                <h3 style="color:#8b5cf6;font-size:1.4rem;margin:0.5rem 0;font-weight:700;">${btn.dataset.course}</h3>
                
                <!-- Divider -->
                <div style="margin:1.5rem auto;width:60%;height:1px;background:#ddd;"></div>
                
                <!-- Footer -->
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:1rem;">
                    <div style="text-align:center;">
                        <div style="width:120px;border-bottom:2px solid #1a1a2e;margin-bottom:0.5rem;"></div>
                        <div style="color:#666;font-size:0.8rem;">Date of Issue</div>
                        <div style="color:#1a1a2e;font-weight:600;">${btn.dataset.date}</div>
                    </div>
                    <div style="font-size:3rem;color:#eab308;">🏆</div>
                    <div style="text-align:center;">
                        <div style="width:120px;border-bottom:2px solid #1a1a2e;margin-bottom:0.5rem;"></div>
                        <div style="color:#666;font-size:0.8rem;">Certificate ID</div>
                        <div style="color:#1a1a2e;font-weight:600;font-family:monospace;font-size:0.85rem;">${btn.dataset.code}</div>
                    </div>
                </div>
            </div>
        `;
        Swal.fire({
            width: '600px',
            html: certHtml,
            confirmButtonColor: '#8b5cf6',
            confirmButtonText: 'Close',
            showClass: { popup: 'swal2-show' },
            hideClass: { popup: 'swal2-hide' }
        });
    });
});
</script>