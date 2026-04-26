<?php
/**
 * APPOLIOS - Admin Evenement Requests
 */
?>

<section class="neo-auth-wrap neo-login-page" style="padding: 2rem; background: #fdfdfd; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif;">
    <div class="neo-glass-card neo-auth-grid" style="max-width: 1150px; width: 100%; background: #ffffff; border-radius: 20px; box-shadow: 0 15px 40px rgba(43, 72, 101, 0.08); border: 1px solid rgba(233, 241, 250, 0.8); overflow: hidden; display: grid; grid-template-columns: 1fr 1.1fr; gap: 0;">
        
        <!-- Left Side: Visual & Info -->
        <aside class="neo-auth-info" style="background: #fcfcfc; padding: 3.5rem 3rem; display: flex; flex-direction: column; justify-content: space-between; border-right: 1px solid #eef2f6; position: relative; overflow: hidden;">
            <!-- Decorative blobs -->
            <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: #e9f1fa; border-radius: 50%; z-index: 0; opacity: 0.7;"></div>
            <div style="position: absolute; bottom: 10%; right: -30px; width: 150px; height: 150px; background: #e1edf7; border-radius: 50%; z-index: 0; opacity: 0.5;"></div>

            <div style="position: relative; z-index: 2;">


                <a href="<?= APP_ENTRY ?>?url=event/evenements" style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #548CA8; font-weight: 600; text-decoration: none; margin-bottom: 2rem; transition: color 0.2s;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Back to Evenements
                </a>

                <h2 style="font-size: 2.5rem; font-weight: 800; color: #2B4865; line-height: 1.15; margin: 0 0 1rem 0; letter-spacing: -0.02em;">
                    Teacher<br><span style="color: #548CA8;">Requests</span>
                </h2>
                <p style="color: #64748b; font-size: 1.05rem; line-height: 1.6; margin: 0 0 2rem 0; max-width: 90%;">
                    Review and process evenement proposals from faculty members. Ensure high-quality events for our students.
                </p>

                <div class="neo-badges" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <span style="background: #e9f1fa; color: #548CA8; padding: 6px 14px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">Pending Review</span>
                    <span style="background: rgba(225, 152, 100, 0.15); color: #E19864; padding: 6px 14px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">Admin Actions</span>
                </div>
            </div>

            <!-- Image mimicking login page -->
            <div style="position: relative; z-index: 2; margin-top: 3rem; text-align: center;">
                <div style="position: absolute; top: 10%; left: 5%; width: 90%; height: 80%; background: #E19864; border-radius: 20px; transform: rotate(-3deg); z-index: -1; opacity: 0.2;"></div>
                <img src="<?= APP_URL ?>/View/assets/images/instructor/06.jpg" alt="Teacher Illustration" style="width: 100%; max-width: 320px; border-radius: 16px; box-shadow: 0 12px 24px rgba(43, 72, 101, 0.15); border: 4px solid #fff; object-fit: cover; aspect-ratio: 4/3;">
            </div>
        </aside>

        <!-- Right Side: Forms/Lists -->
        <div class="neo-auth-form" style="padding: 3.5rem 3rem; background: #ffffff; display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
                <div>
                    <h2 style="font-size: 1.8rem; font-weight: 700; color: #2B4865; margin: 0 0 0.4rem 0;">Action Required</h2>
                    <p style="color: #64748b; font-size: 0.95rem; margin: 0;">Approve or reject the following submissions.</p>
                </div>
                <div style="background: #e9f1fa; color: #2B4865; font-weight: 700; padding: 6px 12px; border-radius: 8px; font-size: 0.9rem;">
                    <?= count($requests ?? []) ?> Pending
                </div>
            </div>

            <div style="overflow-y: auto; padding-right: 10px; flex-grow: 1; max-height: 500px; display: flex; flex-direction: column; gap: 1.25rem;">
                <?php if (!empty($requests)): ?>
                    <?php foreach ($requests as $event): ?>
                        <div style="background: #ffffff; border: 1.5px solid #e9f1fa; border-radius: 14px; padding: 1.5rem; transition: all 0.2s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.02);" onmouseover="this.style.borderColor='#b6d0e2'; this.style.boxShadow='0 8px 25px rgba(43,72,101,0.06)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#e9f1fa'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.02)'; this.style.transform='translateY(0)'">
                            
                            <!-- Header: Teacher info -->
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.2rem;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 44px; height: 44px; background: linear-gradient(135deg, #548CA8 0%, #355C7D 100%); color: #fff; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem; box-shadow: 0 4px 10px rgba(53, 92, 125, 0.2);">
                                        <?= strtoupper(substr($event['creator_name'] ?? 'T', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h4 style="margin: 0 0 2px 0; color: #2B4865; font-size: 1.05rem; font-weight: 700;"><?= htmlspecialchars($event['creator_name'] ?? 'Teacher') ?></h4>
                                        <span style="color: #64748b; font-size: 0.85rem; display: flex; align-items: center; gap: 4px;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                            <?= htmlspecialchars($event['creator_email'] ?? '-') ?>
                                        </span>
                                    </div>
                                </div>
                                <span style="color: #94a3b8; font-size: 0.8rem; font-weight: 600;">
                                    <?= date('M d', strtotime($event['created_at'] ?? 'now')) ?>
                                </span>
                            </div>

                            <!-- Content: Title and Meta -->
                            <h3 style="margin: 0 0 0.75rem 0; color: #1e293b; font-size: 1.2rem; font-weight: 600; line-height: 1.3;">
                                <?= htmlspecialchars(($event['titre'] ?? '') ?: ($event['title'] ?? '')) ?>
                            </h3>
                            
                            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; background: #f8fafc; padding: 10px; border-radius: 8px;">
                                <div style="display: flex; align-items: center; gap: 6px; color: #475569; font-size: 0.85rem; font-weight: 500;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#548CA8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    <?= htmlspecialchars((string) (($event['date_debut'] ?? '') ?: 'N/A')) ?>
                                </div>
                                <div style="display: flex; align-items: center; gap: 6px; color: #475569; font-size: 0.85rem; font-weight: 500;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#E19864" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    <?= htmlspecialchars((string) (($event['lieu'] ?? '') ?: ($event['location'] ?? 'TBA'))) ?>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div style="display: flex; gap: 10px; align-items: stretch; flex-wrap: wrap;">
                                <!-- View Details Button -->
                                <button type="button"
                                    onclick="openEventModal(<?= htmlspecialchars(json_encode([
                                        'id'           => $event['id'],
                                        'title'        => ($event['titre'] ?? '') ?: ($event['title'] ?? ''),
                                        'description'  => $event['description'] ?? '',
                                        'date_debut'   => $event['date_debut'] ?? '',
                                        'date_fin'     => $event['date_fin'] ?? '',
                                        'heure_debut'  => $event['heure_debut'] ?? '',
                                        'heure_fin'    => $event['heure_fin'] ?? '',
                                        'lieu'         => ($event['lieu'] ?? '') ?: ($event['location'] ?? ''),
                                        'capacite_max' => $event['capacite_max'] ?? '',
                                        'type'         => $event['type'] ?? '',
                                        'statut'       => $event['statut'] ?? '',
                                        'creator_name' => $event['creator_name'] ?? '',
                                        'creator_email'=> $event['creator_email'] ?? '',
                                        'created_at'   => $event['created_at'] ?? '',
                                        'ressources'   => $event['ressources'] ?? ['rule'=>[],'materiel'=>[],'plan'=>[]],
                                    ]), ENT_QUOTES) ?>)"
                                    style="background: #f0f6fb; border: 1.5px solid #b6d0e2; color: #548CA8; height: 42px; padding: 0 1.2rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.2s;"
                                    onmouseover="this.style.background='#e9f1fa'; this.style.borderColor='#548CA8';"
                                    onmouseout="this.style.background='#f0f6fb'; this.style.borderColor='#b6d0e2';">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                    Details
                                </button>
                                <form action="<?= APP_ENTRY ?>?url=event/approve-evenement/<?= (int) $event['id'] ?>" method="POST" style="flex: 0 0 auto;">
                                    <button type="submit" style="background: linear-gradient(135deg, #548CA8 0%, #355C7D 100%); color: #fff; border: none; padding: 0 1.2rem; height: 42px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; box-shadow: 0 4px 10px rgba(84, 140, 168, 0.2);" onmouseover="this.style.boxShadow='0 6px 15px rgba(84, 140, 168, 0.3)'; this.style.transform='translateY(-1px)'" onmouseout="this.style.boxShadow='0 4px 10px rgba(84, 140, 168, 0.2)'; this.style.transform='translateY(0)'">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        Approve
                                    </button>
                                </form>
                                <form action="<?= APP_ENTRY ?>?url=event/reject-evenement/<?= (int) $event['id'] ?>" method="POST" style="flex: 1; display: flex; flex-direction: column; gap: 8px;" novalidate>
                                    <div style="display: flex; gap: 8px; width: 100%;">
                                        <input type="text" name="rejection_reason" placeholder="Rejection reason..." style="flex: 1; background: <?= isset($errors['rejection_reason_' . $event['id']]) ? '#fef2f2' : '#fdfdfd' ?>; border: 1.5px solid <?= isset($errors['rejection_reason_' . $event['id']]) ? '#ef4444' : '#e2e8f0' ?>; border-radius: 8px; padding: 0 1rem; height: 42px; color: #1e293b; font-size: 0.9rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='<?= isset($errors['rejection_reason_' . $event['id']]) ? '#ef4444' : '#E19864' ?>'" onblur="this.style.borderColor='<?= isset($errors['rejection_reason_' . $event['id']]) ? '#ef4444' : '#e2e8f0' ?>'">
                                        <button type="submit" style="background: #fff; border: 1.5px solid #e2e8f0; color: #64748b; height: 42px; padding: 0 1.2rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: all 0.2s ease;" onmouseover="this.style.borderColor='#ef4444'; this.style.color='#ef4444'; this.style.background='#fef2f2'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#64748b'; this.style.background='#fff'">
                                            Reject
                                        </button>
                                    </div>
                                    <?php if (isset($errors['rejection_reason_' . $event['id']])): ?>
                                        <div style="color: #ef4444; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 6px; margin-top: -2px;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                            <?= htmlspecialchars($errors['rejection_reason_' . $event['id']]) ?>
                                        </div>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 4rem 2rem; background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 14px; margin-top: 1rem;">
                        <div style="color: #b6d0e2; margin-bottom: 1.5rem; display: flex; justify-content: center;">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <h3 style="color: #2B4865; margin: 0 0 0.5rem 0; font-size: 1.4rem; font-weight: 700;">You're all caught up!</h3>
                        <p style="color: #64748b; margin: 0; font-size: 1rem;">There are no pending evenement requests to review right now.</p>
                        <a href="<?= APP_ENTRY ?>?url=event/evenements" style="display: inline-block; margin-top: 1.5rem; background: #E19864; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 0.95rem; box-shadow: 0 4px 10px rgba(225, 152, 100, 0.3);">View All Events</a>
                    </div>
                <?php endif; ?>

                <!-- Rejected Requests Section -->
                <?php if (!empty($rejectedRequests)): ?>
                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #eef2f6;">
                        <h2 style="font-size: 1.4rem; font-weight: 700; color: #2B4865; margin: 0 0 1.25rem 0; display: flex; align-items: center; gap: 10px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                            Rejected Requests
                        </h2>
                        
                        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                            <?php foreach ($rejectedRequests as $event): ?>
                                <div style="background: #fff5f5; border: 1.5px solid #fee2e2; border-radius: 14px; padding: 1.5rem; position: relative; transition: transform 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                                    
                                    <!-- Header: Teacher info -->
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.2rem;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div style="width: 44px; height: 44px; background: #fee2e2; color: #ef4444; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem;">
                                                <?= strtoupper(substr($event['creator_name'] ?? 'T', 0, 1)) ?>
                                            </div>
                                            <div>
                                                <h4 style="margin: 0 0 2px 0; color: #7f1d1d; font-size: 1.05rem; font-weight: 700;"><?= htmlspecialchars($event['creator_name'] ?? 'Teacher') ?></h4>
                                                <span style="color: #991b1b; font-size: 0.85rem; display: flex; align-items: center; gap: 4px;">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                                    <?= htmlspecialchars($event['creator_email'] ?? '-') ?>
                                                </span>
                                            </div>
                                        </div>
                                        <span style="background: #fecaca; color: #b91c1c; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-align: right;">
                                            Rejected on<br><?= date('M d, Y', strtotime($event['updated_at'] ?? 'now')) ?>
                                        </span>
                                    </div>

                                    <!-- Content: Title and Reason -->
                                    <h3 style="margin: 0 0 0.75rem 0; color: #7f1d1d; font-size: 1.1rem; font-weight: 600;">
                                        <?= htmlspecialchars(($event['titre'] ?? '') ?: ($event['title'] ?? '')) ?>
                                    </h3>
                                    
                                    <div style="background: white; padding: 12px; border-radius: 8px; border-left: 4px solid #ef4444; margin-top: 10px;">
                                        <p style="margin: 0; color: #991b1b; font-size: 0.9rem; line-height: 1.5;">
                                            <strong>Reason:</strong> <?= htmlspecialchars($event['rejection_reason'] ?? 'No reason provided.') ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════ -->
<!-- EVENT DETAILS MODAL                         -->
<!-- ═══════════════════════════════════════════ -->
<div id="eventDetailsOverlay"
     onclick="if(event.target===this) closeEventModal()"
     style="display:none; position:fixed; inset:0; z-index:9999;
            background:rgba(15,25,45,0.45); backdrop-filter:blur(8px);
            -webkit-backdrop-filter:blur(8px);
            align-items:center; justify-content:center; padding:1.5rem;">

    <div id="eventDetailsModal"
         style="background:#fff; border-radius:22px; width:100%; max-width:580px;
                box-shadow:0 30px 80px rgba(43,72,101,0.25);
                transform:scale(0.92) translateY(20px);
                transition:transform 0.3s cubic-bezier(.34,1.56,.64,1), opacity 0.25s ease;
                opacity:0; overflow:hidden;">

        <!-- Modal Header -->
        <div style="background:linear-gradient(135deg,#2B4865 0%,#355C7D 100%); padding:1.8rem 2rem; display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                    <div style="background:rgba(255,255,255,0.15); border-radius:8px; padding:6px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                    <span style="color:rgba(255,255,255,0.75); font-size:0.8rem; font-weight:600; letter-spacing:0.08em; text-transform:uppercase;">Event Request Details</span>
                </div>
                <h2 id="modal-title" style="margin:0; color:#fff; font-size:1.4rem; font-weight:700; line-height:1.3; max-width:400px;"></h2>
            </div>
            <button onclick="closeEventModal()" style="background:rgba(255,255,255,0.15); border:none; border-radius:8px; width:34px; height:34px; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div style="padding:1.8rem 2rem; display:flex; flex-direction:column; gap:1.1rem; max-height:65vh; overflow-y:auto;">

            <!-- Teacher Info -->
            <div style="background:#f8fafc; border-radius:12px; padding:1rem 1.2rem; display:flex; align-items:center; gap:12px; border:1px solid #e9f1fa;">
                <div id="modal-avatar" style="width:42px; height:42px; border-radius:10px; background:linear-gradient(135deg,#548CA8,#355C7D); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.1rem; flex-shrink:0;"></div>
                <div>
                    <div id="modal-creator" style="font-weight:700; color:#2B4865; font-size:0.95rem;"></div>
                    <div id="modal-email" style="color:#64748b; font-size:0.82rem; margin-top:2px;"></div>
                </div>
                <div style="margin-left:auto;">
                    <span style="background:#e9f1fa; color:#548CA8; padding:4px 10px; border-radius:6px; font-size:0.75rem; font-weight:700; text-transform:uppercase;">Teacher</span>
                </div>
            </div>

            <!-- Date / Time / Location row -->
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px;">
                <div style="background:#f8fafc; border-radius:10px; padding:0.9rem; border:1px solid #e9f1fa; text-align:center;">
                    <div style="color:#94a3b8; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:4px;">Start</div>
                    <div id="modal-date-debut" style="color:#2B4865; font-weight:700; font-size:0.9rem;"></div>
                    <div id="modal-heure-debut" style="color:#548CA8; font-size:0.82rem; font-weight:600; margin-top:2px;"></div>
                </div>
                <div style="background:#f8fafc; border-radius:10px; padding:0.9rem; border:1px solid #e9f1fa; text-align:center;">
                    <div style="color:#94a3b8; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:4px;">End</div>
                    <div id="modal-date-fin" style="color:#2B4865; font-weight:700; font-size:0.9rem;"></div>
                    <div id="modal-heure-fin" style="color:#548CA8; font-size:0.82rem; font-weight:600; margin-top:2px;"></div>
                </div>
                <div style="background:#f8fafc; border-radius:10px; padding:0.9rem; border:1px solid #e9f1fa; text-align:center;">
                    <div style="color:#94a3b8; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:4px;">Capacity</div>
                    <div id="modal-capacity" style="color:#E19864; font-weight:700; font-size:1.1rem;"></div>
                    <div style="color:#94a3b8; font-size:0.78rem;">attendees</div>
                </div>
            </div>

            <!-- Location & Type -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <div style="background:#f8fafc; border-radius:10px; padding:0.9rem 1rem; border:1px solid #e9f1fa; display:flex; align-items:center; gap:10px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E19864" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <div>
                        <div style="color:#94a3b8; font-size:0.72rem; font-weight:700; text-transform:uppercase;">Location</div>
                        <div id="modal-lieu" style="color:#2B4865; font-weight:600; font-size:0.88rem; margin-top:2px;"></div>
                    </div>
                </div>
                <div style="background:#f8fafc; border-radius:10px; padding:0.9rem 1rem; border:1px solid #e9f1fa; display:flex; align-items:center; gap:10px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#548CA8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                    <div>
                        <div style="color:#94a3b8; font-size:0.72rem; font-weight:700; text-transform:uppercase;">Type</div>
                        <div id="modal-type" style="color:#2B4865; font-weight:600; font-size:0.88rem; margin-top:2px;"></div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div style="background:#f8fafc; border-radius:12px; padding:1.1rem 1.2rem; border:1px solid #e9f1fa;">
                <div style="color:#94a3b8; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:8px;">Description</div>
                <p id="modal-description" style="margin:0; color:#475569; font-size:0.9rem; line-height:1.65;"></p>
            </div>

            <!-- Submitted on -->
            <div style="display:flex; align-items:center; gap:6px; color:#94a3b8; font-size:0.82rem;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                Submitted on: <span id="modal-created-at" style="font-weight:600; color:#64748b;"></span>
            </div>

            <!-- Resources Section -->
            <div style="background:#f8fafc; border-radius:12px; padding:1.1rem 1.2rem; border:1px solid #e9f1fa;">
                <div style="color:#94a3b8; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:10px;">Resources</div>

                <!-- Tabs -->
                <div style="display:flex; gap:6px; margin-bottom:10px; flex-wrap:wrap;">
                    <button id="res-tab-rule"
                        onclick="switchResTab('rule')"
                        style="display:flex; align-items:center; gap:5px; padding:5px 12px; border-radius:8px; border:1.5px solid #b6d0e2; background:#e9f1fa; color:#548CA8; font-size:0.78rem; font-weight:700; cursor:pointer; transition:all 0.18s;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        Rules
                    </button>
                    <button id="res-tab-materiel"
                        onclick="switchResTab('materiel')"
                        style="display:flex; align-items:center; gap:5px; padding:5px 12px; border-radius:8px; border:1.5px solid #e9f1fa; background:#fff; color:#64748b; font-size:0.78rem; font-weight:700; cursor:pointer; transition:all 0.18s;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                        Materiel
                    </button>
                    <button id="res-tab-plan"
                        onclick="switchResTab('plan')"
                        style="display:flex; align-items:center; gap:5px; padding:5px 12px; border-radius:8px; border:1.5px solid #e9f1fa; background:#fff; color:#64748b; font-size:0.78rem; font-weight:700; cursor:pointer; transition:all 0.18s;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        Plans
                    </button>
                </div>

                <div id="res-panel-rule"     style="display:block;"></div>
                <div id="res-panel-materiel" style="display:none;"></div>
                <div id="res-panel-plan"     style="display:none;"></div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div style="padding:1.2rem 2rem; border-top:1px solid #eef2f6; display:flex; justify-content:flex-end;">
            <button onclick="closeEventModal()" style="background:#f1f5f9; border:none; color:#64748b; padding:0.6rem 1.5rem; border-radius:8px; font-weight:600; font-size:0.9rem; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">Close</button>
        </div>
    </div>
</div>

<script>
function openEventModal(ev) {
    var overlay = document.getElementById('eventDetailsOverlay');
    var modal   = document.getElementById('eventDetailsModal');

    // Populate fields
    document.getElementById('modal-title').textContent       = ev.title || 'Untitled';
    document.getElementById('modal-avatar').textContent      = (ev.creator_name || 'T').charAt(0).toUpperCase();
    document.getElementById('modal-creator').textContent     = ev.creator_name  || '—';
    document.getElementById('modal-email').textContent       = ev.creator_email || '—';
    document.getElementById('modal-date-debut').textContent  = ev.date_debut    || '—';
    document.getElementById('modal-heure-debut').textContent = ev.heure_debut   || '';
    document.getElementById('modal-date-fin').textContent    = ev.date_fin      || '—';
    document.getElementById('modal-heure-fin').textContent   = ev.heure_fin     || '';
    document.getElementById('modal-capacity').textContent    = ev.capacite_max  || '∞';
    document.getElementById('modal-lieu').textContent        = ev.lieu          || 'TBA';
    document.getElementById('modal-type').textContent        = ev.type          ? ev.type.charAt(0).toUpperCase() + ev.type.slice(1) : '—';
    document.getElementById('modal-description').textContent = ev.description   || 'No description provided.';
    document.getElementById('modal-created-at').textContent  = ev.created_at    || '—';

    // Populate resources
    var res = ev.ressources || {rule:[], materiel:[], plan:[]};
    renderResPanel('rule',     res.rule     || []);
    renderResPanel('materiel', res.materiel || []);
    renderResPanel('plan',     res.plan     || []);
    switchResTab('rule');

    // Show overlay
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Animate in
    requestAnimationFrame(function() {
        modal.style.opacity   = '1';
        modal.style.transform = 'scale(1) translateY(0)';
    });
}

function renderResPanel(type, items) {
    var panel = document.getElementById('res-panel-' + type);
    if (!panel) return;
    if (!items || items.length === 0) {
        panel.innerHTML = '<div style="text-align:center; padding:1rem; color:#94a3b8; font-size:0.83rem; background:#fff; border-radius:8px; border:1.5px dashed #e2e8f0;">No ' + (type === 'materiel' ? 'materials' : type + 's') + ' added yet.</div>';
        return;
    }
    var html = '<div style="display:flex; flex-direction:column; gap:6px;">';
    var accentColors = {rule:'#548CA8', materiel:'#E19864', plan:'#355C7D'};
    items.forEach(function(item) {
        var accent = accentColors[type] || '#548CA8';
        html += '<div style="background:#fff; border:1px solid #e9f1fa; border-left:3px solid ' + accent + '; border-radius:8px; padding:0.7rem 0.9rem;">';
        html +=   '<div style="font-weight:700; color:#2B4865; font-size:0.87rem;">' + escHtml(item.title) + '</div>';
        if (item.details) {
            html += '<div style="color:#64748b; font-size:0.81rem; margin-top:3px; line-height:1.55;">' + escHtml(item.details) + '</div>';
        }
        html += '</div>';
    });
    html += '</div>';
    panel.innerHTML = html;
}

function switchResTab(active) {
    ['rule','materiel','plan'].forEach(function(t) {
        var tab   = document.getElementById('res-tab-' + t);
        var panel = document.getElementById('res-panel-' + t);
        if (!tab || !panel) return;
        if (t === active) {
            tab.style.background  = '#e9f1fa';
            tab.style.color       = '#548CA8';
            tab.style.borderColor = '#b6d0e2';
            panel.style.display   = 'block';
        } else {
            tab.style.background  = '#fff';
            tab.style.color       = '#64748b';
            tab.style.borderColor = '#e9f1fa';
            panel.style.display   = 'none';
        }
    });
}

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function closeEventModal() {
    var overlay = document.getElementById('eventDetailsOverlay');
    var modal   = document.getElementById('eventDetailsModal');

    modal.style.opacity   = '0';
    modal.style.transform = 'scale(0.92) translateY(20px)';

    setTimeout(function() {
        overlay.style.display = 'none';
        document.body.style.overflow = '';
    }, 280);
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeEventModal();
});
</script>

<style>
    /* Styling the scrollbar for the right pane */
    .neo-auth-form > div[style*="overflow-y: auto"]::-webkit-scrollbar {
        width: 6px;
    }
    .neo-auth-form > div[style*="overflow-y: auto"]::-webkit-scrollbar-track {
        background: transparent;
    }
    .neo-auth-form > div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .neo-auth-form > div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
        .neo-auth-grid {
            grid-template-columns: 1fr !important;
        }
        .neo-auth-info {
            border-right: none !important;
            border-bottom: 1px solid #eef2f6;
            padding: 2.5rem 2rem !important;
        }
        .neo-auth-form {
            padding: 2.5rem 2rem !important;
        }
        .neo-auth-info img {
            max-width: 200px !important;
            margin-top: 2rem !important;
        }
    }
</style>
