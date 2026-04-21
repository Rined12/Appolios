<?php
/**
 * APPOLIOS - Admin Evenements Management
 */
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php $adminSidebarActive = 'evenements'; require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0;">
                
                <!-- Back Button -->
                <div style="margin-bottom: 20px;">
                    <a href="javascript:history.back()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; background: #6c757d;">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="transform: rotate(180deg);">
                            <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                        </svg>
                        Back
                    </a>
                </div>
                
                <section class="neo-auth-wrap" style="background: transparent; font-family: 'Inter', sans-serif;">
                    
                    <div class="neo-glass-card" style="width: 100%; background: #ffffff; border-radius: 20px; box-shadow: 0 15px 40px rgba(43, 72, 101, 0.08); border: 1px solid rgba(233, 241, 250, 0.8); overflow: hidden; display: flex; flex-direction: column;">
                        
                        <!-- Top Part (Split like login) -->
                        <div class="neo-auth-grid" style="display: grid; grid-template-columns: 1.2fr 1fr; border-bottom: 1px solid #eef2f6;">
                            
                            <!-- Left: Info & Actions -->
                            <div style="padding: 3.5rem; background: #fcfcfc; position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: center;">
                                <!-- Decorative blobs -->
                                <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: #e9f1fa; border-radius: 50%; z-index: 0; opacity: 0.7;"></div>
                                <div style="position: absolute; bottom: 10%; right: -30px; width: 150px; height: 150px; background: #e1edf7; border-radius: 50%; z-index: 0; opacity: 0.5;"></div>
                                
                                <div style="position: relative; z-index: 2;">
                                    <h2 style="font-size: 2.8rem; font-weight: 800; color: #2B4865; line-height: 1.15; margin: 0 0 1rem 0; letter-spacing: -0.02em;">
                                        Module<br><span style="color: #548CA8;">Evenements</span>
                                    </h2>
                                    <p style="color: #64748b; font-size: 1.1rem; line-height: 1.6; margin: 0 0 2rem 0; max-width: 90%;">
                                        Plan sessions, review teacher proposals, and keep the catalog up to date. Create and manage events seamlessly from your back-office.
                                    </p>
                                    
                                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                        <a href="<?= APP_ENTRY ?>?url=admin/dashboard" style="background: #fff; border: 1.5px solid #e2e8f0; color: #64748b; padding: 12px 24px; border-radius: 10px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='#548CA8'; this.style.color='#548CA8'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#64748b'">Back to Dashboard</a>
                                        <a href="<?= APP_ENTRY ?>?url=admin/evenement-requests" style="background: #e9f1fa; color: #548CA8; padding: 12px 24px; border-radius: 10px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#d0e3f5'" onmouseout="this.style.background='#e9f1fa'">Teacher Requests</a>
                                        <a href="<?= APP_ENTRY ?>?url=admin/add-evenement" style="background: linear-gradient(135deg, #E19864 0%, #d9804b 100%); color: #fff; padding: 12px 24px; border-radius: 10px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 15px rgba(225, 152, 100, 0.3); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(225, 152, 100, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(225, 152, 100, 0.3)'">Add Event</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: The Images -->
                            <div style="padding: 2rem; display: flex; align-items: center; justify-content: center; position: relative; background: #fff; overflow: hidden;">
                                <div style="position: absolute; top: 15%; right: 10%; width: 70%; height: 70%; background: #E19864; border-radius: 30px; transform: rotate(5deg); z-index: 0; opacity: 0.15;"></div>
                                <div style="position: absolute; bottom: 5%; left: 5%; width: 50%; height: 50%; background: #548CA8; border-radius: 30px; transform: rotate(-8deg); z-index: 0; opacity: 0.1;"></div>
                                
                                <div style="display: flex; gap: 1.5rem; position: relative; z-index: 1;">
                                    <!-- First image (Student/Tutor) -->
                                    <img src="<?= APP_URL ?>/View/assets/images/instructor/06.jpg" alt="Student learning" style="width: 220px; height: 300px; object-fit: cover; border-radius: 20px; box-shadow: 0 15px 30px rgba(43, 72, 101, 0.15); border: 5px solid #fff; transform: translateY(-15px); transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-20px)'" onmouseout="this.style.transform='translateY(-15px)'">
                                    
                                    <!-- Second image (Hackathon Event) -->
                                    <img src="<?= APP_URL ?>/View/assets/images/event/admin-events-hero.png" alt="Hackathon" style="width: 220px; height: 300px; object-fit: cover; border-radius: 20px; box-shadow: 0 15px 30px rgba(43, 72, 101, 0.15); border: 5px solid #fff; transform: translateY(25px); transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(20px)'" onmouseout="this.style.transform='translateY(25px)'">
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Part: The Table -->
                        <div style="padding: 2.5rem 3.5rem; background: #ffffff;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                                <div>
                                    <h3 style="margin: 0 0 0.5rem 0; color: #2B4865; font-size: 1.6rem; font-weight: 700;">Upcoming Events</h3>
                                    <p style="margin: 0; color: #64748b; font-size: 0.95rem;">Manage all scheduled events and their resources.</p>
                                </div>
                                <div style="display: flex; gap: 1rem; align-items: center;">
                                    <div class="custom-select-wrapper" style="position: relative; user-select: none; width: 170px; z-index: 50;">
                                        <div class="custom-select-trigger" style="padding: 10px 15px; border-radius: 8px; border: 1px solid #e2e8f0; background: white; color: #475569; font-family: inherit; font-weight: 500; font-size: 0.95rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: all 0.2s;">
                                            <span class="custom-select-text">Sort By</span>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                        <div class="custom-select-options" style="position: absolute; top: calc(100% + 8px); left: 0; width: 100%; background: white; border: 1px solid #eef2f6; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.2s; overflow: hidden; padding: 6px;">
                                            <div class="custom-option" data-value="default" style="padding: 10px 15px; cursor: pointer; border-radius: 8px; font-size: 0.9rem; color: #475569; transition: all 0.2s; margin-bottom: 2px;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b'" onmouseout="this.style.background='transparent'; this.style.color='#475569'">Sort By</div>
                                            <div class="custom-option" data-value="titleAsc" style="padding: 10px 15px; cursor: pointer; border-radius: 8px; font-size: 0.9rem; color: #475569; transition: all 0.2s; margin-bottom: 2px;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b'" onmouseout="this.style.background='transparent'; this.style.color='#475569'">Title (A-Z)</div>
                                            <div class="custom-option" data-value="titleDesc" style="padding: 10px 15px; cursor: pointer; border-radius: 8px; font-size: 0.9rem; color: #475569; transition: all 0.2s; margin-bottom: 2px;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b'" onmouseout="this.style.background='transparent'; this.style.color='#475569'">Title (Z-A)</div>
                                            <div class="custom-option" data-value="dateAsc" style="padding: 10px 15px; cursor: pointer; border-radius: 8px; font-size: 0.9rem; color: #475569; transition: all 0.2s; margin-bottom: 2px;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b'" onmouseout="this.style.background='transparent'; this.style.color='#475569'">Date (Oldest)</div>
                                            <div class="custom-option" data-value="dateDesc" style="padding: 10px 15px; cursor: pointer; border-radius: 8px; font-size: 0.9rem; color: #475569; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b'" onmouseout="this.style.background='transparent'; this.style.color='#475569'">Date (Newest)</div>
                                        </div>
                                    </div>
                                    <select id="adminEventSort" style="display: none;">
                                        <option value="default">Sort By</option>
                                        <option value="titleAsc">Title (A-Z)</option>
                                        <option value="titleDesc">Title (Z-A)</option>
                                        <option value="dateAsc">Date (Oldest)</option>
                                        <option value="dateDesc">Date (Newest)</option>
                                    </select>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const wrapper = document.querySelector('.custom-select-wrapper');
                                            const trigger = wrapper.querySelector('.custom-select-trigger');
                                            const options = wrapper.querySelector('.custom-select-options');
                                            const text = wrapper.querySelector('.custom-select-text');
                                            const svg = wrapper.querySelector('svg');
                                            const hiddenSelect = document.getElementById('adminEventSort');
                                            const optionItems = wrapper.querySelectorAll('.custom-option');

                                            trigger.addEventListener('click', function(e) {
                                                e.stopPropagation();
                                                const isOpen = options.style.visibility === 'visible';
                                                
                                                if (isOpen) {
                                                    options.style.opacity = '0';
                                                    options.style.visibility = 'hidden';
                                                    options.style.transform = 'translateY(-10px)';
                                                    svg.style.transform = 'rotate(0deg)';
                                                    trigger.style.borderColor = '#e2e8f0';
                                                    trigger.style.boxShadow = 'none';
                                                } else {
                                                    options.style.opacity = '1';
                                                    options.style.visibility = 'visible';
                                                    options.style.transform = 'translateY(0)';
                                                    svg.style.transform = 'rotate(180deg)';
                                                    trigger.style.borderColor = '#548CA8';
                                                    trigger.style.boxShadow = '0 0 0 3px rgba(84, 140, 168, 0.1)';
                                                }
                                            });

                                            optionItems.forEach(item => {
                                                item.addEventListener('click', function(e) {
                                                    e.stopPropagation();
                                                    const value = this.getAttribute('data-value');
                                                    text.textContent = this.textContent;
                                                    hiddenSelect.value = value;
                                                    
                                                    hiddenSelect.dispatchEvent(new Event('change'));
                                                    
                                                    options.style.opacity = '0';
                                                    options.style.visibility = 'hidden';
                                                    options.style.transform = 'translateY(-10px)';
                                                    svg.style.transform = 'rotate(0deg)';
                                                    trigger.style.borderColor = '#e2e8f0';
                                                    trigger.style.boxShadow = 'none';
                                                });
                                            });

                                            document.addEventListener('click', function() {
                                                options.style.opacity = '0';
                                                options.style.visibility = 'hidden';
                                                options.style.transform = 'translateY(-10px)';
                                                svg.style.transform = 'rotate(0deg)';
                                                trigger.style.borderColor = '#e2e8f0';
                                                trigger.style.boxShadow = 'none';
                                            });
                                        });
                                    </script>
                                    <div style="position: relative;">
                                        <input type="text" id="adminEventSearch" placeholder="Search event by title..." style="padding: 10px 15px 10px 35px; border-radius: 8px; border: 1px solid #e2e8f0; width: 250px; outline: none; transition: border-color 0.2s; font-family: inherit;" onfocus="this.style.borderColor='#548CA8'" onblur="this.style.borderColor='#e2e8f0'">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                    </div>
                                    <span style="background: #e9f1fa; color: #548CA8; padding: 8px 16px; border-radius: 50px; font-size: 0.9rem; font-weight: 700; white-space: nowrap;">
                                        <?= count($evenements ?? []) ?> Total Events
                                    </span>
                                </div>
                            </div>

                            <div class="table-responsive" style="overflow-x: auto; border-radius: 14px; border: 1px solid #eef2f6; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                                <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 1200px;">
                                    <thead>
                                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                            <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">ID</th>
                                            <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Title</th>
                                            <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Description</th>
                                            <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Start Date</th>
                                            <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Location</th>
                                            <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Capacity</th>
                                            <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Type</th>
                                            <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                                            <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Resource</th>
                                            <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Created By</th>
                                            <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($evenements)): ?>
                                            <?php foreach ($evenements as $evenement): ?>
                                                <?php
                                                $statutKey = strtolower((string) (($evenement['statut'] ?? '') ?: 'planifie'));
                                                $statutColor = '#64748b'; $statutBg = '#f1f5f9';
                                                if (in_array($statutKey, ['planifie', 'planifié'], true)) {
                                                    $statutColor = '#3b82f6'; $statutBg = '#eff6ff';
                                                } elseif (in_array($statutKey, ['en cours', 'en_cours'], true)) {
                                                    $statutColor = '#eab308'; $statutBg = '#fefce8';
                                                } elseif (in_array($statutKey, ['termine', 'terminé'], true)) {
                                                    $statutColor = '#22c55e'; $statutBg = '#f0fdf4';
                                                } elseif (in_array($statutKey, ['annule', 'annulé'], true)) {
                                                    $statutColor = '#ef4444'; $statutBg = '#fef2f2';
                                                }
                                                $descFull = (string) ($evenement['description'] ?? '');
                                                ?>
                                                <tr style="border-bottom: 1px solid #eef2f6; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                                    <td style="padding: 1.2rem 1rem; color: #64748b; font-size: 0.9rem; font-weight: 500;">#<?= htmlspecialchars((string) $evenement['id']) ?></td>
                                                    <td style="padding: 1.2rem 1rem; color: #1e293b; font-weight: 700; font-size: 0.95rem;"><?= htmlspecialchars($evenement['titre'] ?: $evenement['title']) ?></td>
                                                    <td style="padding: 1.2rem 1rem; color: #64748b; font-size: 0.9rem; max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($descFull) ?>">
                                                        <?= htmlspecialchars($descFull ?: '—') ?>
                                                    </td>
                                                    <td style="padding: 1.2rem 1rem; color: #475569; font-size: 0.9rem;">
                                                        <div style="font-weight: 600; color: #2B4865;"><?= htmlspecialchars((string) (($evenement['date_debut'] ?? '') ?: (!empty($evenement['event_date']) ? date('M d, Y', strtotime((string) $evenement['event_date'])) : 'N/A'))) ?></div>
                                                        <div style="font-size: 0.8rem; color: #94a3b8;"><?= htmlspecialchars((string) (($evenement['heure_debut'] ?? '') ?: (!empty($evenement['event_date']) ? date('H:i', strtotime((string) $evenement['event_date'])) : '—'))) ?></div>
                                                    </td>
                                                    <td style="padding: 1.2rem 1rem; color: #475569; font-size: 0.9rem; font-weight: 500;">
                                                        <div style="display: flex; align-items: center; gap: 6px;">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#E19864" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                                            <?= htmlspecialchars((string) (($evenement['lieu'] ?? '') ?: ($evenement['location'] ?: 'TBA'))) ?>
                                                        </div>
                                                    </td>
                                                    <td style="padding: 1.2rem 1rem; color: #475569; font-size: 0.9rem; font-weight: 500;"><?= htmlspecialchars((string) (($evenement['capacite_max'] ?? '') ?: 'N/A')) ?></td>
                                                    <td style="padding: 1.2rem 1rem; color: #475569; font-size: 0.9rem; font-weight: 500;">
                                                        <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; border: 1px solid #e2e8f0;">
                                                            <?= htmlspecialchars((string) (($evenement['type'] ?? '') ?: 'N/A')) ?>
                                                        </span>
                                                    </td>
                                                    <td style="padding: 1.2rem 1rem;">
                                                        <span style="background: <?= $statutBg ?>; color: <?= $statutColor ?>; padding: 6px 12px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block;">
                                                            <?= htmlspecialchars((string) (($evenement['statut'] ?? '') ?: 'planifie')) ?>
                                                        </span>
                                                    </td>
                                                    <td style="padding: 1.2rem 1rem;">
                                                        <a href="<?= APP_ENTRY ?>?url=admin/evenement-ressources&evenement_id=<?= (int) $evenement['id'] ?>" style="background: #e9f1fa; color: #548CA8; text-decoration: none; padding: 6px 14px; border-radius: 50px; font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;" onmouseover="this.style.background='#d0e3f5'" onmouseout="this.style.background='#e9f1fa'">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                                                            <?= (int) ($evenement['resource_count'] ?? 0) ?> Items
                                                        </a>
                                                    </td>
                                                    <td style="padding: 1.2rem 1rem;">
                                                        <?php
                                                        $creatorRole = strtolower((string) ($evenement['creator_role'] ?? 'admin'));
                                                        $creatorRoleLabel = $creatorRole === 'teacher' ? 'Teacher' : 'Admin';
                                                        ?>
                                                        <div style="display: flex; align-items: center; gap: 10px;">
                                                            <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #548CA8 0%, #355C7D 100%); color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem;">
                                                                <?= strtoupper(substr($evenement['creator_name'] ?? 'A', 0, 1)) ?>
                                                            </div>
                                                            <div style="display: flex; flex-direction: column;">
                                                                <span style="color: #1e293b; font-weight: 700; font-size: 0.9rem;"><?= htmlspecialchars((string) ($evenement['creator_name'] ?? 'Admin')) ?></span>
                                                                <span style="color: #64748b; font-size: 0.8rem; font-weight: 500;"><?= htmlspecialchars($creatorRoleLabel) ?></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td style="padding: 1.2rem 1rem;">
                                                        <div style="display: flex; gap: 8px;">
                                                            <a href="<?= APP_ENTRY ?>?url=admin/edit-evenement/<?= (int) $evenement['id'] ?>" style="background: #f8fafc; border: 1.5px solid #e2e8f0; color: #64748b; text-decoration: none; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.borderColor='#548CA8'; this.style.color='#548CA8'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#64748b'" title="Edit">
                                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                            </a>
                                                            <?php if (isset($_SESSION['user_id']) && $evenement['created_by'] == $_SESSION['user_id']): ?>
                                                            <a href="<?= APP_ENTRY ?>?url=admin/delete-evenement/<?= (int) $evenement['id'] ?>" onclick="return confirm('Delete this evenement?')" style="background: #fef2f2; border: 1.5px solid #fecaca; color: #ef4444; text-decoration: none; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'" title="Delete">
                                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                            </a>
                                                            <?php else: ?>
                                                            <div style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background: #f1f5f9; color: #cbd5e1; border: 1.5px solid #e2e8f0; cursor: not-allowed;" title="Only the creator can delete this event">
                                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                            </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="11" style="text-align: center; padding: 4rem; color: #64748b; background: #f8fafc;">
                                                    <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                                    <p style="margin: 0 0 1rem 0; font-size: 1.2rem; color: #1e293b; font-weight: 600;">No events found</p>
                                                    <a href="<?= APP_ENTRY ?>?url=admin/add-evenement" style="background: linear-gradient(135deg, #E19864 0%, #d9804b 100%); color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 10px rgba(225, 152, 100, 0.3); display: inline-block;">Create your first event</a>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </section>
                
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('adminEventSearch');
        const table = document.querySelector('.table-responsive table');
        if (!table) return;
        const tbody = table.querySelector('tbody');
        const headers = table.querySelectorAll('th');

        // Search
        if (searchInput && tbody) {
            searchInput.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                const rows = tbody.querySelectorAll('tr');
                
                rows.forEach(row => {
                    if (row.cells.length <= 1) return; // Skip empty message row
                    const titleCell = row.cells[1];
                    if (titleCell) {
                        const titleText = titleCell.textContent.toLowerCase();
                        row.style.display = titleText.includes(filter) ? '' : 'none';
                    }
                });
            });
        }

        // Sort Dropdown
        const sortSelect = document.getElementById('adminEventSort');
        if (sortSelect && tbody) {
            sortSelect.addEventListener('change', function() {
                const val = this.value;
                if (val === 'default') return;

                const rows = Array.from(tbody.querySelectorAll('tr'));
                if (rows.length === 0 || rows[0].cells.length <= 1) return;

                headers.forEach(th => th.innerHTML = th.innerHTML.replace(' ↑', '').replace(' ↓', ''));

                rows.sort((a, b) => {
                    if (val.startsWith('title')) {
                        const aTitle = a.cells[1].textContent.trim();
                        const bTitle = b.cells[1].textContent.trim();
                        return val === 'titleAsc' ? aTitle.localeCompare(bTitle) : bTitle.localeCompare(aTitle);
                    } else if (val.startsWith('date')) {
                        const aDateStr = a.cells[3].querySelector('div') ? a.cells[3].querySelector('div').textContent.trim() : '';
                        const bDateStr = b.cells[3].querySelector('div') ? b.cells[3].querySelector('div').textContent.trim() : '';
                        const aDate = new Date(aDateStr).getTime() || 0;
                        const bDate = new Date(bDateStr).getTime() || 0;
                        return val === 'dateAsc' ? aDate - bDate : bDate - aDate;
                    }
                    return 0;
                });

                rows.forEach(row => tbody.appendChild(row));
            });
        }

        // Header Click Sort
        if (headers.length > 0 && tbody) {
            headers.forEach((header, index) => {
                if (header.textContent.trim() === 'Actions' || header.textContent.trim() === 'Resource') return; // Skip non-sortable
                
                header.style.cursor = 'pointer';
                header.title = 'Click to sort';
                let isAscending = true;
                
                header.addEventListener('click', () => {
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    if (rows.length === 0 || rows[0].cells.length <= 1) return;
                    
                    headers.forEach(th => th.innerHTML = th.innerHTML.replace(' ↑', '').replace(' ↓', ''));
                    
                    rows.sort((a, b) => {
                        const aText = a.cells[index].textContent.trim();
                        const bText = b.cells[index].textContent.trim();
                        
                        if (index === 0 || index === 5) { // ID and Capacity
                            const aNum = parseInt(aText.replace(/[^0-9]/g, '')) || 0;
                            const bNum = parseInt(bText.replace(/[^0-9]/g, '')) || 0;
                            return isAscending ? aNum - bNum : bNum - aNum;
                        }
                        
                        return isAscending ? aText.localeCompare(bText) : bText.localeCompare(aText);
                    });
                    
                    rows.forEach(row => tbody.appendChild(row));
                    header.innerHTML += isAscending ? ' ↑' : ' ↓';
                    isAscending = !isAscending;
                });
            });
        }
    });
</script>

<style>
    /* Styling the scrollbar for the table */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    @media (max-width: 1200px) {
        .neo-auth-grid {
            grid-template-columns: 1fr !important;
        }
        .neo-auth-grid > div:first-child {
            padding: 2.5rem !important;
        }
        .neo-auth-grid > div:last-child {
            display: none !important; /* Hide images on smaller screens to save space */
        }
    }
</style>
