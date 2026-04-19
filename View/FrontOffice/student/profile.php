<?php
/**
 * APPOLIOS - Student Profile Page
 */
$studentSidebarActive = 'profile';
?>

<div class="dashboard student-learning-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main">
                <div class="dashboard-header">
                    <h1>Mon profil</h1>
                    <p>Consultez vos informations personnelles dans une interface plus claire et professionnelle.</p>
                </div>

                <div style="display:grid;grid-template-columns:minmax(280px,340px) minmax(0,1fr);gap:24px;">
                    <section class="student-page-card" style="text-align:center;">
                        <div style="width:120px;height:120px;background:linear-gradient(135deg,#2f80ed,#1abc9c);border-radius:50%;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;box-shadow:0 12px 24px rgba(47,128,237,0.2);">
                            <svg viewBox="0 0 24 24" width="60" height="60" fill="white">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <h3 style="font-size:1.6rem;"><?= htmlspecialchars($user['name']) ?></h3>
                        <p style="color:#64748b;margin:8px 0 14px;"><?= htmlspecialchars($user['email']) ?></p>
                        <span style="display:inline-block;padding:8px 16px;background:linear-gradient(135deg,#22c55e,#34d399);color:white;border-radius:999px;font-size:0.85rem;font-weight:700;">
                            <?= ucfirst(htmlspecialchars($user['role'])) ?>
                        </span>
                    </section>

                    <section class="student-page-card">
                        <h3 style="margin-bottom:18px;">Informations du compte</h3>

                        <div style="display:grid;gap:16px;">
                            <div>
                                <label style="display:block;margin-bottom:8px;font-weight:700;color:#1e3a6d;">Nom complet</label>
                                <div class="student-soft-box"><?= htmlspecialchars($user['name']) ?></div>
                            </div>

                            <div>
                                <label style="display:block;margin-bottom:8px;font-weight:700;color:#1e3a6d;">Adresse e-mail</label>
                                <div class="student-soft-box"><?= htmlspecialchars($user['email']) ?></div>
                            </div>

                            <div>
                                <label style="display:block;margin-bottom:8px;font-weight:700;color:#1e3a6d;">Type de compte</label>
                                <div class="student-soft-box"><?= ucfirst(htmlspecialchars($user['role'])) ?></div>
                            </div>

                            <div>
                                <label style="display:block;margin-bottom:8px;font-weight:700;color:#1e3a6d;">Membre depuis</label>
                                <div class="student-soft-box"><?= date('d/m/Y', strtotime((string) $user['created_at'])) ?></div>
                            </div>
                        </div>

                        <div style="margin-top:24px;padding-top:20px;border-top:1px solid #e2e8f0;">
                            <a href="<?= APP_ENTRY ?>?url=logout" class="btn btn-outline" style="color:#dc3545;border-color:#dc3545;">Se déconnecter</a>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>