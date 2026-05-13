<?php
/**
 * APPOLIOS - Admin Evenements Management
 */
?>



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
                                        <a href="<?= APP_ENTRY ?>?url=event/evenement-requests" style="background: #e9f1fa; color: #548CA8; padding: 12px 24px; border-radius: 10px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#d0e3f5'" onmouseout="this.style.background='#e9f1fa'">Teacher Requests</a>
                                        <a href="<?= APP_ENTRY ?>?url=event/add-evenement" style="background: linear-gradient(135deg, #E19864 0%, #d9804b 100%); color: #fff; padding: 12px 24px; border-radius: 10px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 15px rgba(225, 152, 100, 0.3); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(225, 152, 100, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(225, 152, 100, 0.3)'">Add Event</a>
                                        <button type="button" onclick="openAIPredictionModal()" style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); color: #fff; padding: 12px 24px; border-radius: 10px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3); transition: all 0.2s; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(139, 92, 246, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(139, 92, 246, 0.3)'">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                                            AI Prediction
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: The Images -->
                            <div style="padding: 2rem; display: flex; align-items: center; justify-content: center; position: relative; background: #fff; overflow: hidden;">
                                <div style="position: absolute; top: 15%; right: 10%; width: 70%; height: 70%; background: #E19864; border-radius: 30px; transform: rotate(5deg); z-index: 0; opacity: 0.15;"></div>
                                <div style="position: absolute; bottom: 5%; left: 5%; width: 50%; height: 50%; background: #548CA8; border-radius: 30px; transform: rotate(-8deg); z-index: 0; opacity: 0.1;"></div>
                                
                                <div style="display: flex; gap: 1rem; position: relative; z-index: 1;">
                                    <img src="<?= APP_URL ?>/View/assets/images/instructor/06.jpg" alt="Student learning" style="width: 160px; height: 220px; object-fit: cover; border-radius: 16px; box-shadow: 0 10px 20px rgba(43, 72, 101, 0.12); border: 4px solid #fff; transform: translateY(-10px); transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-14px)'" onmouseout="this.style.transform='translateY(-10px)'">
                                    <img src="<?= APP_URL ?>/View/assets/images/event/admin-events-hero.png" alt="Hackathon" style="width: 160px; height: 220px; object-fit: cover; border-radius: 16px; box-shadow: 0 10px 20px rgba(43, 72, 101, 0.12); border: 4px solid #fff; transform: translateY(18px); transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(14px)'" onmouseout="this.style.transform='translateY(18px)'">
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
                                                <tr style="border-bottom: 1px solid #eef2f6; transition: background 0.2s; cursor: pointer;" 
                                                    onmouseover="this.style.background='#f8fafc'" 
                                                    onmouseout="this.style.background='transparent'" 
                                                    onclick="console.log('Row clicked for event:', <?= (int)$evenement['id'] ?>); showAdminParticipantsModal(<?= (int)$evenement['id'] ?>)">
                                                    <td style="padding: 1.2rem 1rem; color: #64748b; font-size: 0.9rem; font-weight: 500;">#<?= htmlspecialchars((string) $evenement['id']) ?></td>
                                                    <td style="padding: 1.2rem 1rem; color: #1e293b; font-weight: 700; font-size: 0.95rem;">
                                                        <div style="display: flex; align-items: center; gap: 8px;">
                                                            <?= htmlspecialchars($evenement['titre'] ?: $evenement['title']) ?>
                                                            <span style="background: #eff6ff; color: #3b82f6; padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; border: 1px solid #dbeafe; cursor: pointer;" title="Click to view participants">
                                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:2px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                                                                Participants
                                                            </span>
                                                        </div>
                                                    </td>
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
                                                        <a href="<?= APP_ENTRY ?>?url=ressource/evenement-ressources&evenement_id=<?= (int) $evenement['id'] ?>" onclick="event.stopPropagation()" style="background: #e9f1fa; color: #548CA8; text-decoration: none; padding: 6px 14px; border-radius: 50px; font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;" onmouseover="this.style.background='#d0e3f5'" onmouseout="this.style.background='#e9f1fa'">
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
                                                    <td style="padding: 1.2rem 1rem;" onclick="event.stopPropagation()">
                                                        <div style="display: flex; gap: 8px;">
                                                            <a href="<?= APP_ENTRY ?>?url=event/edit-evenement/<?= (int) $evenement['id'] ?>" style="background: #f8fafc; border: 1.5px solid #e2e8f0; color: #64748b; text-decoration: none; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.borderColor='#548CA8'; this.style.color='#548CA8'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#64748b'" title="Edit">
                                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                            </a>
                                                            <?php if (isset($_SESSION['user_id']) && $evenement['created_by'] == $_SESSION['user_id']): ?>
                                                            <a href="<?= APP_ENTRY ?>?url=event/delete-evenement/<?= (int) $evenement['id'] ?>" onclick="if(!confirm('Delete this evenement?')) event.preventDefault()" style="background: #fef2f2; border: 1.5px solid #fecaca; color: #ef4444; text-decoration: none; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'" title="Delete">
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
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                                    <p style="margin: 0 0 1rem 0; font-size: 1.2rem; color: #1e293b; font-weight: 600;">No events found</p>
                                                    <a href="<?= APP_ENTRY ?>?url=event/add-evenement" style="background: linear-gradient(135deg, #E19864 0%, #d9804b 100%); color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 10px rgba(225, 152, 100, 0.3); display: inline-block;">Create your first event</a>
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

<!-- Admin Participants View-Only Modal -->
<div id="adminParticipantsModal" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(15,23,42,0.5);backdrop-filter:blur(6px);display:none;align-items:center;justify-content:center;z-index:9990;opacity:0;transition:opacity 0.3s ease;">
    <div id="adminParticipantsModalCard" style="background:#fff;border-radius:24px;width:95%;max-width:900px;max-height:90vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);transform:translateY(20px);transition:transform 0.3s ease-out;">
        <!-- Header -->
        <div style="padding:2rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:15px;">
                <div style="background:#e9f1fa;width:54px;height:54px;border-radius:16px;display:flex;align-items:center;justify-content:center;color:#548CA8;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div>
                    <h3 id="adminParticipantsEventTitle" style="margin:0;color:#0f172a;font-size:1.4rem;font-weight:800;">Event Participants</h3>
                    <p style="margin:4px 0 0 0;color:#64748b;font-size:0.9rem;font-weight:500;">Manage event attendees and their status.</p>
                </div>
            </div>
            <button onclick="closeAdminParticipantsModal()" style="background:#f1f5f9;border:none;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#64748b;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='#e2e8f0';this.style.color='#0f172a'" onmouseout="this.style.background='#f1f5f9';this.style.color='#64748b'">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- Content -->
        <div style="flex:1;overflow-y:auto;padding:2rem;" id="adminParticipantsModalScrollable">
            <!-- Stats Row -->
            <div id="adminParticipantsStats" style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;margin-bottom:2rem;"></div>

            <!-- Table -->
            <div style="background:#fff;border:1px solid #eef2f6;border-radius:16px;overflow:hidden;">
                <table id="adminParticipantsTable" style="width:100%;border-collapse:collapse;text-align:left;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #eef2f6;">
                            <th style="padding:1rem 1.2rem;color:#475569;font-weight:600;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.05em;">Participant</th>
                            <th style="padding:1rem 1.2rem;color:#475569;font-weight:600;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.05em;">Role</th>
                            <th style="padding:1rem 1.2rem;color:#475569;font-weight:600;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.05em;">Requested</th>
                            <th style="padding:1rem 1.2rem;color:#475569;font-weight:600;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                            <th style="padding:1rem 1.2rem;color:#475569;font-weight:600;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.05em;text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="adminParticipantsTbody"></tbody>
                </table>
                <div id="adminNoParticipantsMsg" style="display:none;padding:3rem 2rem;text-align:center;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin-bottom:12px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <p style="margin:0;color:#94a3b8;font-size:0.95rem;font-weight:500;">No participants for this event yet.</p>
                </div>
            </div>

            <button onclick="closeAdminParticipantsModal()" style="width:100%;margin-top:1.5rem;background:linear-gradient(135deg,#548CA8 0%,#2B4865 100%);border:none;color:white;padding:13px;border-radius:12px;font-weight:700;font-size:1rem;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">Close</button>
        </div>
    </div>
</div>

<style>
    #adminParticipantsModal.open { opacity:1; }
    #adminParticipantsModal.open #adminParticipantsModalCard { transform: translateY(0); }
    /* scrollbar */
    #adminParticipantsModalCard::-webkit-scrollbar { width:6px; }
    #adminParticipantsModalCard::-webkit-scrollbar-track { background:transparent; }
    #adminParticipantsModalCard::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:10px; }
</style>

<script>
    const participationsByEvent = <?= json_encode($participationsByEvent ?? []) ?>;

    function showAdminParticipantsModal(eventId) {
        console.log('Opening modal for event ID:', eventId);
        const modal   = document.getElementById('adminParticipantsModal');
        const tbody   = document.getElementById('adminParticipantsTbody');
        const noMsg   = document.getElementById('adminNoParticipantsMsg');
        const table   = document.getElementById('adminParticipantsTable');
        const stats   = document.getElementById('adminParticipantsStats');
        const titleEl = document.getElementById('adminParticipantsEventTitle');

        tbody.innerHTML = '';
        stats.innerHTML = '';

        const participants = participationsByEvent[eventId] || [];

        // Event title from first participant or fallback
        titleEl.textContent = participants.length > 0
            ? 'Event #' + eventId + ' — ' + (participants[0].event_title || '')
            : 'Event #' + eventId;

        // Stats
        const total    = participants.length;
        const approved = participants.filter(p => (p.status || 'pending') === 'approved').length;
        const pending  = participants.filter(p => (p.status || 'pending') === 'pending').length;
        const rejected = participants.filter(p => (p.status || 'pending') === 'rejected').length;

        const statItems = [
            { label: 'Total', value: total,    bg: '#e9f1fa', color: '#548CA8' },
            { label: 'Approved', value: approved, bg: '#f0fdf4', color: '#22c55e' },
            { label: 'Pending',  value: pending,  bg: '#fff7ed', color: '#f97316' },
        ];
        statItems.forEach(s => {
            stats.innerHTML += `<div style="background:${s.bg};border-radius:12px;padding:14px 16px;text-align:center;">
                <div style="font-size:1.6rem;font-weight:800;color:${s.color};">${s.value}</div>
                <div style="font-size:0.75rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-top:2px;">${s.label}</div>
            </div>`;
        });

        if (participants.length === 0) {
            table.style.display = 'none';
            noMsg.style.display = 'block';
        } else {
            table.style.display = 'table';
            noMsg.style.display = 'none';

            participants.forEach(p => {
                const name   = escapeHtml(p.student_name_full || p.student_name || 'Unknown');
                const email  = p.student_email || '';
                const role   = p.student_role || 'student';
                const status = p.status || 'pending';
                const date   = p.created_at ? new Date(p.created_at).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'}) : '—';

                let sBg = '#fff7ed', sColor = '#f97316';
                if (status === 'approved') { sBg = '#f0fdf4'; sColor = '#22c55e'; }
                else if (status === 'rejected') { sBg = '#fef2f2'; sColor = '#ef4444'; }

                const roleBg    = role === 'student' ? '#eff6ff' : '#f0fdf4';
                const roleColor = role === 'student' ? '#3b82f6' : '#22c55e';
                const initials  = name.split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase();

                const tr = document.createElement('tr');
                tr.style.borderBottom = '1px solid #eef2f6';
                
                // Permission check: if admin is creator, show buttons for pending
                console.log('Checking permission for participant:', p.student_name);
                console.log('Event Creator ID from DB:', p.event_creator_id);
                console.log('Current Session User ID:', <?= (int)($_SESSION['user_id'] ?? 0) ?>);
                
                const isCreator = p.event_creator_id == <?= (int)($_SESSION['user_id'] ?? 0) ?>;
                const canManage = isCreator && status === 'pending';

                tr.innerHTML = `
                    <td style="padding:1rem 1.2rem;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;background:linear-gradient(135deg,#548CA8,#2B4865);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.85rem;flex-shrink:0;">${initials}</div>
                            <div>
                                <div style="font-weight:700;color:#1e293b;font-size:0.9rem;">${name}</div>
                                <div style="color:#94a3b8;font-size:0.78rem;">${escapeHtml(email)}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:1rem 1.2rem;">
                        <span style="background:${roleBg};color:${roleColor};padding:3px 10px;border-radius:20px;font-size:0.75rem;font-weight:700;text-transform:capitalize;">${escapeHtml(role)}</span>
                    </td>
                    <td style="padding:1rem 1.2rem;color:#64748b;font-size:0.85rem;">${date}</td>
                    <td style="padding:1rem 1.2rem;">
                        <span style="background:${sBg};color:${sColor};padding:4px 12px;border-radius:20px;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;">${escapeHtml(status)}</span>
                    </td>
                    <td style="padding:1rem 1.2rem; text-align:right;">
                        <div style="display:flex; gap:6px; justify-content:flex-end;">
                            ${canManage ? `
                                <form method="POST" action="<?= APP_ENTRY ?>?url=event/approve-participation/${p.id}" style="margin:0;">
                                    <button type="submit" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;padding:5px 10px;border-radius:8px;font-weight:700;font-size:0.75rem;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">Approve</button>
                                </form>
                                <form method="POST" action="<?= APP_ENTRY ?>?url=event/reject-participation/${p.id}" style="margin:0;">
                                    <button type="submit" onclick="return confirm('Reject this request?')" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:5px 10px;border-radius:8px;font-weight:700;font-size:0.75rem;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">Reject</button>
                                </form>
                            ` : `
                                <span style="color:#cbd5e1;font-size:0.75rem;font-weight:600;">No Action</span>
                            `}
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        modal.style.display = 'flex';
        modal.style.opacity = '1';
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => {
            modal.classList.add('open');
            const card = document.getElementById('adminParticipantsModalCard');
            if (card) card.style.transform = 'translateY(0)';
        });
    }

    function closeAdminParticipantsModal() {
        const modal = document.getElementById('adminParticipantsModal');
        const card = document.getElementById('adminParticipantsModalCard');
        modal.classList.remove('open');
        modal.style.opacity = '0';
        if (card) card.style.transform = 'translateY(20px)';
        setTimeout(() => { 
            modal.style.display = 'none'; 
            document.body.style.overflow = 'auto'; 
        }, 300);
    }

    document.getElementById('adminParticipantsModal').addEventListener('click', function(e) {
        if (e.target === this) closeAdminParticipantsModal();
    });
    function escapeHtml(text) {
        return (text || '').toString()
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }
</script>

<!-- AI Prediction Modal -->
<div id="aiPredictionModal" class="neo-modal-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center; z-index: 9999;">
    <div class="neo-modal-card" style="background: linear-gradient(145deg, #1a1a2e 0%, #16213e 100%); border-radius: 24px; width: 90%; max-width: 700px; max-height: 85vh; overflow-y: auto; padding: 2.5rem; box-shadow: 0 25px 80px -12px rgba(0, 0, 0, 0.6); border: 1px solid rgba(139, 92, 246, 0.2); position: relative;">
        <!-- Close Button -->
        <button onclick="closeAIPredictionModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: rgba(255,255,255,0.1); border: none; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #94a3b8; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.color='#fff'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 2.5rem;">
            <div style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); width: 64px; height: 64px; border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; margin: 0 auto 1.5rem; box-shadow: 0 8px 32px rgba(139, 92, 246, 0.4);">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
            </div>
            <h3 style="margin: 0 0 0.5rem 0; color: #fff; font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em;">AI Event Predictions</h3>
            <p style="margin: 0; color: #94a3b8; font-size: 1rem;">Smart analysis of your upcoming events</p>
        </div>

        <!-- Loading State -->
        <div id="predictionLoading" style="text-align: center; padding: 3rem 2rem;">
            <div style="display: inline-block; width: 56px; height: 56px; border: 4px solid rgba(139, 92, 246, 0.2); border-top-color: #8B5CF6; border-radius: 50%; animation: spin 1s linear infinite;"></div>
            <p style="color: #94a3b8; margin-top: 1.5rem; font-size: 1rem;">Analyzing event data...</p>
        </div>

        <!-- Error State -->
        <div id="predictionError" style="display: none; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 16px; padding: 2rem; text-align: center;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" style="margin-bottom: 1rem;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <p id="errorMessage" style="color: #fca5a5; margin: 0; font-size: 1rem;">Failed to generate predictions. Please try again.</p>
        </div>

        <!-- Prediction Results Cards -->
        <div id="predictionResults" style="display: none;">
            <div id="predictionCards"></div>
        </div>

        <!-- Analyze Button -->
        <div id="predictionActions" style="margin-top: 2rem;">
            <button onclick="analyzeAllEventsWithAI()" style="width: 100%; background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); color: white; border: none; padding: 16px 28px; border-radius: 14px; font-weight: 700; font-size: 1.05rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 12px; box-shadow: 0 8px 32px rgba(139, 92, 246, 0.35);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 40px rgba(139, 92, 246, 0.5)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 32px rgba(139, 92, 246, 0.35)'">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                Generate Predictions
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .prediction-card {
        background: linear-gradient(145deg, rgba(30, 41, 59, 0.8) 0%, rgba(15, 23, 42, 0.6) 100%);
        border: 1px solid rgba(139, 92, 246, 0.15);
        border-radius: 20px;
        padding: 1.75rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .prediction-card:hover {
        border-color: rgba(139, 92, 246, 0.3);
        transform: translateY(-2px);
    }
    
    .prediction-card:last-child {
        margin-bottom: 0;
    }
    
    .prediction-number {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.25rem;
        color: #fff;
        flex-shrink: 0;
    }
    
    .prediction-number-1 { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .prediction-number-2 { background: linear-gradient(135deg, #64748b 0%, #475569 100%); }
    .prediction-number-3 { background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); }
    .prediction-number-4 { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .prediction-number-5 { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
</style>

<script>
    function openAIPredictionModal() {
        document.getElementById('aiPredictionModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        // Reset state
        document.getElementById('predictionLoading').style.display = 'none';
        document.getElementById('predictionResults').style.display = 'none';
        document.getElementById('predictionError').style.display = 'none';
        document.getElementById('predictionActions').style.display = 'block';
    }

    function closeAIPredictionModal() {
        document.getElementById('aiPredictionModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close on backdrop click
    document.getElementById('aiPredictionModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAIPredictionModal();
        }
    });

    async function analyzeAllEventsWithAI() {
        // Show loading, hide other states
        document.getElementById('predictionActions').style.display = 'none';
        document.getElementById('predictionLoading').style.display = 'block';
        document.getElementById('predictionResults').style.display = 'none';
        document.getElementById('predictionError').style.display = 'none';

        try {
            const response = await fetch('<?= APP_ENTRY ?>?url=event/predict-top-events&role=admin');
            const data = await response.json();

            if (data.success) {
                // Build prediction cards
                const cardsContainer = document.getElementById('predictionCards');
                cardsContainer.innerHTML = '';
                
                data.events.forEach((ev, index) => {
                    const card = document.createElement('div');
                    card.style.cssText = 'background: rgba(30, 41, 59, 0.6); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 1.5rem; margin-bottom: 1rem;';
                    card.innerHTML = `
                        <div style="display: flex; gap: 1rem; align-items: flex-start;">
                            <div class="prediction-number-${index + 1}" style="width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.1rem; flex-shrink: 0;">${index + 1}</div>
                            <div style="flex: 1; min-width: 0;">
                                <h4 style="margin: 0 0 0.5rem 0; color: #f1f5f9; font-size: 1.1rem; font-weight: 700;">${escapeHtml(ev.title)}</h4>
                                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-bottom: 0.75rem; font-size: 0.9rem;">
                                    <span style="color: #A78BFA; font-weight: 600;">Predicted: ${ev.predicted}</span>
                                    <span style="color: #94a3b8;">Capacity: ${ev.capacity}</span>
                                </div>
                                <div style="background: rgba(139, 92, 246, 0.08); border-left: 3px solid #A78BFA; border-radius: 0 8px 8px 0; padding: 0.75rem 1rem;">
                                    <p style="margin: 0; color: #C4B5FD; font-size: 0.9rem; font-style: italic; line-height: 1.5;">"${escapeHtml(ev.reason)}"</p>
                                </div>
                            </div>
                        </div>
                    `;
                    cardsContainer.appendChild(card);
                });

                document.getElementById('predictionLoading').style.display = 'none';
                document.getElementById('predictionResults').style.display = 'block';
            } else {
                throw new Error(data.message || 'Failed to generate predictions');
            }
        } catch (error) {
            document.getElementById('predictionLoading').style.display = 'none';
            document.getElementById('predictionError').style.display = 'block';
            document.getElementById('errorMessage').textContent = error.message || 'Failed to generate predictions. Please try again.';
            document.getElementById('predictionActions').style.display = 'block';
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>



<!-- Removed duplicate modal -->
</script>
