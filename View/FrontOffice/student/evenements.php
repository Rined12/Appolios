<?php
/**
 * APPOLIOS - Student Evenements Catalog
 */

$studentSidebarActive = 'evenements';
$participationMap = $participationMap ?? [];
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main">
                <section class="student-events-hero-top">
                    <div class="student-events-hero-copy">
                        <span class="student-events-hero-kicker">Student Space</span>
                        <h1>Upcoming Events</h1>
                        <p>Welcome <?= htmlspecialchars($userName ?? ($_SESSION['user_name'] ?? 'Student')) ?>, discover event details and resources.</p>
                    </div>

                    <div class="student-events-hero-media" aria-hidden="true">
                        <article class="student-events-visual-card student-events-visual-card-main">
                            <img src="<?= APP_URL ?>/View/assets/images/about/06.jpg" alt="Students collaborating around a table" class="student-events-visual-img">
                        </article>
                        <article class="student-events-visual-card student-events-visual-card-sub">
                            <img src="<?= APP_URL ?>/View/assets/images/about/09.jpg" alt="Online conference session" class="student-events-visual-img">
                        </article>
                    </div>
                </section>

                <div class="student-events-tabs" style="margin-bottom: 2rem; display: flex; gap: 1rem; border-bottom: 2px solid #eef2f6; padding-bottom: 10px;">
                    <button class="student-tab-btn active" data-tab="all-events" style="background: none; border: none; padding: 10px 20px; font-weight: 700; font-size: 1rem; color: #548CA8; cursor: pointer; position: relative; transition: all 0.2s;">
                        All Events
                        <span class="tab-indicator" style="position: absolute; bottom: -12px; left: 0; width: 100%; height: 3px; background: #548CA8; border-radius: 3px;"></span>
                    </button>
                    <button class="student-tab-btn" data-tab="my-participations" style="background: none; border: none; padding: 10px 20px; font-weight: 700; font-size: 1rem; color: #64748b; cursor: pointer; position: relative; transition: all 0.2s;">
                        My Participations
                        <span class="tab-indicator" style="position: absolute; bottom: -12px; left: 0; width: 100%; height: 3px; background: transparent; border-radius: 3px;"></span>
                    </button>
                </div>

                <div id="all-events-tab" class="tab-content">
                    <div class="dashboard-header student-events-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
                        <div>
                            <h2>Available Events</h2>
                            <p>Browse every upcoming session and open details with one click.</p>
                        </div>
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <div class="custom-select-wrapper" style="position: relative; user-select: none; width: 150px; z-index: 50;">
                                <div class="custom-select-trigger" style="padding: 10px 15px; border-radius: 8px; border: 1px solid #e2e8f0; background: white; color: #475569; font-family: inherit; font-weight: 500; font-size: 0.95rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: all 0.2s;">
                                    <span class="custom-select-text">Sort By</span>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </div>
                                <div class="custom-select-options" style="position: absolute; top: calc(100% + 8px); left: 0; width: 100%; background: white; border: 1px solid #eef2f6; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.2s; overflow: hidden; padding: 6px;">
                                    <div class="custom-option" data-value="default" style="padding: 10px 15px; cursor: pointer; border-radius: 8px; font-size: 0.9rem; color: #475569; transition: all 0.2s; margin-bottom: 2px;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b'" onmouseout="this.style.background='transparent'; this.style.color='#475569'">Sort By</div>
                                    <div class="custom-option" data-value="titleAsc" style="padding: 10px 15px; cursor: pointer; border-radius: 8px; font-size: 0.9rem; color: #475569; transition: all 0.2s; margin-bottom: 2px;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b'" onmouseout="this.style.background='transparent'; this.style.color='#475569'">Title (A-Z)</div>
                                    <div class="custom-option" data-value="titleDesc" style="padding: 10px 15px; cursor: pointer; border-radius: 8px; font-size: 0.9rem; color: #475569; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b'" onmouseout="this.style.background='transparent'; this.style.color='#475569'">Title (Z-A)</div>
                                </div>
                            </div>
                            <select id="studentEventSort" style="display: none;">
                                <option value="default">Sort By</option>
                                <option value="titleAsc">Title (A-Z)</option>
                                <option value="titleDesc">Title (Z-A)</option>
                            </select>

                            <div style="position: relative;">
                                <input type="text" id="studentEventSearch" placeholder="Search event by title..." style="padding: 10px 15px 10px 35px; border-radius: 8px; border: 1px solid #e2e8f0; width: 220px; outline: none; transition: border-color 0.2s; font-family: inherit;" onfocus="this.style.borderColor='#548CA8'" onblur="this.style.borderColor='#e2e8f0'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </div>
                            <button id="aiRecommendBtn" style="background: linear-gradient(135deg, #FFD700, #FFA500); color: #2B4865; border: none; padding: 10px 18px; border-radius: 10px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(255, 165, 0, 0.3); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(255, 165, 0, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(255, 165, 0, 0.3)'">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                                AI Recommender
                            </button>
                        </div>
                    </div>

                    <?php if (!empty($evenements)): ?>
                        <div class="student-events-grid">
                            <?php foreach ($evenements as $event): ?>
                                <article class="student-event-card">
                                    <div class="student-event-topline">
                                        <span class="student-event-status"><?= htmlspecialchars(strtoupper($event['statut'] ?? 'PLANIFIE')) ?></span>
                                        <span class="student-event-type"><?= htmlspecialchars($event['type'] ?? 'General') ?></span>
                                    </div>
                                    <h3><?= htmlspecialchars(($event['titre'] ?? '') ?: ($event['title'] ?? 'Event')) ?></h3>
                                    <p><?= htmlspecialchars(substr((string) ($event['description'] ?? ''), 0, 140)) ?>...</p>

                                    <div class="student-event-meta">
                                        <span><strong>Date:</strong> <?= htmlspecialchars((string) (($event['date_debut'] ?? '') ?: date('Y-m-d', strtotime((string) ($event['event_date'] ?? 'now'))))) ?></span>
                                        <span><strong>Time:</strong> <?= htmlspecialchars((string) (!empty($event['heure_debut']) ? substr((string) $event['heure_debut'], 0, 5) : date('H:i', strtotime((string) ($event['event_date'] ?? 'now'))))) ?></span>
                                        <span><strong>Location:</strong> <?= htmlspecialchars((string) (($event['lieu'] ?? '') ?: ($event['location'] ?? 'TBA'))) ?></span>
                                        <span><strong>Resources:</strong> <?= (int) ($event['resource_count'] ?? 0) ?></span>
                                    </div>

                                    <?php
                                        $pDetails = $participationMap[$event['id']]['details'] ?? null;
                                        $pStatus = null;
                                        if ($pDetails) {
                                            $decoded = json_decode($pDetails, true);
                                            $pStatus = (json_last_error() === JSON_ERROR_NONE) ? ($decoded['status'] ?? null) : $pDetails;
                                        }
                                        if ($pStatus === 'pending'):
                                    ?>
                                        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; margin-bottom:10px;">
                                            <span style="background:#fff7ed;color:#f97316;padding:6px 14px;border-radius:50px;font-size:0.8rem;font-weight:700;display:inline-flex;align-items:center;gap:5px;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                                Pending Approval
                                            </span>
                                            <form method="POST" action="<?= APP_ENTRY ?>?url=student/cancel-participation/<?= (int)$event['id'] ?>" style="margin:0;">
                                                <button type="submit" style="background:#fef2f2;color:#ef4444;border:1px solid #fecaca;padding:6px 14px;border-radius:50px;font-size:0.8rem;font-weight:700;cursor:pointer;" onclick="return confirm('Cancel your participation request?')">Cancel</button>
                                            </form>
                                        </div>
                                    <?php elseif ($pStatus === 'approved'): ?>
                                        <span style="background:#f0fdf4;color:#22c55e;padding:6px 14px;border-radius:50px;font-size:0.8rem;font-weight:700;display:inline-flex;align-items:center;gap:5px; margin-bottom:10px;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            Participation Approved
                                        </span>
                                    <?php elseif ($pStatus === 'rejected'): ?>
                                        <span style="background:#fef2f2;color:#ef4444;padding:6px 14px;border-radius:50px;font-size:0.8rem;font-weight:700;display:inline-flex;align-items:center;gap:5px; margin-bottom:10px;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                            Participation Rejected
                                        </span>
                                    <?php else: ?>
                                        <form method="POST" action="<?= APP_ENTRY ?>?url=student/participate/<?= (int)$event['id'] ?>" style="margin-bottom:10px;">
                                            <button type="submit" id="btn-participate-<?= (int)$event['id'] ?>" class="btn btn-primary btn-block" style="background:linear-gradient(135deg,#548CA8,#2B4865);border:none;cursor:pointer;width:100%;">
                                                ✦ Participer
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="<?= APP_ENTRY ?>?url=student/evenement/<?= (int) $event['id'] ?>" class="btn btn-block" style="border:1.5px solid #548CA8;color:#548CA8;background:transparent;margin-top:6px;display:block;text-align:center;padding:10px;border-radius:8px;font-weight:600;text-decoration:none;">View Details</a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="table-container student-events-empty">
                            <h3>No Events Yet</h3>
                            <p>You will see upcoming events here as soon as they are published.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div id="my-participations-tab" class="tab-content" style="display: none;">
                    <div class="dashboard-header student-events-header" style="margin-bottom: 1.5rem;">
                        <div>
                            <h2>My Participations</h2>
                            <p>View and manage events you have joined.</p>
                        </div>
                    </div>

                    <?php
                    // Fetch participations dynamically for this tab
                    $evenementCtrl = new EvenementController();
                    $participations = $evenementCtrl->getParticipationsByUser((int) $_SESSION['user_id']);
                    ?>

                    <?php if (!empty($participations)): ?>
                        <div class="table-container force-white-table table-responsive" style="border-radius: 14px; overflow-x: auto;">
                            <table class="force-white-table__table" style="width: 100%; border-collapse: collapse; text-align: left; border: none !important;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #e2e8f0 !important;">
                                        <th style="padding: 1.2rem 1.5rem; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Event Title</th>
                                        <th style="padding: 1.2rem 1.5rem; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Date & Location</th>
                                        <th style="padding: 1.2rem 1.5rem; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: center;">Status</th>
                                        <th style="padding: 1.2rem 1.5rem; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($participations as $p): ?>
                                        <tr class="force-white-table__row">
                                            <td style="padding: 1.2rem 1rem;">
                                                <div style="font-weight: 700; color: #1e293b !important; font-size: 0.95rem;"><?= htmlspecialchars($p['event_title'] ?? 'Event') ?></div>
                                                <div style="font-size: 0.8rem; color: #64748b !important; margin-top: 2px;"><?= htmlspecialchars($p['event_type'] ?? 'General') ?></div>
                                            </td>
                                            <td style="padding: 1.2rem 1.5rem;">
                                                <div style="font-weight: 600; color: #2B4865 !important; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                                    <?= htmlspecialchars((string) (!empty($p['date_debut']) ? date('M d, Y', strtotime($p['date_debut'])) : 'N/A')) ?>
                                                    <span style="color: #94a3b8 !important; font-weight: 400; font-size: 0.8rem;"><?= htmlspecialchars((string) (!empty($p['heure_debut']) ? substr($p['heure_debut'], 0, 5) : '—')) ?></span>
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 6px; color: #64748b !important; font-size: 0.85rem; font-weight: 500;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#E19864" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                                    <?= htmlspecialchars((string) ($p['lieu'] ?: 'TBA')) ?>
                                                </div>
                                            </td>
                                            <td style="padding: 1.2rem 1.5rem; text-align: center;">
                                                <?php 
                                                    $statusKey = strtolower((string) ($p['status'] ?? 'pending'));
                                                    $statutColor = '#64748b'; $statutBg = '#f1f5f9';
                                                    $cursor = 'default'; $onclick = '';
                                                    if ($statusKey === 'approved') {
                                                        $statutColor = '#22c55e'; $statutBg = '#f0fdf4';
                                                    } elseif ($statusKey === 'rejected') {
                                                        $statutColor = '#ef4444'; $statutBg = '#fef2f2';
                                                        $cursor = 'pointer';
                                                        $reasonText = !empty($p['rejection_reason']) ? addslashes($p['rejection_reason']) : 'No reason provided.';
                                                        $updateDate = !empty($p['updated_at']) ? date('d M Y at H:i', strtotime($p['updated_at'])) : 'Recently';
                                                        $onclick = "showRejectionReason('{$reasonText}', '{$updateDate}')";
                                                    } elseif ($statusKey === 'pending') {
                                                        $statutColor = '#3b82f6'; $statutBg = '#eff6ff';
                                                    }
                                                ?>
                                                <span 
                                                    onclick="<?= $onclick ?>"
                                                    style="background: <?= $statutBg ?>; color: <?= $statutColor ?>; padding: 6px 14px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; cursor: <?= $cursor ?>; transition: transform 0.2s;"
                                                    <?php if($statusKey === 'rejected'): ?>
                                                        onmouseover="this.style.transform='scale(1.05)'"
                                                        onmouseout="this.style.transform='scale(1)'"
                                                        title="Click to view reason"
                                                    <?php endif; ?>
                                                >
                                                    <?= htmlspecialchars($statusKey) ?>
                                                </span>
                                            </td>
                                            <td style="padding: 1.2rem 1.5rem; text-align: right;">
                                                <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                                    <a href="<?= APP_ENTRY ?>?url=student/evenement/<?= (int) $p['evenement_id'] ?>" 
                                                       title="View Details"
                                                       style="background: #f8fafc; border: 1.5px solid #e2e8f0; color: #64748b; text-decoration: none; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s;" 
                                                       onmouseover="this.style.borderColor='#548CA8'; this.style.color='#548CA8'" 
                                                       onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#64748b'">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                            <circle cx="12" cy="12" r="3"></circle>
                                                        </svg>
                                                    </a>

                                                    <?php if ($statusKey === 'approved'): ?>
                                                        <a href="<?= APP_ENTRY ?>?url=student/download-ticket/<?= (int)$p['evenement_id'] ?>" 
                                                           title="Download Ticket" target="_blank"
                                                           style="background: #f0fdf4; border: 1.5px solid #bbf7d0; color: #16a34a; text-decoration: none; padding: 0 12px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s; font-size: 0.75rem; font-weight: 700; gap: 6px;">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                            Ticket
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if ($statusKey === 'pending'): ?>
                                                        <form method="POST" action="<?= APP_ENTRY ?>?url=student/cancel-participation/<?= (int)$p['evenement_id'] ?>" style="margin:0;">
                                                            <button type="submit" 
                                                                    title="Cancel Participation"
                                                                    style="background: #fef2f2; border: 1.5px solid #fecaca; color: #ef4444; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s; cursor: pointer;" 
                                                                    onmouseover="this.style.background='#fee2e2'" 
                                                                    onmouseout="this.style.background='#fef2f2'"
                                                                    onclick="return confirm('Cancel this participation?')">
                                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="table-container student-events-empty" style="text-align: center; padding: 4rem; background: white; border-radius: 12px; border: 2px dashed #eef2f6;">
                            <div style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </div>
                            <h3>No Participations Yet</h3>
                            <p style="color: #64748b;">You haven't joined any events. Go to the "All Events" tab to find some!</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab Switching Logic
    const tabBtns = document.querySelectorAll('.student-tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');

            // Update buttons
            tabBtns.forEach(b => {
                b.classList.remove('active');
                b.style.color = '#64748b';
                b.querySelector('.tab-indicator').style.background = 'transparent';
            });
            this.classList.add('active');
            this.style.color = '#548CA8';
            this.querySelector('.tab-indicator').style.background = '#548CA8';

            // Update content
            tabContents.forEach(content => {
                content.style.display = content.id === targetTab + '-tab' ? 'block' : 'none';
            });
        });
    });

    const wrapper = document.querySelector('.custom-select-wrapper');
    if (wrapper) {
        const trigger = wrapper.querySelector('.custom-select-trigger');
        const options = wrapper.querySelector('.custom-select-options');
        const text = wrapper.querySelector('.custom-select-text');
        const svg = wrapper.querySelector('svg');
        const hiddenSelect = document.getElementById('studentEventSort');
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
    }

    const searchInput = document.getElementById('studentEventSearch');
    const sortSelect = document.getElementById('studentEventSort');
    const grid = document.querySelector('.student-events-grid');
    
    if (grid) {
        let cards = Array.from(grid.querySelectorAll('.student-event-card'));
        
        // Search
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                cards.forEach(card => {
                    const titleEl = card.querySelector('h3');
                    if (titleEl) {
                        const titleText = titleEl.textContent.toLowerCase();
                        card.style.display = titleText.includes(filter) ? '' : 'none';
                    }
                });
            });
        }

        // Sort
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const val = this.value;
                if (val === 'default') return;

                const visibleCards = cards.filter(card => card.style.display !== 'none');
                const hiddenCards = cards.filter(card => card.style.display === 'none');

                visibleCards.sort((a, b) => {
                    const aTitle = a.querySelector('h3').textContent.trim();
                    const bTitle = b.querySelector('h3').textContent.trim();
                    
                    if (val === 'titleAsc') return aTitle.localeCompare(bTitle);
                    if (val === 'titleDesc') return bTitle.localeCompare(aTitle);
                    return 0;
                });

                grid.innerHTML = '';
                visibleCards.forEach(card => grid.appendChild(card));
                hiddenCards.forEach(card => grid.appendChild(card));
            });
        }
    }

    /**
     * AI Recommendations Logic
     */
    const aiBtn = document.getElementById('aiRecommendBtn');
    if (aiBtn) {
        aiBtn.addEventListener('click', function() {
            const btn = this;
            const originalContent = btn.innerHTML;
            
            // Loading State
            btn.disabled = true;
            btn.innerHTML = '<svg class="spin" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="animation: spin 1s linear infinite;"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path></svg> Analyzing...';
            btn.style.opacity = '0.8';

            fetch('<?= APP_ENTRY ?>?url=student/recommend-events')
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                    btn.style.opacity = '1';

                    if (data.success) {
                        const container = document.getElementById('aiRecommendationsList');
                        container.innerHTML = '';
                        
                        data.recommendations.forEach(rec => {
                            const card = document.createElement('div');
                            card.className = 'ai-rec-card';
                            card.style.cssText = 'background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; margin-bottom: 15px; transition: all 0.2s; cursor: pointer;';
                            card.innerHTML = `
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                    <h4 style="margin: 0; color: #1e293b; font-size: 1.1rem; font-weight: 700;">${rec.title}</h4>
                                    <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 50px; font-size: 0.75rem; font-weight: 800;">Recommended</span>
                                </div>
                                <p style="margin: 0 0 15px 0; color: #64748b; font-size: 0.9rem; line-height: 1.5;">${rec.reason}</p>
                                <a href="<?= APP_ENTRY ?>?url=student/evenement/${rec.id}" style="color: #548CA8; text-decoration: none; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 5px;">
                                    View Event Details 
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </a>
                            `;
                            card.onmouseover = () => { card.style.borderColor = '#548CA8'; card.style.background = '#fff'; card.style.transform = 'translateX(5px)'; };
                            card.onmouseout = () => { card.style.borderColor = '#e2e8f0'; card.style.background = '#f8fafc'; card.style.transform = 'translateX(0)'; };
                            container.appendChild(card);
                        });

                        document.getElementById('aiRecommendModal').style.display = 'flex';
                    } else {
                        alert('AI Error: ' + (data.message || data.error || 'Could not get recommendations.'));
                    }
                })
                .catch(err => {
                    console.error(err);
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                    btn.style.opacity = '1';
                    alert('A network error occurred. Please try again.');
                });
        });
    }
});

function closeAiModal() {
    document.getElementById('aiRecommendModal').style.display = 'none';
}

function showRejectionReason(reason, date) {
    document.getElementById('rejectionModalDate').textContent = date;
    document.getElementById('rejectionModalReason').textContent = reason;
    document.getElementById('rejectionModal').style.display = 'flex';
}

function closeRejectionModal() {
    document.getElementById('rejectionModal').style.display = 'none';
}
</script>

<!-- AI RECOMMENDATIONS MODAL -->
<div id="aiRecommendModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(8px); z-index: 10001; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; padding: 20px; box-sizing: border-box;">
    <div style="background: white; border-radius: 32px; width: 100%; max-width: 550px; max-height: 95vh; display: flex; flex-direction: column; padding: 30px; box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.25); position: relative; animation: modalPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); box-sizing: border-box;">
        <button onclick="closeAiModal()" style="position: absolute; top: 20px; right: 20px; background: #f1f5f9; border: none; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #64748b; cursor: pointer; transition: all 0.2s; z-index: 2;" onmouseover="this.style.background='#e2e8f0'; this.style.color='#1e293b'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b'">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>

        <div style="text-align: center; margin-bottom: 25px; flex-shrink: 0;">
            <div style="background: linear-gradient(135deg, #FFC107, #FF9800); width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; color: #ffffff; margin: 0 auto 20px; box-shadow: 0 8px 16px rgba(255, 152, 0, 0.25);">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg>
            </div>
            <h2 style="margin: 0; font-size: 1.8rem; font-weight: 800; color: #0f172a; letter-spacing: -0.03em;">Smart Recommendations</h2>
            <p style="margin: 8px 0 0; font-size: 1rem; color: #64748b;">AI-tailored events just for you, <?= htmlspecialchars($userName ?? ($_SESSION['user_name'] ?? 'Student')) ?>.</p>
        </div>

        <div id="aiRecommendationsList" style="overflow-y: auto; padding-right: 5px; scrollbar-width: thin; flex-grow: 1;">
            <!-- Recommendations will be injected here -->
        </div>

        <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eef2f6; text-align: center; flex-shrink: 0;">
            <p style="font-size: 0.85rem; color: #94a3b8; font-weight: 500; margin: 0;">Powered by Gemini Pro AI • Appolios Intelligent Learning</p>
        </div>
    </div>
</div>

<!-- REJECTION MODAL -->
<div id="rejectionModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(5px); z-index: 10000; align-items: center; justify-content: center; font-family: 'Inter', sans-serif;">
    <div style="background: white; border-radius: 28px; width: 100%; max-width: 480px; padding: 35px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15); position: relative; animation: modalPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
        <!-- Close Button -->
        <button onclick="closeRejectionModal()" style="position: absolute; top: 20px; right: 20px; background: #f8fafc; border: none; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #64748b; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b'" onmouseout="this.style.background='#f8fafc'; this.style.color='#64748b'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>

        <div style="display: flex; align-items: flex-start; gap: 20px; margin-bottom: 25px;">
            <div style="background: #fff1f2; width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; color: #ef4444; flex-shrink: 0;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </div>
            <div>
                <h2 style="margin: 0; font-size: 1.5rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em;">Event Rejected</h2>
                <p style="margin: 4px 0 0; font-size: 0.95rem; color: #64748b;">Rejected on <span id="rejectionModalDate"></span></p>
            </div>
        </div>

        <div style="background: #f8fafc; border: 1px solid #eef2f6; border-radius: 20px; padding: 25px; margin-bottom: 25px;">
            <h3 style="margin: 0 0 10px; font-size: 0.75rem; font-weight: 800; color: #548CA8; text-transform: uppercase; letter-spacing: 0.1em;">Reason for Refusal</h3>
            <p id="rejectionModalReason" style="margin: 0; font-size: 1.05rem; color: #334155; line-height: 1.6; font-weight: 500;"></p>
        </div>

        <button onclick="closeRejectionModal()" style="width: 100%; background: white; border: 1.5px solid #e2e8f0; color: #1e293b; padding: 14px; border-radius: 16px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1'" onmouseout="this.style.background='white'; this.style.borderColor='#e2e8f0'">
            Understood
        </button>
    </div>
</div>

<style>
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.9) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Participation table — light + dark (do not merge body.dark-mode with light rules) */
.force-white-table {
    background: #ffffff !important;
    border: 1px solid #eef2f6 !important;
    border-radius: 14px !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02) !important;
}

.force-white-table table {
    color: #1e293b !important;
}

.force-white-table table th {
    background: #f8fafc !important;
    color: #475569 !important;
    border-bottom: 2px solid #e2e8f0 !important;
}

.force-white-table thead tr {
    background: #f8fafc !important;
}

.force-white-table table td {
    background: #ffffff !important;
    border-bottom: 1px solid #eef2f6 !important;
}

.force-white-table tbody tr:hover td {
    background: #f8fafc !important;
}

body.dark-mode .force-white-table {
    background: rgba(30, 41, 59, 0.96) !important;
    border-color: rgba(84, 140, 168, 0.28) !important;
    box-shadow: 0 8px 28px rgba(0, 0, 0, 0.35) !important;
}

body.dark-mode .force-white-table table {
    color: #e2e8f0 !important;
}

body.dark-mode .force-white-table table th {
    background: rgba(15, 23, 42, 0.92) !important;
    color: #cbd5e1 !important;
    border-bottom-color: rgba(84, 140, 168, 0.35) !important;
}

body.dark-mode .force-white-table thead tr {
    background: rgba(15, 23, 42, 0.92) !important;
}

body.dark-mode .force-white-table table td {
    background: rgba(30, 41, 59, 0.88) !important;
    color: #e2e8f0 !important;
    border-bottom-color: rgba(51, 65, 85, 0.45) !important;
}

body.dark-mode .force-white-table tbody tr:hover td {
    background: rgba(51, 65, 85, 0.5) !important;
}
</style>
