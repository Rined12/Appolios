<?php
/**
 * APPOLIOS - Gestion des Enseignants (Neo Admin Pro)
 */
?>

<div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 800; color: #1e293b; margin: 0 0 0.5rem 0;">Gestion des Enseignants</h1>
        <p style="color: #64748b; margin: 0;">Administrez les comptes du corps enseignant.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="<?= APP_ENTRY ?>?url=admin/export-teachers-pdf" class="btn-admin" style="background: white; border: 1px solid #e2e8f0; color: #475569;">
            <i class="bi bi-file-earmark-pdf-fill"></i> Exporter PDF
        </a>
        <a href="<?= APP_ENTRY ?>?url=admin/add-teacher" class="btn-admin btn-admin-primary" style="background: var(--admin-secondary);">
            <i class="bi bi-person-plus-fill"></i> Ajouter un Enseignant
        </a>
    </div>
</div>

<!-- Search Panel (Modernized) -->
<div id="search-panel-pro" class="admin-card" style="margin-bottom: 2rem; border-left: 4px solid var(--admin-primary); padding: 1.2rem;">
    <div style="display: flex; gap: 120px; align-items: center;">
        <div style="width: 450px; position: relative;">
            <i class="bi bi-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
            <input type="text" id="teacher-search-input" placeholder="Rechercher par nom ou email..." 
                   onkeyup="filterTeachersPro()" 
                   style="width: 100%; padding: 10px 12px 10px 45px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.95rem; outline: none; transition: all 0.2s; background: #f8fafc;">
        </div>

        <div style="width: 180px;">
            <select id="sort-filter" onchange="filterTeachersPro()" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 12px; background: white; color: #475569; font-size: 0.9rem; outline: none; cursor: pointer;">
                <option value="newest">🕒 Plus récents</option>
                <option value="oldest">⌛ Plus anciens</option>
                <option value="az">🔤 Nom: A-Z</option>
                <option value="za">🔤 Nom: Z-A</option>
            </select>
        </div>
    </div>
</div>

<div class="admin-card" style="padding: 0; overflow: hidden;">
    <div class="admin-card-header" style="padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; margin-bottom: 0;">
        <h2 class="admin-card-title">Liste des Enseignants</h2>
    </div>
    <div style="overflow-x: auto;">
        <table class="admin-table-pro" id="teachers-table-body">
            <thead>
                <tr>
                    <th>Enseignant</th>
                    <th>Email</th>
                    <th>Date d'adhésion</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($teachers)): ?>
                    <?php foreach ($teachers as $teacher): ?>
                        <tr class="teacher-row" 
                            data-name="<?= htmlspecialchars($teacher['name']) ?>" 
                            data-email="<?= htmlspecialchars($teacher['email']) ?>" 
                            data-date="<?= strtotime($teacher['created_at']) ?>">
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #e0f2fe; color: #0369a1; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                        <?= strtoupper(substr($teacher['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($teacher['name']) ?></div>
                                        <span class="admin-badge admin-badge-primary" style="font-size: 0.65rem;">Certified</span>
                                    </div>
                                </div>
                            </td>
                            <td style="color: #64748b; font-size: 0.9rem;"><?= htmlspecialchars($teacher['email']) ?></td>
                            <td style="color: #64748b; font-size: 0.9rem;"><?= date('d M, Y', strtotime($teacher['created_at'])) ?></td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <button onclick="deleteTeacher(<?= $teacher['id'] ?>, '<?= addslashes($teacher['name']) ?>')" class="btn-admin" style="background: #fee2e2; color: #b91c1c; border: none; padding: 6px 12px;">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center; padding: 3rem; color: #94a3b8;">Aucun enseignant trouvé.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filterTeachersPro() {
    const query = document.getElementById('teacher-search-input').value.toLowerCase();
    const sortVal = document.getElementById('sort-filter').value;
    const tableBody = document.querySelector('#teachers-table-body tbody');
    const rows = Array.from(tableBody.querySelectorAll('.teacher-row'));

    // 1. Filtering
    rows.forEach(row => {
        const name = row.getAttribute('data-name').toLowerCase();
        const email = row.getAttribute('data-email').toLowerCase();
        const matchesSearch = name.includes(query) || email.includes(query);
        row.style.display = matchesSearch ? '' : 'none';
    });

    // 2. Sorting
    rows.sort((a, b) => {
        if (sortVal === 'az') return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
        if (sortVal === 'za') return b.getAttribute('data-name').localeCompare(a.getAttribute('data-name'));
        if (sortVal === 'newest') return b.getAttribute('data-date') - a.getAttribute('data-date');
        if (sortVal === 'oldest') return a.getAttribute('data-date') - b.getAttribute('data-date');
        return 0;
    });

    // Re-append sorted rows
    rows.forEach(row => tableBody.appendChild(row));
}

function deleteTeacher(id, name) {
    Swal.fire({
        title: 'Supprimer l\'enseignant ?',
        text: `Êtes-vous sûr de vouloir supprimer ${name} ? Cette action est irréversible.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= APP_ENTRY ?>?url=admin/delete-user/' + id;
        }
    });
}
</script>

