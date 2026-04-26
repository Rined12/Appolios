<?php
/**
 * APPOLIOS - Teacher Participation Requests
 */

$teacherSidebarActive = 'participations';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0; font-family: 'Inter', sans-serif;">

                <!-- Hero Banner -->
                <div style="background: linear-gradient(135deg, #2B4865 0%, #355C7D 50%, #548CA8 100%); border-radius: 20px; margin-bottom: 2rem; color: white; padding: 2.5rem; box-shadow: 0 10px 30px rgba(43,72,101,0.2);">
                    <span style="background:rgba(255,255,255,0.2);backdrop-filter:blur(5px);padding:5px 12px;border-radius:20px;font-size:0.85rem;font-weight:600;text-transform:uppercase;letter-spacing:1px;margin-bottom:15px;display:inline-block;">Event Management</span>
                    <h1 style="font-size:2rem;font-weight:800;margin:0 0 0.5rem 0;">Participation Requests</h1>
                    <p style="margin:0;opacity:0.85;max-width:600px;">Review and manage student participation requests for your events.</p>
                </div>

                <!-- Flash Message -->
                <?php if (!empty($flash)): ?>
                    <div style="padding:1rem 1.5rem;border-radius:12px;margin-bottom:1.5rem;font-weight:600;
                        background:<?= $flash['type'] === 'success' ? '#f0fdf4' : ($flash['type'] === 'error' ? '#fef2f2' : '#fff7ed') ?>;
                        color:<?= $flash['type'] === 'success' ? '#16a34a' : ($flash['type'] === 'error' ? '#dc2626' : '#ea580c') ?>;
                        border:1px solid <?= $flash['type'] === 'success' ? '#bbf7d0' : ($flash['type'] === 'error' ? '#fecaca' : '#fed7aa') ?>;">
                        <?= htmlspecialchars($flash['message']) ?>
                    </div>
                <?php endif; ?>

                <!-- Stats Row -->
                <?php
                $pending  = array_filter($requests ?? [], fn($r) => $r['status'] === 'pending');
                $approved = array_filter($requests ?? [], fn($r) => $r['status'] === 'approved');
                $rejected = array_filter($requests ?? [], fn($r) => $r['status'] === 'rejected');
                ?>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem;">
                    <div style="background:white;border-radius:16px;padding:1.5rem;border:1px solid #eef2f6;text-align:center;box-shadow:0 4px 15px rgba(0,0,0,0.03);">
                        <div style="font-size:2rem;font-weight:800;color:#f97316;"><?= count($pending) ?></div>
                        <div style="font-size:0.85rem;color:#64748b;font-weight:600;margin-top:4px;">Pending</div>
                    </div>
                    <div style="background:white;border-radius:16px;padding:1.5rem;border:1px solid #eef2f6;text-align:center;box-shadow:0 4px 15px rgba(0,0,0,0.03);">
                        <div style="font-size:2rem;font-weight:800;color:#22c55e;"><?= count($approved) ?></div>
                        <div style="font-size:0.85rem;color:#64748b;font-weight:600;margin-top:4px;">Approved</div>
                    </div>
                    <div style="background:white;border-radius:16px;padding:1.5rem;border:1px solid #eef2f6;text-align:center;box-shadow:0 4px 15px rgba(0,0,0,0.03);">
                        <div style="font-size:2rem;font-weight:800;color:#ef4444;"><?= count($rejected) ?></div>
                        <div style="font-size:0.85rem;color:#64748b;font-weight:600;margin-top:4px;">Rejected</div>
                    </div>
                </div>

                <!-- Requests List (Card Design) -->
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php if (!empty($requests)): ?>
                        <?php foreach ($requests as $req): ?>
                            <?php
                            $s = $req['status'] ?? 'pending';
                            $statusColor = $s === 'approved' ? '#22c55e' : ($s === 'rejected' ? '#ef4444' : '#f97316');
                            $statusBg    = $s === 'approved' ? '#f0fdf4' : ($s === 'rejected' ? '#fef2f2' : '#fff7ed');
                            $studentName = $req['student_name_full'] ?? $req['student_name'] ?? 'Student';
                            $firstLetter = strtoupper(substr($studentName, 0, 1));
                            ?>
                            <div style="background: white; border-radius: 20px; padding: 1.5rem 2rem; border: 1px solid #eef2f6; box-shadow: 0 4px 15px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; transition: all 0.2s;" onmouseover="this.style.borderColor='#548CA8'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#eef2f6'; this.style.transform='translateY(0)'">
                                
                                <!-- Student Info -->
                                <div style="display: flex; align-items: center; gap: 1.2rem; flex: 1;">
                                    <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #2B4865; font-size: 1.2rem;">
                                        <?= $firstLetter ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 800; color: #1e293b; font-size: 1.1rem;"><?= htmlspecialchars($studentName) ?></div>
                                        <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 500;"><?= htmlspecialchars($req['student_email'] ?? '') ?></div>
                                        <div style="font-size: 0.8rem; color: #548CA8; font-weight: 700; margin-top: 4px; display: flex; align-items: center; gap: 5px;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                            <?= htmlspecialchars($req['event_title'] ?? '') ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <div style="flex: 0 0 auto;">
                                    <span style="background:<?= $statusBg ?>; color:<?= $statusColor ?>; padding: 6px 16px; border-radius: 50px; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">
                                        <?= htmlspecialchars($s) ?>
                                    </span>
                                </div>

                                <!-- Actions -->
                                <div style="display: flex; align-items: center; gap: 12px; flex: 0 0 auto;">
                                    <?php if ($s === 'pending'): ?>
                                        <!-- Approve Form -->
                                        <form method="POST" action="<?= APP_ENTRY ?>?url=teacher/approve-participation/<?= (int)$req['id'] ?>" style="margin:0;">
                                            <button type="submit" style="background:#2B4865; color:white; border:none; padding:10px 20px; border-radius:12px; font-weight:700; font-size:0.85rem; cursor:pointer; transition:all 0.2s; display:flex; align-items:center; gap:8px;" onmouseover="this.style.background='#355C7D'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#2B4865'; this.style.transform='translateY(0)'">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                Approve
                                            </button>
                                        </form>

                                        <!-- Reject Form with Always-Visible Reason -->
                                        <form method="POST" action="<?= APP_ENTRY ?>?url=teacher/reject-participation/<?= (int)$req['id'] ?>" style="margin:0; display:flex; align-items:center; gap:8px;">
                                            <input type="text" name="reason" placeholder="Reason..." style="background: #fff; border: 1.5px solid #fecaca; border-radius: 12px; padding: 10px 15px; font-size: 0.9rem; width: 180px; outline: none; transition: all 0.2s; color: #64748b;" onfocus="this.style.borderColor='#ef4444'; this.style.boxShadow='0 0 0 3px rgba(239, 68, 68, 0.1)'" onblur="this.style.borderColor='#fecaca'; this.style.boxShadow='none'">
                                            <button type="submit" style="background:#fef2f2; color:#ef4444; border:1.5px solid #fecaca; padding:10px 20px; border-radius:12px; font-weight:700; font-size:0.85rem; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.background='#fee2e2'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#fef2f2'; this.style.transform='translateY(0)'" onclick="return this.form.reason.value.trim() !== '' || (alert('Please provide a reason') && false)">
                                                Reject
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div style="color: #94a3b8; font-size: 0.85rem; font-style: italic; border: 1.5px dashed #e2e8f0; padding: 6px 15px; border-radius: 10px;">
                                            Processed on <?= !empty($req['updated_at']) ? date('d M', strtotime($req['updated_at'])) : 'Recently' ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <div style="text-align:center;padding:5rem 2rem;background:white;border-radius:20px;border:1.5px dashed #eef2f6;">
                            <div style="font-size:4rem;margin-bottom:1rem;opacity:0.2;">👥</div>
                            <h3 style="color:#2B4865;margin:0 0 10px 0;font-weight:800;">No participation requests</h3>
                            <p style="color:#64748b;margin:0;">When students join your events, they will appear here.</p>
                        </div>
                    <?php endif; ?>
                </div>
  </div>

            </div>
        </div>
    </div>
</div>
