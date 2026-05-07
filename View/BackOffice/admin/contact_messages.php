<?php
/**
 * APPOLIOS - Boîte de Réception (Neo Admin Pro)
 */
?>

<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 800; color: #1e293b; margin: 0 0 0.5rem 0;">Messages de Contact</h1>
        <p style="color: #64748b; margin: 0;">Gérez les communications reçues via le formulaire de contact.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <button onclick="location.reload()" class="btn-admin" style="background: white; border: 1px solid #e2e8f0; color: #475569;">
            <i class="bi bi-arrow-clockwise"></i> Rafraîchir
        </button>
    </div>
</div>

<div class="stats-grid-pro" style="margin-bottom: 2rem;">
    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background: #eef2ff; color: #4338ca;">
            <i class="bi bi-envelope-fill"></i>
        </div>
        <div>
            <div class="stat-label">Total Messages</div>
            <div class="stat-value"><?= count($messages) ?></div>
        </div>
    </div>
    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background: #fff1f2; color: #be123c;">
            <i class="bi bi-envelope-exclamation-fill"></i>
        </div>
        <div>
            <div class="stat-label">Non lus</div>
            <div class="stat-value"><?= $unreadCount ?></div>
        </div>
    </div>
    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background: #f0fdf4; color: #15803d;">
            <i class="bi bi-envelope-check-fill"></i>
        </div>
        <div>
            <div class="stat-label">Messages lus</div>
            <div class="stat-value"><?= count($messages) - $unreadCount ?></div>
        </div>
    </div>
</div>

<div class="admin-card" style="padding: 0; overflow: hidden;">
    <div style="overflow-x: auto;">
        <table class="admin-table-pro">
            <thead>
                <tr>
                    <th style="width: 60px;">Statut</th>
                    <th>Expéditeur</th>
                    <th>Sujet</th>
                    <th>Date de réception</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($messages)): ?>
                    <?php foreach ($messages as $msg): ?>
                        <tr style="<?= !$msg['is_read'] ? 'background: #f8fafc;' : '' ?>">
                            <td style="text-align: center;">
                                <?php if (!$msg['is_read']): ?>
                                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #ef4444; box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);"></span>
                                <?php else: ?>
                                    <i class="bi bi-check-circle-fill" style="color: #22c55e;"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($msg['name']) ?></div>
                                <div style="font-size: 0.75rem; color: #94a3b8;"><?= htmlspecialchars($msg['email']) ?></div>
                            </td>
                            <td>
                                <div style="<?= !$msg['is_read'] ? 'font-weight: 800; color: #1e293b;' : 'color: #64748b;' ?>">
                                    <?= htmlspecialchars($msg['subject']) ?>
                                    <?php if (!$msg['is_read']): ?>
                                        <span class="admin-badge admin-badge-danger" style="font-size: 0.6rem; margin-left: 8px;">Nouveau</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="color: #64748b; font-size: 0.9rem;"><?= date('d M, Y H:i', strtotime($msg['created_at'])) ?></td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="<?= APP_ENTRY ?>?url=admin/view-contact-message/<?= $msg['id'] ?>" class="btn-admin btn-admin-primary" style="padding: 6px 12px; font-size: 0.8rem;">
                                        <i class="bi bi-eye-fill"></i> Lire
                                    </a>
                                    <button onclick="deleteMessage(<?= $msg['id'] ?>)" class="btn-admin" style="background: #fee2e2; color: #b91c1c; border: none; padding: 6px 12px;">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; padding: 4rem; color: #94a3b8;">
                        <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 1rem;"></i>
                        Votre boîte de réception est vide.
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function deleteMessage(id) {
    Swal.fire({
        title: 'Supprimer le message ?',
        text: 'Cette action supprimera définitivement le message de la base de données.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= APP_ENTRY ?>?url=admin/delete-contact-message/' + id;
        }
    });
}
</script>
