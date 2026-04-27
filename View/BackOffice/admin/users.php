<?php
/**
 * APPOLIOS - Admin Users Management
 */
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php $adminSidebarActive = 'users'; require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0;">
                <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h1>Manage Users</h1>
                        <p>View and manage platform users</p>
                    </div>
                    <a href="javascript:history.back()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; background: #6c757d;">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="transform: rotate(180deg);">
                            <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                        </svg>
                        Back
                    </a>
                </div>

        <div style="margin-bottom: 20px; text-align: right;">
            <a href="<?= APP_ENTRY ?>?url=admin/export-users-pdf" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                </svg>
                Export Users to PDF
            </a>
        </div>

        <!-- Search & Sort Toggle Section -->
        <div style="background: white; border-radius: 16px; padding: 1.5rem; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: 1px solid #eef2f6;">
            <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                <!-- Search Toggle -->
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-weight: 600; color: #2B4865; font-size: 0.95rem;">🔍 Search</span>
                    <label class="switch">
                        <input type="checkbox" id="searchToggle" onchange="toggleSearch()">
                        <span class="slider"></span>
                    </label>
                </div>

                <!-- Sort Toggle -->
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-weight: 600; color: #2B4865; font-size: 0.95rem;">📊 Sort</span>
                    <label class="switch">
                        <input type="checkbox" id="sortToggle" onchange="toggleSort()">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <!-- Search Input (Hidden by default) -->
            <div id="searchPanel" style="display: none; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #eef2f6;">
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <div style="flex: 1; position: relative;">
                        <svg style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <input type="text" id="userSearchInput" placeholder="Search by name, email or role..." onkeyup="filterUsers()" style="width: 100%; padding: 12px 12px 12px 44px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.95rem; transition: all 0.2s;">
                    </div>
                    <button onclick="clearSearch()" style="padding: 12px 20px; background: #f1f5f9; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; color: #64748b; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                        Clear
                    </button>
                </div>
                <p id="searchResults" style="margin: 0.5rem 0 0 0; font-size: 0.85rem; color: #64748b;"></p>
            </div>

            <!-- Sort Panel (Hidden by default) -->
            <div id="sortPanel" style="display: none; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #eef2f6;">
                <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    <select id="sortBy" onchange="sortUsers()" style="padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.95rem; background: white; cursor: pointer;">
                        <option value="">Sort by...</option>
                        <option value="name">Name (A-Z)</option>
                        <option value="nameDesc">Name (Z-A)</option>
                        <option value="email">Email (A-Z)</option>
                        <option value="role">Role</option>
                        <option value="dateNew">Date (Newest)</option>
                        <option value="dateOld">Date (Oldest)</option>
                    </select>

                    <div id="sortStats" style="font-size: 0.9rem; color: #64748b;"></div>
                </div>
            </div>
        </div>

        <style>
            /* Toggle Switch Styles */
            .switch {
                position: relative;
                display: inline-block;
                width: 50px;
                height: 26px;
            }
            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }
            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .3s;
                border-radius: 26px;
            }
            .slider:before {
                position: absolute;
                content: "";
                height: 20px;
                width: 20px;
                left: 3px;
                bottom: 3px;
                background-color: white;
                transition: .3s;
                border-radius: 50%;
            }
            .switch input:checked + .slider {
                background: linear-gradient(135deg, #548CA8 0%, #E19864 100%);
            }
            .switch input:checked + .slider:before {
                transform: translateX(24px);
            }
            #userSearchInput:focus {
                border-color: #E19864 !important;
                outline: none;
            }
            #sortBy:focus {
                border-color: #E19864 !important;
                outline: none;
            }
        </style>

        <script>
            // Toggle Search Panel
            function toggleSearch() {
                const panel = document.getElementById('searchPanel');
                const toggle = document.getElementById('searchToggle');
                panel.style.display = toggle.checked ? 'block' : 'none';
                if (toggle.checked) {
                    setTimeout(() => document.getElementById('userSearchInput').focus(), 100);
                }
            }

            // Toggle Sort Panel
            function toggleSort() {
                const panel = document.getElementById('sortPanel');
                const toggle = document.getElementById('sortToggle');
                panel.style.display = toggle.checked ? 'block' : 'none';
            }

            // Filter Users
            function filterUsers() {
                const searchTerm = document.getElementById('userSearchInput').value.toLowerCase();
                const rows = document.querySelectorAll('table tbody tr');
                let visibleCount = 0;

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                document.getElementById('searchResults').textContent =
                    searchTerm ? `Found ${visibleCount} user${visibleCount !== 1 ? 's' : ''}` : '';
            }

            // Clear Search
            function clearSearch() {
                document.getElementById('userSearchInput').value = '';
                filterUsers();
                document.getElementById('userSearchInput').focus();
            }

            // Sort Users
            function sortUsers() {
                const sortBy = document.getElementById('sortBy').value;
                if (!sortBy) return;

                const tbody = document.querySelector('table tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));

                rows.sort((a, b) => {
                    let aVal, bVal;

                    switch(sortBy) {
                        case 'name':
                            aVal = a.cells[1].textContent.trim().toLowerCase();
                            bVal = b.cells[1].textContent.trim().toLowerCase();
                            return aVal.localeCompare(bVal);
                        case 'nameDesc':
                            aVal = a.cells[1].textContent.trim().toLowerCase();
                            bVal = b.cells[1].textContent.trim().toLowerCase();
                            return bVal.localeCompare(aVal);
                        case 'email':
                            aVal = a.cells[2].textContent.trim().toLowerCase();
                            bVal = b.cells[2].textContent.trim().toLowerCase();
                            return aVal.localeCompare(bVal);
                        case 'role':
                            aVal = a.cells[3].textContent.trim().toLowerCase();
                            bVal = b.cells[3].textContent.trim().toLowerCase();
                            return aVal.localeCompare(bVal);
                        case 'dateNew':
                            aVal = new Date(a.cells[4].textContent);
                            bVal = new Date(b.cells[4].textContent);
                            return bVal - aVal;
                        case 'dateOld':
                            aVal = new Date(a.cells[4].textContent);
                            bVal = new Date(b.cells[4].textContent);
                            return aVal - bVal;
                        default:
                            return 0;
                    }
                });

                rows.forEach(row => tbody.appendChild(row));

                document.getElementById('sortStats').textContent =
                    `Sorted by: ${document.getElementById('sortBy').options[document.getElementById('sortBy').selectedIndex].text}`;
            }
        </script>

        <div class="table-container">
            <div class="table-header">
                <h3 style="margin: 0;">All Users</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span style="padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; background: <?= $user['role'] === 'admin' ? 'var(--yellow)' : 'var(--secondary-color)' ?>; color: white;">
                                            <?= ucfirst(htmlspecialchars($user['role'])) ?>
                                        </span>
                                        <?php if ($user['is_blocked'] ?? 0): ?>
                                            <span style="padding: 4px 8px; border-radius: 20px; font-size: 0.7rem; background: #dc3545; color: white; margin-left: 5px;">BLOCKED</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <?php if ($user['is_blocked'] ?? 0): ?>
                                                <a href="<?= APP_ENTRY ?>?url=admin/unblock-user/<?= $user['id'] ?>" class="btn action-btn" style="padding: 5px 10px; font-size: 0.8rem; background: #28a745; color: white;" onclick="return confirm('Are you sure you want to unblock this user?')">Unblock</a>
                                            <?php else: ?>
                                                <a href="<?= APP_ENTRY ?>?url=admin/block-user/<?= $user['id'] ?>" class="btn action-btn danger" style="padding: 5px 10px; font-size: 0.8rem;" onclick="return confirm('Are you sure you want to block this user? They will not be able to access the site.')">Block</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="color: var(--gray-dark); font-size: 0.85rem;">Current User</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px;">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>