<?php
/**
 * APPOLIOS - Admin Teachers Management
 */
?>

<div class="dashboard">
    <div class="container">
        <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Manage Teachers</h1>
                <p>View and manage teacher accounts</p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <a href="javascript:history.back()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; background: #6c757d;">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="transform: rotate(180deg);">
                        <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                    </svg>
                    Back
                </a>
                <div style="display: flex; gap: 10px;">
                <a href="<?= APP_ENTRY ?>?url=admin/export-teachers-pdf" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                        <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                    </svg>
                    Export to PDF
                </a>
                <a href="<?= APP_ENTRY ?>?url=admin/add-teacher" class="btn btn-yellow">Add New Teacher</a>
                </div>
            </div>
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
                        <input type="text" id="teacherSearchInput" placeholder="Search by name or email..." onkeyup="filterTeachers()" style="width: 100%; padding: 12px 12px 12px 44px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.95rem; transition: all 0.2s;">
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
                    <select id="sortBy" onchange="sortTeachers()" style="padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.95rem; background: white; cursor: pointer;">
                        <option value="">Sort by...</option>
                        <option value="name">Name (A-Z)</option>
                        <option value="nameDesc">Name (Z-A)</option>
                        <option value="email">Email (A-Z)</option>
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
            #teacherSearchInput:focus {
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
                    setTimeout(() => document.getElementById('teacherSearchInput').focus(), 100);
                }
            }

            // Toggle Sort Panel
            function toggleSort() {
                const panel = document.getElementById('sortPanel');
                const toggle = document.getElementById('sortToggle');
                panel.style.display = toggle.checked ? 'block' : 'none';
            }

            // Filter Teachers
            function filterTeachers() {
                const searchTerm = document.getElementById('teacherSearchInput').value.toLowerCase();
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
                    searchTerm ? `Found ${visibleCount} teacher${visibleCount !== 1 ? 's' : ''}` : '';
            }

            // Clear Search
            function clearSearch() {
                document.getElementById('teacherSearchInput').value = '';
                filterTeachers();
                document.getElementById('teacherSearchInput').focus();
            }

            // Sort Teachers
            function sortTeachers() {
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
                        case 'dateNew':
                            aVal = new Date(a.cells[3].textContent);
                            bVal = new Date(b.cells[3].textContent);
                            return bVal - aVal;
                        case 'dateOld':
                            aVal = new Date(a.cells[3].textContent);
                            bVal = new Date(b.cells[3].textContent);
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
                <h3 style="margin: 0;">All Teachers</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($teachers)): ?>
                            <?php foreach ($teachers as $teacher): ?>
                                <tr>
                                    <td><?= htmlspecialchars($teacher['id']) ?></td>
                                    <td><?= htmlspecialchars($teacher['name']) ?></td>
                                    <td><?= htmlspecialchars($teacher['email']) ?></td>
                                    <td><?= date('M d, Y', strtotime($teacher['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= APP_ENTRY ?>?url=admin/delete-user/<?= $teacher['id'] ?>" class="btn action-btn danger" style="padding: 5px 10px; font-size: 0.8rem;" onclick="return confirm('Are you sure you want to delete this teacher?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px;">No teachers found. <a href="<?= APP_ENTRY ?>?url=admin/add-teacher">Add your first teacher</a></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
