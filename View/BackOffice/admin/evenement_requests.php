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
                <div style="display: flex; gap: 10px; margin-bottom: 2rem;">
                    <a href="javascript:history.back()" style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #6c757d; font-weight: 600; text-decoration: none; transition: color 0.2s;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        Back
                    </a>
                </div>
                <a href="<?= APP_ENTRY ?>?url=admin/evenements" style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #548CA8; font-weight: 600; text-decoration: none; margin-bottom: 2rem; transition: color 0.2s;">
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
                            <div style="display: flex; gap: 10px; align-items: stretch;">
                                <form action="<?= APP_ENTRY ?>?url=admin/approve-evenement/<?= (int) $event['id'] ?>" method="POST" style="flex: 0 0 auto;">
                                    <button type="submit" style="background: linear-gradient(135deg, #548CA8 0%, #355C7D 100%); color: #fff; border: none; padding: 0 1.2rem; height: 42px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; box-shadow: 0 4px 10px rgba(84, 140, 168, 0.2);" onmouseover="this.style.boxShadow='0 6px 15px rgba(84, 140, 168, 0.3)'; this.style.transform='translateY(-1px)'" onmouseout="this.style.boxShadow='0 4px 10px rgba(84, 140, 168, 0.2)'; this.style.transform='translateY(0)'">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        Approve
                                    </button>
                                </form>
                                <form action="<?= APP_ENTRY ?>?url=admin/reject-evenement/<?= (int) $event['id'] ?>" method="POST" style="flex: 1; display: flex; flex-direction: column; gap: 8px;" novalidate>
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
                        <a href="<?= APP_ENTRY ?>?url=admin/evenements" style="display: inline-block; margin-top: 1.5rem; background: #E19864; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 0.95rem; box-shadow: 0 4px 10px rgba(225, 152, 100, 0.3);">View All Events</a>
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
