<?php
/**
 * APPOLIOS - Teacher Evenements Management
 */

$teacherSidebarActive = 'evenements';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            
            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0; font-family: 'Inter', sans-serif;">
                
                <!-- Welcome Banner with Image -->
                <div style="background: url('https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; border-radius: 20px; margin-bottom: 2rem; color: white; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(43, 72, 101, 0.15); min-height: 250px; display: flex; align-items: flex-end;">
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to top, rgba(43, 72, 101, 0.95) 0%, rgba(43, 72, 101, 0.4) 100%);"></div>
                    
                    <div style="position: relative; z-index: 1; padding: 2.5rem; width: 100%; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1.5rem;">
                        <div>
                            <span style="background: rgba(255,255,255,0.2); backdrop-filter: blur(5px); padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; display: inline-block;">Event Management</span>
                            <h1 style="font-size: 2.5rem; font-weight: 800; margin: 0 0 0.5rem 0; color: #ffffff;">My Events</h1>
                            <p style="font-size: 1.1rem; margin: 0; opacity: 0.9; max-width: 600px; color: #f8fafc;">Create and manage your event proposals for admin approval. Build unforgettable experiences for your students.</p>
                        </div>
                        <div style="display: flex; gap: 12px;">
                            <a href="<?= APP_ENTRY ?>?url=teacher/dashboard" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 600; backdrop-filter: blur(5px); transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">Back to Dashboard</a>
                            <a href="<?= APP_ENTRY ?>?url=teacher/add-evenement" style="background: #E19864; color: white; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 600; box-shadow: 0 4px 15px rgba(225, 152, 100, 0.3); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(225, 152, 100, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(225, 152, 100, 0.3)'">Propose Event</a>
                        </div>
                    </div>
                </div>

                <div style="background: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; overflow: hidden; padding: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                        <h3 style="margin: 0; font-size: 1.3rem; color: #548CA8; font-weight: 800;">All My Events</h3>
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
                            <select id="teacherEventSort" style="display: none;">
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
                                    const hiddenSelect = document.getElementById('teacherEventSort');
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
                                <input type="text" id="teacherEventSearch" placeholder="Search event by title..." style="padding: 10px 15px 10px 35px; border-radius: 8px; border: 1px solid #e2e8f0; width: 250px; outline: none; transition: border-color 0.2s; font-family: inherit;" onfocus="this.style.borderColor='#548CA8'" onblur="this.style.borderColor='#e2e8f0'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive" style="overflow-x: auto; border-radius: 14px; border: 1px solid #eef2f6; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 1000px;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">ID</th>
                                    <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Title</th>
                                    <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Start Date</th>
                                    <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Location</th>
                                    <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Approval</th>
                                    <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Resources</th>
                                    <th style="padding: 1.2rem 1rem; color: #475569; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($evenements)): ?>
                                    <?php foreach ($evenements as $evenement): ?>
                                        <?php
                                        $approval = (string) ($evenement['display_approval_lower'] ?? 'approved');
                                        $pillBg = (string) ($evenement['display_approval_pill_bg'] ?? '#f0fdf4');
                                        $pillColor = (string) ($evenement['display_approval_pill_color'] ?? '#22c55e');
                                        ?>
                                        <tr style="border-bottom: 1px solid #eef2f6; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                            <td style="padding: 1.2rem 1rem; color: #64748b; font-size: 0.9rem; font-weight: 500;">#<?= (int) $evenement['id'] ?></td>
                                            <td style="padding: 1.2rem 1rem; color: #1e293b; font-weight: 700; font-size: 0.95rem;"><?= htmlspecialchars(($evenement['titre'] ?? '') ?: ($evenement['title'] ?? '')) ?></td>
                                            <td style="padding: 1.2rem 1rem; color: #475569; font-size: 0.9rem;">
                                                <div style="font-weight: 600; color: #2B4865;"><?= htmlspecialchars((string) ($evenement['display_date_primary'] ?? 'N/A')) ?></div>
                                                <div style="font-size: 0.8rem; color: #94a3b8;"><?= htmlspecialchars((string) ($evenement['display_time_primary'] ?? '—')) ?></div>
                                            </td>
                                            <td style="padding: 1.2rem 1rem; color: #475569; font-size: 0.9rem; font-weight: 500;">
                                                <div style="display: flex; align-items: center; gap: 6px;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#E19864" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                                    <?= htmlspecialchars((string) (($evenement['lieu'] ?? '') ?: ($evenement['location'] ?? 'TBA'))) ?>
                                                </div>
                                            </td>
                                            <td style="padding: 1.2rem 1rem;">
                                                <?php if ($approval === 'rejected'): ?>
                                                    <button type="button" 
                                                            onclick="showRejectionModal('<?= (string) ($evenement['display_rejection_modal_reason_js'] ?? '') ?>', '<?= (string) ($evenement['display_rejection_modal_date_js'] ?? '') ?>')"
                                                            style="background: <?= $pillBg ?>; color: <?= $pillColor ?>; border: 1px solid rgba(239, 68, 68, 0.3); padding: 5px 12px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; transition: all 0.2s; outline: none;"
                                                            onmouseover="this.style.boxShadow='0 4px 10px rgba(239,68,68,0.2)'; this.style.transform='translateY(-1px)'; this.style.background='#fee2e2';"
                                                            onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)'; this.style.background='<?= $pillBg ?>';">
                                                        <?= htmlspecialchars($approval) ?>
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                                    </button>
                                                <?php else: ?>
                                                    <span style="background: <?= $pillBg ?>; color: <?= $pillColor ?>; padding: 6px 12px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block;">
                                                        <?= htmlspecialchars($approval) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="padding: 1.2rem 1rem;">
                                                <a href="<?= APP_ENTRY ?>?url=teacher/evenement-ressources&evenement_id=<?= (int) $evenement['id'] ?>" style="background: #e9f1fa; color: #548CA8; text-decoration: none; padding: 6px 14px; border-radius: 50px; font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;" onmouseover="this.style.background='#d0e3f5'" onmouseout="this.style.background='#e9f1fa'">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                                                    <?= (int) ($evenement['resource_count'] ?? 0) ?> items
                                                </a>
                                            </td>

                                            <td style="padding: 1.2rem 1rem;">
                                                <div style="display: flex; gap: 8px;">
                                                    <a href="<?= APP_ENTRY ?>?url=teacher/edit-evenement/<?= (int) $evenement['id'] ?>" style="background: #f8fafc; border: 1.5px solid #e2e8f0; color: #64748b; text-decoration: none; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.borderColor='#548CA8'; this.style.color='#548CA8'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#64748b'" title="Edit">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                    </a>
                                                    <?php if (in_array($approval, ['pending', 'rejected'])): ?>
                                                        <a href="<?= APP_ENTRY ?>?url=teacher/delete-evenement/<?= (int) $evenement['id'] ?>" style="background: #fef2f2; border: 1.5px solid #fecaca; color: #ef4444; text-decoration: none; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'" onclick="return confirm('Delete this event and all linked resources?')" title="Delete">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 4rem 2rem; background: #f8fafc;">
                                            <div style="color: #b6d0e2; margin-bottom: 1rem; display: flex; justify-content: center;">
                                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                            </div>
                                            <p style="color: #64748b; margin: 0 0 1.5rem 0; font-size: 1rem;">No events proposed yet.</p>
                                            <a href="<?= APP_ENTRY ?>?url=teacher/add-evenement" class="pro-action-btn primary" style="display: inline-block; background: #E19864; color: white; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600;">Propose your first event</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
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

                    /* Rejection Modal Styles */
                    .neo-modal-overlay {
                        position: fixed;
                        top: 0; left: 0; width: 100%; height: 100%;
                        background: rgba(15, 23, 42, 0.4);
                        backdrop-filter: blur(4px);
                        -webkit-backdrop-filter: blur(4px);
                        display: flex; align-items: center; justify-content: center;
                        z-index: 9999;
                        opacity: 0; visibility: hidden;
                        transition: all 0.3s ease;
                    }
                    .neo-modal-overlay.active {
                        opacity: 1; visibility: visible;
                    }
                    .neo-modal-card {
                        background: white;
                        border-radius: 20px;
                        width: 90%; max-width: 450px;
                        padding: 2.5rem;
                        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                        transform: translateY(20px) scale(0.95);
                        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
                        border: 1px solid #f1f5f9;
                        position: relative;
                    }
                    .neo-modal-overlay.active .neo-modal-card {
                        transform: translateY(0) scale(1);
                    }
                    .neo-modal-close {
                        position: absolute; top: 1.5rem; right: 1.5rem;
                        background: #f8fafc; border: none; width: 36px; height: 36px;
                        border-radius: 50%; display: flex; align-items: center; justify-content: center;
                        color: #64748b; cursor: pointer; transition: all 0.2s;
                    }
                    .neo-modal-close:hover {
                        background: #f1f5f9; color: #0f172a;
                    }
                </style>

                <!-- Rejection Reason Modal -->
                <div id="rejectionModal" class="neo-modal-overlay">
                    <div class="neo-modal-card">
                        <button class="neo-modal-close" onclick="closeRejectionModal()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                        
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 1.5rem;">
                            <div style="background: #fef2f2; width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #ef4444; flex-shrink: 0;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            </div>
                            <div>
                                <h3 style="margin: 0 0 5px 0; color: #0f172a; font-size: 1.3rem; font-weight: 800;">Event Rejected</h3>
                                <p style="margin: 0; color: #64748b; font-size: 0.85rem;" id="modalRejectionDate"></p>
                            </div>
                        </div>

                        <div style="background: #f8fafc; padding: 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem;">
                            <p style="margin: 0; color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Reason for refusal</p>
                            <p style="margin: 0; color: #334155; font-size: 1rem; line-height: 1.5;" id="modalRejectionReason"></p>
                        </div>

                        <button onclick="closeRejectionModal()" style="width: 100%; background: white; border: 2px solid #e2e8f0; color: #475569; padding: 12px; border-radius: 10px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1'" onmouseout="this.style.background='white'; this.style.borderColor='#e2e8f0'">Understood</button>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const searchInput = document.getElementById('teacherEventSearch');
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
                                    if (row.cells.length <= 1) return; // Skip "No events" row
                                    const titleCell = row.cells[1];
                                    if (titleCell) {
                                        const titleText = titleCell.textContent.toLowerCase();
                                        row.style.display = titleText.includes(filter) ? '' : 'none';
                                    }
                                });
                            });
                        }

                        // Sort Dropdown
                        const sortSelect = document.getElementById('teacherEventSort');
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
                                        const aDateStr = a.cells[2].querySelector('div') ? a.cells[2].querySelector('div').textContent.trim() : '';
                                        const bDateStr = b.cells[2].querySelector('div') ? b.cells[2].querySelector('div').textContent.trim() : '';
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
                                if (header.textContent.trim() === 'Actions' || header.textContent.trim() === 'Resources') return; // Skip these columns
                                
                                header.style.cursor = 'pointer';
                                header.title = 'Click to sort';
                                let isAscending = true;
                                
                                header.addEventListener('click', () => {
                                    const rows = Array.from(tbody.querySelectorAll('tr'));
                                    if (rows.length === 0 || rows[0].cells.length <= 1) return; // Skip if empty
                                    
                                    // Reset other indicators
                                    headers.forEach(th => th.innerHTML = th.innerHTML.replace(' ↑', '').replace(' ↓', ''));
                                    
                                    rows.sort((a, b) => {
                                        const aText = a.cells[index].textContent.trim();
                                        const bText = b.cells[index].textContent.trim();
                                        
                                        // Sort ID or Capacity as numbers
                                        if (index === 0) {
                                            const aNum = parseInt(aText.replace(/[^0-9]/g, ''));
                                            const bNum = parseInt(bText.replace(/[^0-9]/g, ''));
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

                    function showRejectionModal(reason, date) {
                        document.getElementById('modalRejectionReason').textContent = reason;
                        document.getElementById('modalRejectionDate').textContent = 'Rejected on ' + date;
                        document.getElementById('rejectionModal').classList.add('active');
                        document.body.style.overflow = 'hidden'; // Prevent scrolling
                    }

                    function closeRejectionModal() {
                        document.getElementById('rejectionModal').classList.remove('active');
                        document.body.style.overflow = 'auto'; // Restore scrolling
                    }

                    // Close modal when clicking outside the card
                    document.getElementById('rejectionModal').addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeRejectionModal();
                        }
                    });
                </script>
                </div>

            </div>
        </div>
    </div>
</div>
