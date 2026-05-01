<?php
/**
 * APPOLIOS - Teacher Evenement Resources (premium neo theme)
 */

$ressource_old = $ressource_old ?? [];
$old = $ressource_old;
$participation_rollup = $participation_rollup ?? ['total' => 0, 'pending_count' => 0, 'approved_count' => 0, 'rejected_count' => 0];
$participation_modal_open = $participation_modal_open ?? false;
$selectedEvenementId = (int) ($selectedEvenementId ?? 0);
$selectedEvenementTitle = $selectedEvenement['title'] ?? $selectedEvenement['titre'] ?? '';
$editResource = $editResource ?? null;

$teacherSidebarActive = 'evenements';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0;">
                
                <section class="neo-auth-wrap" style="background: transparent; font-family: 'Inter', sans-serif;">
                    
                    <div class="neo-glass-card" style="width: 100%; background: #ffffff; border-radius: 20px; box-shadow: 0 15px 40px rgba(43, 72, 101, 0.08); border: 1px solid rgba(233, 241, 250, 0.8); overflow: hidden; display: flex; flex-direction: column;">
                        
                        <!-- Header Area -->
                        <div style="padding: 3rem 3.5rem; background: #fcfcfc; position: relative; overflow: hidden; border-bottom: 1px solid #eef2f6;">
                            <!-- Decorative blobs -->
                            <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: #e9f1fa; border-radius: 50%; z-index: 0; opacity: 0.7;"></div>
                            <div style="position: absolute; bottom: -50px; right: 10%; width: 300px; height: 300px; background: #fff7ed; border-radius: 50%; z-index: 0; opacity: 0.4;"></div>

                            <div style="position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: flex-start; gap: 2rem; flex-wrap: wrap;">

                                <!-- LEFT: Back / Title / Badge -->
                                <div>
                                    <a href="<?= APP_ENTRY ?>?url=teacher/evenements" style="display: inline-flex; align-items: center; gap: 8px; font-size: 1rem; color: #E19864; font-weight: 700; text-decoration: none; margin-bottom: 1.5rem; transition: color 0.2s;" onmouseover="this.style.color='#c88251'" onmouseout="this.style.color='#E19864'">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                                        Back to My Evenements
                                    </a>

                                    <h2 style="font-size: 2.8rem; font-weight: 800; color: #2B4865; line-height: 1.15; margin: 0 0 0.4rem 0; letter-spacing: -0.02em;">
                                        Ressources <span style="color: #E19864;">Evenement</span>
                                    </h2>
                                    <p style="color: #64748b; font-size: 1rem; line-height: 1.6; margin: 0 0 1.2rem 0;">
                                        Manage rules, materiel, and day plans for your evenement proposal.
                                    </p>
                                    <div style="display: inline-flex; background: #fff; border: 1px solid #eef2f6; padding: 7px 16px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); align-items: center; gap: 6px;">
                                        <span style="color: #94a3b8; font-size: 0.9rem;">Evenement:</span>
                                        <strong style="color: #2B4865; font-size: 1rem;"><?= htmlspecialchars($selectedEvenementTitle) ?></strong>
                                    </div>
                                </div>

                                <!-- RIGHT: Participation button -->
                                <?php $participations = $participations ?? []; $pendingCount = (int) ($participation_rollup['pending_count'] ?? 0); ?>
                                <div style="display: flex; align-items: flex-start; padding-top: 3.5rem;">
                                    <button type="button" onclick="document.getElementById('partModal').classList.add('active')"
                                        style="display:inline-flex;align-items:center;gap:10px;background:linear-gradient(135deg,#2B4865 0%,#548CA8 100%);color:#fff;border:none;padding:13px 22px;border-radius:12px;font-weight:700;font-size:0.95rem;cursor:pointer;box-shadow:0 6px 20px rgba(43,72,101,0.25);transition:all 0.25s;white-space:nowrap;"
                                        onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 10px 28px rgba(43,72,101,0.35)'"
                                        onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 6px 20px rgba(43,72,101,0.25)'">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                        Liste des Participations
                                        <?php if ($pendingCount > 0): ?>
                                            <span style="background:#f97316;color:#fff;padding:3px 10px;border-radius:50px;font-size:0.72rem;font-weight:800;letter-spacing:0.03em;"><?= $pendingCount ?> pending</span>
                                        <?php else: ?>
                                            <span style="background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.3);padding:3px 10px;border-radius:50px;font-size:0.72rem;"><?= count($participations) ?></span>
                                        <?php endif; ?>
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- PARTICIPATION MODAL -->
                    <div id="partModal" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(15,23,42,0.45);backdrop-filter:blur(7px);-webkit-backdrop-filter:blur(7px);display:flex;align-items:center;justify-content:center;z-index:9999;opacity:0;visibility:hidden;transition:all 0.3s ease;">
                        <div style="background:#fff;border-radius:22px;width:92%;max-width:720px;max-height:82vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,0.22);transform:translateY(24px) scale(0.96);transition:all 0.3s cubic-bezier(0.34,1.56,0.64,1);border:1px solid #f1f5f9;">
                            <div style="padding:1.8rem 2rem 1.4rem;border-bottom:1px solid #eef2f6;display:flex;justify-content:space-between;align-items:center;">
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <div style="background:#e9f1fa;width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#2B4865;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path></svg>
                                    </div>
                                    <div>
                                        <h2 style="margin:0;font-size:1.2rem;font-weight:800;color:#0f172a;">Participation Requests</h2>
                                        <p style="margin:0;font-size:0.82rem;color:#64748b;"><?= (int) ($participation_rollup['total'] ?? 0) ?> total &mdash; <?= $pendingCount ?> pending</p>
                                    </div>
                                </div>
                                <button onclick="document.getElementById('partModal').classList.remove('active')" style="background:#f8fafc;border:none;width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#64748b;cursor:pointer;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </button>
                            </div>
                            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.8rem;padding:1rem 2rem;border-bottom:1px solid #eef2f6;">
                                <div style="text-align:center;padding:0.7rem;background:#fff7ed;border-radius:10px;"><div style="font-size:1.4rem;font-weight:800;color:#f97316;"><?= $pendingCount ?></div><div style="font-size:0.75rem;color:#64748b;font-weight:600;">Pending</div></div>
                                <div style="text-align:center;padding:0.7rem;background:#f0fdf4;border-radius:10px;"><div style="font-size:1.4rem;font-weight:800;color:#22c55e;"><?= (int) ($participation_rollup['approved_count'] ?? 0) ?></div><div style="font-size:0.75rem;color:#64748b;font-weight:600;">Approved</div></div>
                                <div style="text-align:center;padding:0.7rem;background:#fef2f2;border-radius:10px;"><div style="font-size:1.4rem;font-weight:800;color:#ef4444;"><?= (int) ($participation_rollup['rejected_count'] ?? 0) ?></div><div style="font-size:0.75rem;color:#64748b;font-weight:600;">Rejected</div></div>
                            </div>
                            <div style="overflow-y:auto;flex:1;padding:1.2rem 2rem 1.8rem;">
                                <?php if (!empty($participations)): ?>
                                    <?php foreach ($participations as $p):
                                        $s = (string) ($p['display_status'] ?? 'pending');
                                        $sc = (string) ($p['display_status_color'] ?? '#f97316');
                                        $sb = (string) ($p['display_status_bg'] ?? '#fff7ed');
                                    ?>
                                    <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:0.9rem 1rem;border-radius:12px;border:1px solid #eef2f6;margin-bottom:0.7rem;background:#fafbfc;transition:background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#fafbfc'">
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <div style="background:#e9f1fa;width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#548CA8;font-weight:800;flex-shrink:0;"><?= htmlspecialchars((string) ($p['display_student_initial'] ?? 'S')) ?></div>
                                            <div>
                                                <div style="font-weight:700;color:#1e293b;"><?= htmlspecialchars((string) ($p['display_student_name'] ?? '')) ?></div>
                                                <div style="font-size:0.78rem;color:#94a3b8;"><?= htmlspecialchars($p['student_email'] ?? '') ?></div>
                                            </div>
                                        </div>
                                        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                                            <span style="background:<?= htmlspecialchars($sb) ?>;color:<?= htmlspecialchars($sc) ?>;padding:3px 10px;border-radius:50px;font-size:0.72rem;font-weight:700;text-transform:uppercase;"><?= htmlspecialchars($s) ?></span>
                                            <?php if ($s === 'pending'): ?>
                                                <form method="POST" action="<?= APP_ENTRY ?>?url=teacher/approve-participation/<?= (int)$p['id'] ?>" style="margin:0;">
                                                    <input type="hidden" name="from_evenement_id" value="<?= (int)$selectedEvenementId ?>">
                                                    <button type="submit" style="background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0;padding:4px 12px;border-radius:7px;font-weight:700;font-size:0.8rem;cursor:pointer;" onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">&#10003; Approve</button>
                                                </form>
                                                <form method="POST" action="<?= APP_ENTRY ?>?url=teacher/reject-participation/<?= (int)$p['id'] ?>" style="margin:0;">
                                                    <input type="hidden" name="from_evenement_id" value="<?= (int)$selectedEvenementId ?>">
                                                    <button type="submit" style="background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca;padding:4px 12px;border-radius:7px;font-weight:700;font-size:0.8rem;cursor:pointer;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'" onclick="return confirm('Reject this student?')">&#10005; Reject</button>
                                                </form>
                                            <?php endif; ?>

                                            <!-- Delete (always visible to event owner) -->
                                            <form method="POST" action="<?= APP_ENTRY ?>?url=teacher/delete-participation/<?= (int)$p['id'] ?>" style="margin:0;">
                                                <input type="hidden" name="from_evenement_id" value="<?= (int)$selectedEvenementId ?>">
                                                <button type="submit"
                                                    onclick="return confirm('Delete this participation record permanently?')"
                                                    title="Delete Participation"
                                                    style="background:#fff1f2;color:#e11d48;border:1.5px solid #fecdd3;width:32px;height:32px;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
                                                    onmouseover="this.style.background='#ffe4e6';this.style.borderColor='#fb7185'"
                                                    onmouseout="this.style.background='#fff1f2';this.style.borderColor='#fecdd3'">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                                        <div style="font-size:2rem;margin-bottom:0.6rem;opacity:0.4;">&#128101;</div>
                                        <p style="margin:0;font-size:0.9rem;">No participation requests yet for this event.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <script>
                    (function(){
                        var m = document.getElementById('partModal');
                        if (!m) return;
                        m.addEventListener('click', function(e){ if(e.target===m) m.classList.remove('active'); });
                        var card = m.querySelector('div');
                        var mo = new MutationObserver(function(){
                            if(m.classList.contains('active')){ m.style.opacity='1';m.style.visibility='visible';card.style.transform='translateY(0) scale(1)'; }
                            else { m.style.opacity='0';m.style.visibility='hidden';card.style.transform='translateY(24px) scale(0.96)'; }
                        });
                        mo.observe(m,{attributes:true,attributeFilter:['class']});
                        <?php if (!empty($participation_modal_open)): ?>m.classList.add('active');<?php endif; ?>
                    })();
                    </script>

                        <!-- Content Area: Forms and Lists -->
                        <div class="ressource-content-grid" style="padding: 2rem; background: #ffffff; display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem; align-items: start;">
                            
                            <!-- LEFT: Forms -->
                            <div class="ressource-forms-col" style="display: flex; flex-direction: column; gap: 1.5rem;">
                                
                                <!-- Form Card Template -->
                                <?php
                                $formCards = [
                                    ['type' => 'rule', 'label' => 'Rule', 'titleLabel' => 'Rule Title *', 'titlePlaceholder' => 'Example: Respect start time', 'detailsLabel' => 'Rule Details', 'detailsPlaceholder' => 'Explain this rule clearly'],
                                    ['type' => 'materiel', 'label' => 'Materiel', 'titleLabel' => 'Materiel Name *', 'titlePlaceholder' => 'Example: Projector', 'detailsLabel' => 'Materiel Details', 'detailsPlaceholder' => 'Quantity, room, notes...'],
                                    ['type' => 'plan', 'label' => 'Plan (Journée)', 'titleLabel' => 'Plan Item *', 'titlePlaceholder' => 'Example: 09:00 - Opening', 'detailsLabel' => 'Plan Details', 'detailsPlaceholder' => 'Speaker, duration, objectives...']
                                ];
                                ?>
                                <?php foreach ($formCards as $fc): ?>
                                <div class="ressource-form-card" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.02);" onmouseover="this.style.transform='translateY(-4px)'; this.style.borderColor='#cbd5e1'; this.style.boxShadow='0 12px 20px rgba(0,0,0,0.06)'" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='#e2e8f0'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.02)'">
                                    <h3 style="margin: 0 0 1.2rem 0; color: #1e293b; font-size: 1.15rem; font-weight: 700;">Ajouter <?= htmlspecialchars($fc['label']) ?></h3>
                                    <form action="<?= APP_ENTRY ?>?url=teacher/store-evenement-ressource" method="POST" class="resource-create-form" data-type-label="<?= htmlspecialchars($fc['label']) ?>">
                                        <input type="hidden" name="type" value="<?= htmlspecialchars($fc['type']) ?>">
                                        <input type="hidden" name="evenement_id" value="<?= (int) $selectedEvenementId ?>">
                                        
                                        <div style="margin-bottom: 1rem;">
                                            <label style="display: block; color: #475569; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem;"><?= htmlspecialchars($fc['titleLabel']) ?></label>
                                            <input type="text" name="title" placeholder="<?= htmlspecialchars($fc['titlePlaceholder']) ?>" value="<?= htmlspecialchars(($old['type'] ?? '') === $fc['type'] ? ($old['title'] ?? '') : '') ?>" data-js-required="1" style="width: 100%; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 0.9rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#E19864'" onblur="this.style.borderColor='#cbd5e1'">
                                        </div>
                                        
                                        <div style="margin-bottom: 1.2rem;">
                                            <label style="display: block; color: #475569; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem;"><?= htmlspecialchars($fc['detailsLabel']) ?></label>
                                            <textarea name="details" placeholder="<?= htmlspecialchars($fc['detailsPlaceholder']) ?>" style="width: 100%; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 0.9rem; min-height: 70px; resize: vertical; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#E19864'" onblur="this.style.borderColor='#cbd5e1'"><?= htmlspecialchars(($old['type'] ?? '') === $fc['type'] ? ($old['details'] ?? '') : '') ?></textarea>
                                        </div>
                                        
                                        <button type="submit" style="background: linear-gradient(135deg, #548CA8 0%, #355C7D 100%); color: #fff; border: none; width: 100%; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 10px rgba(84, 140, 168, 0.2);" onmouseover="this.style.boxShadow='0 6px 15px rgba(84, 140, 168, 0.3)'; this.style.transform='translateY(-1px)'" onmouseout="this.style.boxShadow='0 4px 10px rgba(84, 140, 168, 0.2)'; this.style.transform='translateY(0)'">
                                            Ajouter
                                        </button>
                                    </form>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- RIGHT: Lists -->
                            <div class="ressource-lists-col" style="display: flex; flex-direction: column; gap: 1.5rem;">
                                <?php
                                $groups = [
                                    'Rules' => ['key' => 'rules', 'type' => 'rule', 'color' => '#3b82f6', 'bg' => '#eff6ff'],
                                    'Materiels' => ['key' => 'materials', 'type' => 'materiel', 'color' => '#eab308', 'bg' => '#fefce8'],
                                    'Plans' => ['key' => 'plans', 'type' => 'plan', 'color' => '#22c55e', 'bg' => '#f0fdf4']
                                ];
                                ?>
                                <?php foreach ($groups as $groupTitle => $groupConfig): ?>
                                    <div class="ressource-list-card" style="background: #ffffff; border: 1px solid #eef2f6; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.03); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 25px rgba(0,0,0,0.06)'; this.style.borderColor='#d0e3f5'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.03)'; this.style.borderColor='#eef2f6'">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.8rem;">
                                            <h3 style="margin: 0; color: #2B4865; font-size: 1.25rem; font-weight: 800;">Liste <?= htmlspecialchars($groupTitle) ?></h3>
                                            <?php $items = ${$groupConfig['key']} ?? []; ?>
                                            <span style="background: <?= $groupConfig['bg'] ?>; color: <?= $groupConfig['color'] ?>; padding: 4px 10px; border-radius: 50px; font-size: 0.8rem; font-weight: 700;">
                                                <?= count($items) ?> Items
                                            </span>
                                        </div>
                                        
                                        <ul class="ressource-list" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.8rem;">
                                            <?php if (!empty($items)): ?>
                                                <?php foreach ($items as $item): ?>
                                                    <li style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem; transition: all 0.2s;" onmouseover="this.style.borderColor='#cbd5e1'; this.style.background='#f1f5f9'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc'">
                                                        <?php if ($editResource && (int) $editResource['id'] === (int) $item['id']): ?>
                                                            <form action="<?= APP_ENTRY ?>?url=teacher/update-evenement-ressource/<?= (int) $item['id'] ?>" method="POST">
                                                                <input type="hidden" name="evenement_id" value="<?= (int) $selectedEvenementId ?>">
                                                                <div style="margin-bottom: 10px;">
                                                                    <input type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>" data-js-required="1" style="width: 100%; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 0.95rem; font-weight: 600; outline: none;">
                                                                </div>
                                                                <div style="margin-bottom: 12px;">
                                                                    <textarea name="details" placeholder="Details..." style="width: 100%; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 0.9rem; min-height: 60px; resize: vertical; outline: none;"><?= htmlspecialchars($item['details'] ?? '') ?></textarea>
                                                                </div>
                                                                <div style="display: flex; gap: 10px;">
                                                                    <button type="submit" style="background: #548CA8; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer;">Sauvegarder</button>
                                                                    <a href="<?= APP_ENTRY ?>?url=teacher/evenement-ressources&evenement_id=<?= (int) $selectedEvenementId ?>" style="background: #e2e8f0; color: #475569; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-weight: 600;">Annuler</a>
                                                                </div>
                                                            </form>
                                                        <?php else: ?>
                                                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                                                <div>
                                                                    <strong style="color: #1e293b; font-size: 1.1rem; display: block; margin-bottom: 4px;"><?= htmlspecialchars($item['title']) ?></strong>
                                                                    <?php if (($groupConfig['type'] ?? '') === 'materiel'): ?>
                                                                        <?php if (($item['materiel_qty_badge'] ?? '') !== ''): ?>
                                                                            <span style="display: inline-block; background: #fef9c3; color: #854d0e; font-size: 0.75rem; font-weight: 800; padding: 3px 10px; border-radius: 50px; margin-bottom: 6px;">Qty <?= htmlspecialchars((string) $item['materiel_qty_badge']) ?></span>
                                                                        <?php endif; ?>
                                                                        <?php
                                                                        $mDetail = trim((string) ($item['materiel_details_plain'] ?? ''));
                                                                        ?>
                                                                        <?php if ($mDetail !== ''): ?>
                                                                            <p style="color: #64748b; font-size: 0.95rem; margin: 0; line-height: 1.5;"><?= nl2br(htmlspecialchars($mDetail)) ?></p>
                                                                        <?php endif; ?>
                                                                    <?php elseif (!empty($item['details'])): ?>
                                                                        <p style="color: #64748b; font-size: 0.95rem; margin: 0; line-height: 1.5;"><?= nl2br(htmlspecialchars($item['details'])) ?></p>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div style="display: flex; gap: 8px; flex-shrink: 0;">
                                                                    <a href="<?= APP_ENTRY ?>?url=teacher/evenement-ressources&evenement_id=<?= (int) $selectedEvenementId ?>&edit_id=<?= (int) $item['id'] ?>" style="background: #fff; border: 1.5px solid #e2e8f0; color: #64748b; text-decoration: none; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.borderColor='#548CA8'; this.style.color='#548CA8'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#64748b'" title="Edit">
                                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                                    </a>
                                                                    <form action="<?= APP_ENTRY ?>?url=teacher/delete-evenement-ressource/<?= (int) $item['id'] ?>" method="POST" style="margin: 0;">
                                                                        <input type="hidden" name="evenement_id" value="<?= (int) $selectedEvenementId ?>">
                                                                        <button type="submit" onclick="return confirm('Delete this resource item?')" style="background: #fff; border: 1.5px solid #e2e8f0; color: #ef4444; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#fecaca'; this.style.background='#fef2f2'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='#fff'" title="Delete">
                                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <li class="empty" style="text-align: center; padding: 1.5rem; color: #94a3b8; border: 2px dashed #e2e8f0; border-radius: 10px;">
                                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.4rem;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                                    <p style="margin: 0; font-size: 0.95rem;">No <?= strtolower($groupTitle) ?> added yet.</p>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                <?php endforeach; ?>
                                
                                <div style="margin-top: 1rem; display: flex; justify-content: flex-end;">
                                    <button type="button" id="saveAllTeacherResourcesBtn" style="background: linear-gradient(135deg, #E19864 0%, #d9804b 100%); color: #fff; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 15px rgba(225, 152, 100, 0.3); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(225, 152, 100, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(225, 152, 100, 0.3)'">
                                        Sauvegarder Tout
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>
                
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 992px) {
        .ressource-content-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<script>
(function () {
    const saveBtn = document.getElementById('saveAllTeacherResourcesBtn');
    if (!saveBtn) {
        return;
    }

    saveBtn.addEventListener('click', async function () {
        const forms = Array.from(document.querySelectorAll('.resource-create-form'));
        const validForms = forms.filter(function (form) {
            const titleInput = form.querySelector('input[name="title"]');
            return titleInput && titleInput.value.trim() !== '';
        });
        const existingRightListItems = document.querySelectorAll('.ressource-list li:not(.empty)');

        if (validForms.length === 0) {
            if (existingRightListItems.length > 0) {
                alert('Resources are already present in the right list and considered saved.');
                window.location.href = '<?= APP_ENTRY ?>?url=teacher/evenements';
                return;
            }

            alert('No resources found. Add at least one resource first.');
            return;
        }

        saveBtn.disabled = true;
        saveBtn.textContent = 'Sauvegarde en cours...';

        let savedCount = 0;
        const errors = [];

        for (const form of validForms) {
            const data = new FormData(form);
            data.append('batch_mode', '1');

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: data,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();
                if (result.success && result.verified_in_right_list === true) {
                    savedCount += 1;
                } else {
                    errors.push(result.message || ('Verification failed for ' + (form.dataset.typeLabel || 'resource')));
                }
            } catch (err) {
                errors.push('Network error while saving resources.');
            }
        }

        if (errors.length > 0) {
            alert('Saved: ' + savedCount + '\nErrors:\n- ' + errors.join('\n- '));
        } else {
            alert('All resources saved successfully (' + savedCount + ').');
        }

        window.location.href = '<?= APP_ENTRY ?>?url=teacher/evenements';
    });
})();
</script>
