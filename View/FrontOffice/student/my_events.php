<?php
/**
 * APPOLIOS - My Events Student Page
 */
?>

<div style="min-height: 100vh; background: #f8fafc; padding: 30px; font-family: 'Inter', sans-serif;">
    
    <!-- Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px;">
        <div>
            <h1 style="margin: 0; font-size: 2rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em;">My Events</h1>
            <p style="margin: 8px 0 0; color: #64748b; font-size: 1.05rem; font-weight: 500;">Track and manage your event participations.</p>
        </div>
        <div style="background: white; padding: 12px 24px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px;">
            <div style="width: 40px; height: 40px; background: #eef2ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #4f46e5;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
            </div>
            <div>
                <span style="display: block; color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Registered</span>
                <span style="color: #0f172a; font-weight: 800; font-size: 1.1rem;"><?= count($participations) ?> Events</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <?php if (empty($participations)): ?>
        <div style="background: white; border-radius: 24px; padding: 80px 40px; text-align: center; border: 1px dashed #cbd5e1;">
            <div style="width: 80px; height: 80px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; color: #94a3b8;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
            <h3 style="margin: 0; color: #1e293b; font-size: 1.4rem; font-weight: 700;">No events yet</h3>
            <p style="margin: 12px auto 24px; color: #64748b; max-width: 400px; line-height: 1.6;">You haven't registered for any events. Browse the upcoming events and join our community!</p>
            <a href="<?= APP_ENTRY ?>?url=student/evenements" style="display: inline-flex; align-items: center; gap: 8px; background: #2B4865; color: white; padding: 14px 28px; border-radius: 14px; text-decoration: none; font-weight: 700; transition: all 0.2s;">
                Browse Events
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 24px;">
            <?php foreach ($participations as $p): 
                $status = strtolower($p['status'] ?? 'pending');
                $statusBg = '#fff7ed'; $statusColor = '#ea580c';
                if ($status === 'approved') { $statusBg = '#f0fdf4'; $statusColor = '#16a34a'; }
                elseif ($status === 'rejected') { $statusBg = '#fef2f2'; $statusColor = '#dc2626'; }
            ?>
                <div style="background: white; border-radius: 24px; border: 1px solid #e2e8f0; overflow: hidden; display: flex; flex-direction: column; transition: transform 0.2s, box-shadow 0.2s; position: relative;" 
                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 20px 25px -5px rgba(0,0,0,0.05)';" 
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                    
                    <!-- Card Top -->
                    <div style="padding: 24px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                            <span style="background: <?= $statusBg ?>; color: <?= $statusColor ?>; padding: 6px 14px; border-radius: 100px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">
                                <?= $status ?>
                            </span>
                            <span style="color: #94a3b8; font-size: 0.8rem; font-weight: 600;">Registered: <?= date('M d, Y', strtotime($p['created_at'])) ?></span>
                        </div>
                        
                        <h3 style="margin: 0 0 10px; font-size: 1.35rem; font-weight: 800; color: #0f172a; line-height: 1.3;"><?= htmlspecialchars($p['event_title'] ?? 'Event Title') ?></h3>
                        
                        <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 15px;">
                            <div style="display: flex; align-items: center; gap: 10px; color: #64748b; font-size: 0.95rem;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <?= date('F d, Y \a\t H:i', strtotime(($p['date_debut'] ?? '') . ' ' . ($p['heure_debut'] ?? ''))) ?>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px; color: #64748b; font-size: 0.95rem;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <?= htmlspecialchars($p['lieu'] ?? 'TBA') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Card Actions -->
                    <div style="padding: 20px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; gap: 12px; margin-top: auto;">
                        <?php if ($status === 'approved'): ?>
                            <a href="<?= APP_ENTRY ?>?url=student/download-ticket/<?= $p['evenement_id'] ?>" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; background: #2B4865; color: white; padding: 12px; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 0.9rem; transition: background 0.2s;" onmouseover="this.style.background='#1e344a'" onmouseout="this.style.background='#2B4865'">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                Ticket
                            </a>
                        <?php endif; ?>
                        
                        <form action="<?= APP_ENTRY ?>?url=student/cancel-participation/<?= $p['evenement_id'] ?>" method="POST" style="flex: 1; margin: 0;" onsubmit="return confirm('Are you sure you want to cancel your participation?');">
                            <button type="submit" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background: white; border: 1.5px solid #e2e8f0; color: #ef4444; padding: 11px; border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 0.9rem; transition: all 0.2s;" onmouseover="this.style.background='#fef2f2'; this.style.borderColor='#fecaca'" onmouseout="this.style.background='white'; this.style.borderColor='#e2e8f0'">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                Cancel
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
