<?php
/**
 * APPOLIOS - Student Evenements Catalog
 */
$participationMap = $participationMap ?? [];
?>

<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <aside class="admin-sidebar student-space-sidebar">
                <div class="student-sidebar-panel">
                    <a class="student-sidebar-brand" href="<?= APP_ENTRY ?>?url=student/dashboard">
                        <span class="student-sidebar-brand-mark" aria-hidden="true">a</span>
                        <span class="student-sidebar-brand-text">Appolios</span>
                    </a>

                    <nav class="admin-sidebar-nav student-sidebar-nav" aria-label="Front Office Navigation">
                        <a href="<?= APP_ENTRY ?>?url=student/dashboard" class="admin-side-link">
                            <span class="admin-side-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 10.5L12 3l9 7.5"></path>
                                    <path d="M5 9.5V21h14V9.5"></path>
                                </svg>
                            </span>
                            <span>Dashboard</span>
                        </a>

                        <p class="student-sidebar-section">Pages</p>

                        <a href="<?= APP_ENTRY ?>?url=student/courses" class="admin-side-link">
                            <span class="admin-side-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 6h16v12H4z"></path>
                                    <path d="M8 10h8"></path>
                                    <path d="M8 14h5"></path>
                                </svg>
                            </span>
                            <span>Courses</span>
                        </a>

                        <a href="<?= APP_ENTRY ?>?url=student/evenements" class="admin-side-link active">
                            <span class="admin-side-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M8 2v4"></path>
                                    <path d="M16 2v4"></path>
                                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                    <path d="M3 10h18"></path>
                                </svg>
                            </span>
                            <span>Events</span>
                        </a>

                        <a href="<?= APP_ENTRY ?>?url=student/my-courses" class="admin-side-link">
                            <span class="admin-side-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 6h7v12H3z"></path>
                                    <path d="M14 6h7v12h-7z"></path>
                                </svg>
                            </span>
                            <span>My Courses</span>
                        </a>

                        <a href="<?= APP_ENTRY ?>?url=student/profile" class="admin-side-link">
                            <span class="admin-side-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="8" r="4"></circle>
                                    <path d="M4 20c2-4 5-6 8-6s6 2 8 6"></path>
                                </svg>
                            </span>
                            <span>Profile</span>
                        </a>
                    </nav>
                </div>
            </aside>

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
                                <input type="text" id="studentEventSearch" placeholder="Search event by title..." style="padding: 10px 15px 10px 35px; border-radius: 8px; border: 1px solid #e2e8f0; width: 250px; outline: none; transition: border-color 0.2s; font-family: inherit;" onfocus="this.style.borderColor='#548CA8'" onblur="this.style.borderColor='#e2e8f0'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </div>
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
                                        $pStatus = $participationMap[$event['id']] ?? null;
                                        if ($pStatus === 'pending'):
                                    ?>
                                        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                                            <span style="background:#fff7ed;color:#f97316;padding:6px 14px;border-radius:50px;font-size:0.8rem;font-weight:700;display:inline-flex;align-items:center;gap:5px;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                                Pending Approval
                                            </span>
                                            <form method="POST" action="<?= APP_ENTRY ?>?url=student/cancel-participation/<?= (int)$event['id'] ?>" style="margin:0;">
                                                <button type="submit" style="background:#fef2f2;color:#ef4444;border:1px solid #fecaca;padding:6px 14px;border-radius:50px;font-size:0.8rem;font-weight:700;cursor:pointer;" onclick="return confirm('Cancel your participation request?')">Cancel</button>
                                            </form>
                                        </div>
                                    <?php elseif ($pStatus === 'approved'): ?>
                                        <span style="background:#f0fdf4;color:#22c55e;padding:6px 14px;border-radius:50px;font-size:0.8rem;font-weight:700;display:inline-flex;align-items:center;gap:5px;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            Participation Approved
                                        </span>
                                    <?php elseif ($pStatus === 'rejected'): ?>
                                        <span style="background:#fef2f2;color:#ef4444;padding:6px 14px;border-radius:50px;font-size:0.8rem;font-weight:700;display:inline-flex;align-items:center;gap:5px;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                            Participation Rejected
                                        </span>
                                    <?php else: ?>
                                        <form method="POST" action="<?= APP_ENTRY ?>?url=student/participate/<?= (int)$event['id'] ?>" style="margin:0;">
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

                    <?php if (!empty($participations)): ?>
                        <div class="table-container" style="background: white; border-radius: 15px; border: 1px solid #eef2f6; box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden;">
                            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                <thead>
                                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                        <th style="padding: 1.2rem 1.5rem; color: #475569; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Event Title</th>
                                        <th style="padding: 1.2rem 1.5rem; color: #475569; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Date & Location</th>
                                        <th style="padding: 1.2rem 1.5rem; color: #475569; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: center;">Status</th>
                                        <th style="padding: 1.2rem 1.5rem; color: #475569; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($participations as $p): ?>
                                        <tr style="border-bottom: 1px solid #eef2f6; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                            <td style="padding: 1.2rem 1rem;">
                                                <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;"><?= htmlspecialchars($p['titre'] ?: $p['title']) ?></div>
                                                <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;"><?= htmlspecialchars($p['type'] ?? 'General') ?></div>
                                            </td>
                                            <td style="padding: 1.2rem 1.5rem;">
                                                <div style="font-weight: 600; color: #2B4865; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                                    <?= htmlspecialchars((string) (!empty($p['date_debut']) ? date('M d, Y', strtotime($p['date_debut'])) : 'N/A')) ?>
                                                    <span style="color: #94a3b8; font-weight: 400; font-size: 0.8rem;"><?= htmlspecialchars((string) (!empty($p['heure_debut']) ? substr($p['heure_debut'], 0, 5) : '—')) ?></span>
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 6px; color: #64748b; font-size: 0.85rem; font-weight: 500;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#E19864" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                                    <?= htmlspecialchars((string) ($p['lieu'] ?: 'TBA')) ?>
                                                </div>
                                            </td>
                                            <td style="padding: 1.2rem 1.5rem; text-align: center;">
                                                <?php 
                                                    $statusKey = strtolower((string) ($p['p_status'] ?? 'pending'));
                                                    $statutColor = '#64748b'; $statutBg = '#f1f5f9';
                                                    $cursor = 'default'; $onclick = '';
                                                    if ($statusKey === 'approved') {
                                                        $statutColor = '#22c55e'; $statutBg = '#f0fdf4';
                                                    } elseif ($statusKey === 'rejected') {
                                                        $statutColor = '#ef4444'; $statutBg = '#fef2f2';
                                                        $cursor = 'pointer';
                                                        $reasonText = !empty($p['rejection_reason']) ? addslashes($p['rejection_reason']) : 'No reason provided.';
                                                        $updateDate = !empty($p['p_update_date']) ? date('d M Y at H:i', strtotime($p['p_update_date'])) : 'Recently';
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
                                                    <!-- Details Button -->
                                                    <a href="<?= APP_ENTRY ?>?url=student/evenement/<?= (int) $p['id'] ?>" 
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
                                                        <a href="<?= APP_ENTRY ?>?url=student/download-ticket/<?= (int)$p['p_id'] ?>" 
                                                           title="Download Ticket" target="_blank"
                                                           style="background: #f0fdf4; border: 1.5px solid #bbf7d0; color: #16a34a; text-decoration: none; padding: 0 12px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s; font-size: 0.75rem; font-weight: 700; gap: 6px;">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                            Ticket
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if ($statusKey === 'pending'): ?>
                                                        <!-- Cancel Button -->
                                                        <form method="POST" action="<?= APP_ENTRY ?>?url=student/cancel-participation/<?= (int)$p['id'] ?>" style="margin:0;">
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

    const searchInput = document.getElementById('studentEventSearch');
    const sortSelect = document.getElementById('studentEventSort');
    const grid = document.querySelector('.student-events-grid');
    if (!grid) return;

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

            // Separate visible and hidden to avoid messing up the filtered view
            const visibleCards = cards.filter(card => card.style.display !== 'none');
            const hiddenCards = cards.filter(card => card.style.display === 'none');

            visibleCards.sort((a, b) => {
                const aTitle = a.querySelector('h3').textContent.trim();
                const bTitle = b.querySelector('h3').textContent.trim();
                
                if (val === 'titleAsc') {
                    return aTitle.localeCompare(bTitle);
                } else if (val === 'titleDesc') {
                    return bTitle.localeCompare(aTitle);
                }
                return 0;
            });

            // Re-append to DOM
            grid.innerHTML = '';
            visibleCards.forEach(card => grid.appendChild(card));
            hiddenCards.forEach(card => grid.appendChild(card));
        });
    }
});

function showRejectionReason(reason, date) {
    document.getElementById('rejectionModalDate').textContent = date;
    document.getElementById('rejectionModalReason').textContent = reason;
    document.getElementById('rejectionModal').style.display = 'flex';
}

function closeRejectionModal() {
    document.getElementById('rejectionModal').style.display = 'none';
}
</script>

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
</style>
