<?php
/**
 * APPOLIOS - Candidatures Enseignants (Neo Admin Pro)
 */
?>

<div style="margin-bottom: 2.5rem;">
    <h1 style="font-size: 1.8rem; font-weight: 800; color: #1e293b; margin: 0 0 0.5rem 0;">Candidatures Enseignants</h1>
    <p style="color: #64748b; margin: 0;">Examinez et approuvez les demandes d'inscription des futurs enseignants.</p>
</div>

<!-- Stats Card -->
<div class="stats-grid-pro" style="margin-bottom: 2rem; grid-template-columns: 1fr;">
    <div class="stat-card-pro" style="background: var(--admin-active-gradient); color: white;">
        <div class="stat-icon-pro" style="background: rgba(255,255,255,0.2); color: white;">
            <i class="bi bi-person-badge-fill"></i>
        </div>
        <div>
            <div class="stat-label" style="color: rgba(255,255,255,0.8);">En attente de révision</div>
            <div class="stat-value" style="color: white;"><?= $pendingCount ?? 0 ?></div>
        </div>
    </div>
</div>

<div class="admin-card" style="padding: 0; overflow: hidden;">
    <div style="overflow-x: auto;">
        <table class="admin-table-pro">
            <thead>
                <tr>
                    <th>Candidat</th>
                    <th>Email</th>
                    <th>Curriculum Vitae</th>
                    <th>Date de dépôt</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($applications)): ?>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--admin-primary);">
                                        <?= strtoupper(substr($app['name'], 0, 1)) ?>
                                    </div>
                                    <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($app['name']) ?></div>
                                </div>
                            </td>
                            <td style="color: #64748b; font-size: 0.9rem;"><?= htmlspecialchars($app['email']) ?></td>
                            <td>
                                <a href="<?= APP_URL ?>/<?= htmlspecialchars($app['cv_path']) ?>" target="_blank" class="admin-badge admin-badge-info" style="text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                                    <i class="bi bi-file-earmark-pdf"></i> Voir le CV
                                </a>
                            </td>
                            <td style="color: #64748b; font-size: 0.9rem;"><?= date('d M, Y H:i', strtotime($app['created_at'])) ?></td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <button onclick="openApproveModal(<?= $app['id'] ?>, '<?= addslashes($app['name']) ?>')" class="btn-admin" style="background: #dcfce7; color: #15803d; border: none; padding: 6px 12px;">
                                        <i class="bi bi-check-lg"></i> Approuver
                                    </button>
                                    <button onclick="openRejectModal(<?= $app['id'] ?>, '<?= addslashes($app['name']) ?>')" class="btn-admin" style="background: #fee2e2; color: #b91c1c; border: none; padding: 6px 12px;">
                                        <i class="bi bi-x-lg"></i> Rejeter
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; padding: 4rem; color: #94a3b8;">
                        <i class="bi bi-check2-circle" style="font-size: 2rem; display: block; margin-bottom: 1rem;"></i>
                        Toutes les candidatures ont été traitées.
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modals -->
<div id="approveModal" class="admin-modal-overlay" style="display: none;">
    <div class="admin-modal-content">
        <h3 style="margin:0 0 10px; color:#1e293b; font-weight:800;">Approuver la candidature</h3>
        <p style="color:#64748b; font-size:0.9rem; margin-bottom:20px;">Voulez-vous vraiment accepter <strong><span id="approveTeacherName"></span></strong> en tant qu'enseignant ?</p>
        <form action="<?= APP_ENTRY ?>?url=admin/approve-teacher" method="POST">
            <input type="hidden" id="approveAppId" name="application_id">
            <label class="admin-label-pro">Notes administratives (optionnel)</label>
            <textarea name="admin_notes" rows="3" class="admin-input-pro" style="margin-bottom:20px;"></textarea>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('approveModal')" class="btn-admin">Annuler</button>
                <button type="submit" class="btn-admin btn-admin-primary">Confirmer l'approbation</button>
            </div>
        </form>
    </div>
</div>

<div id="rejectModal" class="admin-modal-overlay" style="display: none;">
    <div class="admin-modal-content">
        <h3 style="margin:0 0 10px; color:#ef4444; font-weight:800;">Rejeter la candidature</h3>
        <p style="color:#64748b; font-size:0.9rem; margin-bottom:20px;">Indiquez la raison du rejet pour <strong><span id="rejectTeacherName"></span></strong>.</p>
        <form action="<?= APP_ENTRY ?>?url=admin/reject-teacher" method="POST">
            <input type="hidden" id="rejectAppId" name="application_id">
            <label class="admin-label-pro">Raison du rejet (obligatoire)</label>
            <textarea name="admin_notes" rows="3" class="admin-input-pro" required style="margin-bottom:20px;"></textarea>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('rejectModal')" class="btn-admin">Annuler</button>
                <button type="submit" class="btn-admin" style="background: #ef4444; color: white;">Confirmer le rejet</button>
            </div>
        </form>
    </div>
</div>

<style>
.admin-modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 9999; display: flex; align-items: center; justify-content: center; }
.admin-modal-content { background: white; border-radius: 20px; padding: 2rem; width: 95%; max-width: 480px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
.admin-label-pro { display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 8px; }
.admin-input-pro { width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.9rem; background: white; outline: none; resize: vertical; }
</style>

<script>
function openApproveModal(appId, name) {
    document.getElementById('approveAppId').value = appId;
    document.getElementById('approveTeacherName').textContent = name;
    document.getElementById('approveModal').style.display = 'flex';
}
function openRejectModal(appId, name) {
    document.getElementById('rejectAppId').value = appId;
    document.getElementById('rejectTeacherName').textContent = name;
    document.getElementById('rejectModal').style.display = 'flex';
}
function closeModal(id) { document.getElementById(id).style.display = 'none'; }
</script>
