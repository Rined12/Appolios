<?php
/**
 * APPOLIOS - Gestion des Utilisateurs (Neo Admin Pro)
 */
?>

<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 800; color: #1e293b; margin: 0 0 0.5rem 0;">Gestion des Utilisateurs</h1>
        <p style="color: #64748b; margin: 0;">Consultez et gérez tous les comptes enregistrés sur la plateforme.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="<?= APP_ENTRY ?>?url=admin/export-users-pdf" class="btn-admin" style="background: white; border: 1px solid #e2e8f0; color: #475569;">
            <i class="bi bi-file-earmark-pdf-fill"></i> Exporter PDF
        </a>
    </div>
</div>

<!-- Search Panel (Dynamic) -->
<div id="search-panel-pro" class="admin-card" style="margin-bottom: 2rem; border-left: 4px solid var(--admin-primary); padding: 1.2rem;">
    <div style="display: flex; gap: 120px; align-items: center;">


        <div style="width: 450px; position: relative;">
            <i class="bi bi-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
            <input type="text" id="user-search-input" placeholder="Rechercher par nom ou email..." 
                   onkeyup="filterUsersPro()" 
                   style="width: 100%; padding: 10px 12px 10px 45px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.95rem; outline: none; transition: all 0.2s; background: #f8fafc;">
        </div>

        <div style="width: 180px;">
            <select id="sort-filter" onchange="filterUsersPro()" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; background: white; color: #475569; font-size: 0.9rem; outline: none; cursor: pointer;">
                <option value="newest">🕒 Plus récents</option>
                <option value="oldest">⌛ Plus anciens</option>
                <option value="name-asc">🔤 Nom (A-Z)</option>
                <option value="name-desc">🔤 Nom (Z-A)</option>
            </select>
        </div>
    </div>
</div>



<div class="admin-card" style="padding: 0; overflow: hidden;">
    <div style="overflow-x: auto;">
        <table class="admin-table-pro" id="users-table">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Inscription</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr class="user-row" 
                            data-role="<?= strtolower($user['role']) ?>" 
                            data-name="<?= htmlspecialchars(strtolower($user['name'])) ?>" 
                            data-date="<?= strtotime($user['created_at']) ?>">
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--admin-primary); border: 1px solid #e2e8f0;">
                                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($user['name']) ?></div>
                                        <div style="font-size: 0.75rem; color: #94a3b8;">ID: #<?= $user['id'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="color: #64748b; font-size: 0.9rem;"><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <?php 
                                $roleClass = 'admin-badge-info';
                                if ($user['role'] === 'admin') $roleClass = 'admin-badge-warning';
                                if ($user['role'] === 'teacher') $roleClass = 'admin-badge-primary';
                                ?>
                                <span class="admin-badge <?= $roleClass ?>"><?= ucfirst(htmlspecialchars($user['role'])) ?></span>
                                
                                <?php if ($user['is_blocked'] ?? 0): ?>
                                    <span class="admin-badge admin-badge-danger" style="margin-left: 4px;">Bloqué</span>
                                <?php endif; ?>
                            </td>
                            <td style="color: #64748b; font-size: 0.9rem;"><?= date('d M, Y', strtotime($user['created_at'])) ?></td>
                            <td style="text-align: right;">
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                        <?php if ($user['is_blocked'] ?? 0): ?>
                                            <button onclick="unblockUser(<?= $user['id'] ?>, '<?= addslashes($user['name']) ?>')" class="btn-admin" style="background: #dcfce7; color: #15803d; border: none; padding: 6px 12px;">
                                                Débloquer
                                            </button>
                                        <?php else: ?>
                                            <select id="ban-duration-<?= $user['id'] ?>" class="admin-select-small">
                                                <option value="permanent">Permanent</option>
                                                <option value="2h">2 Heures</option>
                                                <option value="10h">10 Heures</option>
                                                <option value="1d">1 Jour</option>
                                            </select>
                                            <button onclick="banUser(<?= $user['id'] ?>, '<?= addslashes($user['name']) ?>')" class="btn-admin" style="background: #fee2e2; color: #b91c1c; border: none; padding: 6px 12px;">
                                                Bannir
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="font-size: 0.8rem; color: #94a3b8; font-style: italic;">Vous</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; padding: 3rem; color: #94a3b8;">Aucun utilisateur trouvé.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.admin-select-small {
    padding: 6px 10px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.8rem;
    background: white;
    outline: none;
}
.admin-select-small:focus { border-color: var(--admin-primary); }
</style>

<script>
function toggleSearchPanel() {
    const p = document.getElementById('search-panel-pro');
    p.style.display = p.style.display === 'none' ? 'block' : 'none';
    if (p.style.display === 'block') document.getElementById('user-search-input').focus();
}

function filterUsersPro() {
    const q = document.getElementById('user-search-input').value.toLowerCase();
    const s = document.getElementById('sort-filter').value;
    const table = document.getElementById('users-table');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('.user-row'));

    // 1. Filter
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const matchesQuery = text.includes(q);
        row.style.display = matchesQuery ? '' : 'none';
    });


    // 2. Sort
    rows.sort((a, b) => {
        if (s === 'name-asc') return a.dataset.name.localeCompare(b.dataset.name);
        if (s === 'name-desc') return b.dataset.name.localeCompare(a.dataset.name);
        if (s === 'newest') return b.dataset.date - a.dataset.date;
        if (s === 'oldest') return a.dataset.date - b.dataset.date;
        return 0;
    });

    // 3. Re-append
    rows.forEach(row => tbody.appendChild(row));
}


function banUser(userId, userName) {
    const duration = document.getElementById('ban-duration-' + userId).value;
    const labels = { 'permanent': 'définitivement', '2h': 'pendant 2 heures', '10h': 'pendant 10 heures', '1d': 'pendant 1 jour' };

    Swal.fire({
        title: 'Confirmer le bannissement',
        text: `Voulez-vous vraiment bannir ${userName} ${labels[duration]} ?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Oui, bannir',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= APP_ENTRY ?>?url=admin/ban-user/' + userId;
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = 'ban_duration'; input.value = duration;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function unblockUser(userId, userName) {
    Swal.fire({
        title: 'Débloquer l\'utilisateur',
        text: `Voulez-vous vraiment débloquer ${userName} ?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Oui, débloquer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= APP_ENTRY ?>?url=admin/unblock-user/' + userId;
        }
    });
}
</script>