<?php
/**
 * APPOLIOS - Admin Evenement Resources (premium neo theme)
 */

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
$selectedEvenementId = (int) ($selectedEvenementId ?? 0);
$selectedEvenementTitle = $selectedEvenement['title'] ?? $selectedEvenement['titre'] ?? '';
$editResource = $editResource ?? null;

?>

<section class="neo-auth-wrap" style="background: transparent; font-family: 'Inter', sans-serif;">
                    
                    <div class="neo-glass-card" style="width: 100%; background: #0f172a; border-radius: 20px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden; display: flex; flex-direction: column; color: #ffffff;">
                        
                        <!-- Header Area -->
                        <div style="padding: 3.5rem; background: rgba(30, 41, 59, 0.3); position: relative; overflow: hidden; border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                            <!-- Decorative blobs -->
                            <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: #e9f1fa; border-radius: 50%; z-index: 0; opacity: 0.7;"></div>
                            <div style="position: absolute; bottom: -50px; right: 10%; width: 300px; height: 300px; background: #fef2f2; border-radius: 50%; z-index: 0; opacity: 0.4;"></div>
                            
                            <div style="position: relative; z-index: 2;">
                                <!-- Back Link matching the screenshot -->
                                <a href="<?= APP_ENTRY ?>?url=event/evenements" style="display: inline-flex; align-items: center; gap: 8px; font-size: 1.15rem; color: #cbd5e1; font-weight: 700; text-decoration: none; margin-bottom: 2rem; transition: color 0.2s;" onmouseover="this.style.color='#E19864'" onmouseout="this.style.color='#cbd5e1'">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                                    Back to Evenements
                                </a>

                                <h2 style="font-size: 2.8rem; font-weight: 800; color: #ffffff; line-height: 1.15; margin: 0 0 0.5rem 0; letter-spacing: -0.02em;">
                                    Ressources <span style="color: #E19864;">Evenement</span>
                                </h2>
                                <p style="color: #94a3b8; font-size: 1.1rem; line-height: 1.6; margin: 0 0 1.5rem 0; max-width: 90%;">
                                    Manage rules, materiel, and day plans for this evenement. Fill in the forms below to add new resources to the list.
                                </p>
                                <div style="display: inline-flex; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); padding: 8px 16px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                    <span style="color: #94a3b8; margin-right: 8px;">Evenement:</span>
                                    <strong style="color: #E19864; font-size: 1.05rem;"><?= htmlspecialchars($selectedEvenementTitle) ?></strong>
                                </div>



                        <!-- Content Area: Forms and Lists -->
                        <div class="ressource-content-grid" style="padding: 2rem; background: transparent; display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem; align-items: start;">
                            
                            <!-- LEFT: Forms -->
                            <div class="ressource-forms-col" style="display: flex; flex-direction: column; gap: 1.5rem;">
                                
                                <!-- AI Generation Box -->
                                <div style="background: rgba(30, 41, 59, 0.5); padding: 1.5rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: space-between; box-shadow: 0 10px 25px rgba(0,0,0,0.1); position: relative; overflow: hidden; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: default;" onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 20px 40px rgba(0,0,0,0.2)'; this.style.borderColor='rgba(225, 152, 100, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)'; this.style.borderColor='rgba(255,255,255,0.05)'">
                                    <!-- Ambient glow -->
                                    <div style="position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(84, 140, 168, 0.15) 0%, transparent 70%); z-index: 0; pointer-events: none;"></div>
                                    
                                    <div style="position: relative; z-index: 1;">
                                        <h4 style="margin: 0 0 0.4rem 0; color: #ffffff; font-size: 1.15rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E19864" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="filter: drop-shadow(0 0 5px rgba(225, 152, 100, 0.3)); flex-shrink: 0;"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                                            Génération par IA
                                        </h4>
                                        <p style="margin: 0; font-size: 0.9rem; color: #94a3b8;">Pré-remplissez automatiquement les ressources grâce à l'intelligence artificielle.</p>
                                    </div>
                                    <button type="button" id="generateAiBtn" style="background: #E19864; color: white; border: none; padding: 11px 24px; border-radius: 12px; font-weight: 700; font-size: 0.95rem; cursor: pointer; transition: all 0.3s ease; white-space: nowrap; box-shadow: 0 4px 15px rgba(225, 152, 100, 0.2); display: flex; align-items: center; gap: 10px; position: relative; z-index: 1;" onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 6px 20px rgba(225, 152, 100, 0.3)'" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(225, 152, 100, 0.2)'">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        Générer avec IA
                                    </button>
                                </div>

                                <!-- Form Card Template -->
                                <?php
                                $formCards = [
                                    ['type' => 'rule',     'label' => 'Rule',     'titleLabel' => 'Rule Title *',     'titlePlaceholder' => 'Example: Respect start time', 'detailsLabel' => 'Rule Details',     'detailsPlaceholder' => 'Explain this rule clearly',        'quantite' => false],
                                    ['type' => 'materiel', 'label' => 'Materiel', 'titleLabel' => 'Materiel Name *',  'titlePlaceholder' => 'Example: Projector',          'detailsLabel' => 'Materiel Details', 'detailsPlaceholder' => 'Room, notes, specifications...', 'quantite' => true],
                                    ['type' => 'plan',     'label' => 'Plan',     'titleLabel' => 'Plan Item *',      'titlePlaceholder' => 'Example: 09:00 - Opening',   'detailsLabel' => 'Plan Details',     'detailsPlaceholder' => 'Speaker, duration, objectives...', 'quantite' => false],
                                ];
                                ?>
                                <?php foreach ($formCards as $fc): ?>
                                <div class="ressource-form-card" style="background: rgba(30, 41, 59, 0.5); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 12px; padding: 1.5rem; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" onmouseover="this.style.transform='translateY(-4px)'; this.style.borderColor='rgba(255, 255, 255, 0.1)'; this.style.boxShadow='0 12px 25px rgba(0,0,0,0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255, 255, 255, 0.05)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.1)'">
                                    <h3 style="margin: 0 0 1.2rem 0; color: #ffffff; font-size: 1.15rem; font-weight: 700;">Ajouter <?= htmlspecialchars($fc['label']) ?></h3>
                                    <form action="<?= APP_ENTRY ?>?url=ressource/store-evenement-ressource" method="POST" class="resource-create-form" data-type-label="<?= htmlspecialchars($fc['label']) ?>">
                                        <input type="hidden" name="type" value="<?= htmlspecialchars($fc['type']) ?>">
                                        <input type="hidden" name="evenement_id" value="<?= (int) $selectedEvenementId ?>">
                                        
                                        <div style="margin-bottom: 1rem;">
                                            <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem;"><?= htmlspecialchars($fc['titleLabel']) ?></label>
                                            <input type="text" name="title" placeholder="<?= htmlspecialchars($fc['titlePlaceholder']) ?>" value="<?= htmlspecialchars(($old['type'] ?? '') === $fc['type'] ? ($old['title'] ?? '') : '') ?>" required style="width: 100%; background: rgba(30, 41, 59, 0.5); border: 1.5px solid rgba(255, 255, 255, 0.1); border-radius: 8px; padding: 8px 12px; font-size: 0.9rem; color: #ffffff; outline: none; transition: all 0.2s;" onfocus="this.style.borderColor='#E19864'; this.style.background='rgba(30, 41, 59, 0.8)'" onblur="this.style.borderColor='rgba(255, 255, 255, 0.1)'; this.style.background='rgba(30, 41, 59, 0.5)'">
                                        </div>

                                        <?php if (!empty($fc['quantite'])): ?>
                                        <div style="margin-bottom: 1rem;">
                                            <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem;">Quantité</label>
                                            <input type="number" name="quantite" min="1" placeholder="Ex: 5" value="<?= htmlspecialchars(($old['type'] ?? '') === 'materiel' ? ($old['quantite'] ?? '') : '') ?>" style="width: 100%; background: rgba(30, 41, 59, 0.5); border: 1.5px solid rgba(255, 255, 255, 0.1); border-radius: 8px; padding: 8px 12px; font-size: 0.9rem; color: #ffffff; outline: none; transition: all 0.2s;" onfocus="this.style.borderColor='#E19864'; this.style.background='rgba(30, 41, 59, 0.8)'" onblur="this.style.borderColor='rgba(255, 255, 255, 0.1)'; this.style.background='rgba(30, 41, 59, 0.5)'">
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div style="margin-bottom: 1.2rem;">
                                            <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem;"><?= htmlspecialchars($fc['detailsLabel']) ?></label>
                                            <textarea name="details" placeholder="<?= htmlspecialchars($fc['detailsPlaceholder']) ?>" style="width: 100%; background: rgba(30, 41, 59, 0.5); border: 1.5px solid rgba(255, 255, 255, 0.1); border-radius: 8px; padding: 8px 12px; font-size: 0.9rem; color: #ffffff; min-height: 70px; resize: vertical; outline: none; transition: all 0.2s;" onfocus="this.style.borderColor='#E19864'; this.style.background='rgba(30, 41, 59, 0.8)'" onblur="this.style.borderColor='rgba(255, 255, 255, 0.1)'; this.style.background='rgba(30, 41, 59, 0.5)'"><?= htmlspecialchars(($old['type'] ?? '') === $fc['type'] ? ($old['details'] ?? '') : '') ?></textarea>
                                        </div>
                                        
                                        <button type="submit" style="background: #E19864; color: #fff; border: none; width: 100%; padding: 12px; border-radius: 8px; font-weight: 700; font-size: 0.95rem; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 15px rgba(225, 152, 100, 0.2);" onmouseover="this.style.boxShadow='0 6px 20px rgba(225, 152, 100, 0.3)'; this.style.transform='translateY(-1px)'" onmouseout="this.style.boxShadow='0 4px 15px rgba(225, 152, 100, 0.2)'; this.style.transform='translateY(0)'">
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
                                    'Rules' => ['key' => 'rules', 'type' => 'rule', 'color' => '#E19864', 'bg' => 'rgba(225, 152, 100, 0.15)'],
                                    'Materiels' => ['key' => 'materials', 'type' => 'materiel', 'color' => '#E19864', 'bg' => 'rgba(225, 152, 100, 0.15)'],
                                    'Plans' => ['key' => 'plans', 'type' => 'plan', 'color' => '#E19864', 'bg' => 'rgba(225, 152, 100, 0.15)']
                                ];
                                ?>
                                <?php foreach ($groups as $groupTitle => $groupConfig): ?>
                                    <div class="ressource-list-card" style="background: rgba(30, 41, 59, 0.3); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 25px rgba(0,0,0,0.2)'; this.style.borderColor='rgba(255, 255, 255, 0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.1)'; this.style.borderColor='rgba(255, 255, 255, 0.05)'">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05); padding-bottom: 0.8rem;">
                                            <h3 style="margin: 0; color: #ffffff; font-size: 1.25rem; font-weight: 800;">Liste <?= htmlspecialchars($groupTitle) ?></h3>
                                            <?php $items = ${$groupConfig['key']} ?? []; ?>
                                            <span style="background: <?= $groupConfig['bg'] ?>; color: <?= $groupConfig['color'] ?>; padding: 4px 10px; border-radius: 50px; font-size: 0.8rem; font-weight: 700;">
                                                <?= count($items) ?> Items
                                            </span>
                                        </div>
                                        
                                        <ul class="ressource-list" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.8rem;">
                                            <?php if (!empty($items)): ?>
                                                <?php foreach ($items as $item): ?>
                                                    <li style="background: rgba(30, 41, 59, 0.5); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 10px; padding: 1rem; transition: all 0.2s;" onmouseover="this.style.borderColor='rgba(255, 255, 255, 0.1)'; this.style.background='rgba(30, 41, 59, 0.8)'" onmouseout="this.style.borderColor='rgba(255, 255, 255, 0.05)'; this.style.background='rgba(30, 41, 59, 0.5)'">
                                                        <?php if ($editResource && (int) $editResource['id'] === (int) $item['id']): ?>
                                                            <?php
                                                            $currentDetails = $item['details'] ?? '';
                                                            $currentQty = '';
                                                            if ($groupConfig['type'] === 'materiel') {
                                                                if (preg_match('/^Quantité: (\d+)(?:\n|$)/', $currentDetails, $matches)) {
                                                                    $currentQty = $matches[1];
                                                                    $currentDetails = preg_replace('/^Quantité: \d+(?:\n|$)/', '', $currentDetails);
                                                                }
                                                            }
                                                            ?>
                                                            <form action="<?= APP_ENTRY ?>?url=ressource/update-evenement-ressource/<?= (int) $item['id'] ?>" method="POST">
                                                                <input type="hidden" name="evenement_id" value="<?= (int) $selectedEvenementId ?>">
                                                                <div style="margin-bottom: 10px;">
                                                                    <input type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>" required style="width: 100%; background: rgba(30, 41, 59, 0.5); border: 1.5px solid rgba(255, 255, 255, 0.1); border-radius: 6px; padding: 8px 12px; font-size: 0.95rem; font-weight: 600; color: #ffffff; outline: none;">
                                                                </div>
                                                                <?php if ($groupConfig['type'] === 'materiel'): ?>
                                                                <div style="margin-bottom: 10px;">
                                                                    <input type="number" name="quantite" value="<?= htmlspecialchars($currentQty) ?>" placeholder="Quantité" style="width: 100%; background: rgba(30, 41, 59, 0.5); border: 1.5px solid rgba(255, 255, 255, 0.1); border-radius: 6px; padding: 8px 12px; font-size: 0.9rem; color: #ffffff; outline: none;">
                                                                </div>
                                                                <?php endif; ?>
                                                                <div style="margin-bottom: 12px;">
                                                                    <textarea name="details" placeholder="Details..." style="width: 100%; background: rgba(30, 41, 59, 0.5); border: 1.5px solid rgba(255, 255, 255, 0.1); border-radius: 6px; padding: 8px 12px; font-size: 0.9rem; color: #ffffff; min-height: 60px; resize: vertical; outline: none;"><?= htmlspecialchars($currentDetails) ?></textarea>
                                                                </div>
                                                                <div style="display: flex; gap: 10px;">
                                                                    <button type="submit" style="background: #E19864; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 700; cursor: pointer;">Sauvegarder</button>
                                                                    <a href="<?= APP_ENTRY ?>?url=ressource/evenement-ressources&evenement_id=<?= (int) $selectedEvenementId ?>" style="background: rgba(255, 255, 255, 0.05); color: #94a3b8; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.1);">Annuler</a>
                                                                </div>
                                                            </form>
                                                        <?php else: ?>
                                                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                                                <div>
                                                                    <strong style="color: #ffffff; font-size: 1.1rem; display: block; margin-bottom: 4px;"><?= htmlspecialchars($item['title']) ?></strong>
                                                                    <?php
                                                                        // Parse quantity from details for materiel
                                                                        $rawDetails = $item['details'] ?? '';
                                                                        $qtyBadge   = '';
                                                                        $displayDetails = $rawDetails;
                                                                        if ($groupConfig['type'] === 'materiel' && str_starts_with($rawDetails, 'Quantité: ')) {
                                                                            $lines = explode("\n", $rawDetails, 2);
                                                                            preg_match('/^Quantité: (\d+)/', $lines[0], $m);
                                                                            $qtyBadge = $m[1] ?? '';
                                                                            $displayDetails = trim($lines[1] ?? '');
                                                                        }
                                                                    ?>
                                                                    <?php if ($qtyBadge !== ''): ?>
                                                                        <span style="display:inline-flex;align-items:center;gap:4px;background:rgba(225, 152, 100, 0.15);color:#E19864;border:1px solid rgba(225, 152, 100, 0.2);padding:3px 10px;border-radius:6px;font-size:0.78rem;font-weight:700;margin-bottom:5px;">
                                                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                                                                            Quantité: <?= htmlspecialchars($qtyBadge) ?>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                    <?php if (!empty($displayDetails)): ?>
                                                                        <p style="color: #94a3b8; font-size: 0.95rem; margin: 0; line-height: 1.5;"><?= nl2br(htmlspecialchars($displayDetails)) ?></p>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div style="display: flex; gap: 8px; flex-shrink: 0;">
                                                                    <a href="<?= APP_ENTRY ?>?url=ressource/evenement-ressources&evenement_id=<?= (int) $selectedEvenementId ?>&edit_id=<?= (int) $item['id'] ?>" style="background: rgba(255, 255, 255, 0.05); border: 1.5px solid rgba(255, 255, 255, 0.1); color: #94a3b8; text-decoration: none; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.borderColor='#E19864'; this.style.color='#E19864'" onmouseout="this.style.borderColor='rgba(255, 255, 255, 0.1)'; this.style.color='#94a3b8'" title="Edit">
                                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                                    </a>
                                                                    <form action="<?= APP_ENTRY ?>?url=ressource/delete-evenement-ressource/<?= (int) $item['id'] ?>" method="POST" style="margin: 0;">
                                                                        <input type="hidden" name="evenement_id" value="<?= (int) $selectedEvenementId ?>">
                                                                        <button type="submit" onclick="return confirm('Delete this resource item?')" style="background: rgba(255, 255, 255, 0.05); border: 1.5px solid rgba(255, 255, 255, 0.1); color: #ef4444; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#ef4444'; this.style.background='rgba(239, 68, 68, 0.1)'" onmouseout="this.style.borderColor='rgba(255, 255, 255, 0.1)'; this.style.background='rgba(255, 255, 255, 0.05)'" title="Delete">
                                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <li class="empty" style="text-align: center; padding: 1.5rem; color: #94a3b8; border: 2px dashed rgba(255, 255, 255, 0.05); border-radius: 10px;">
                                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.4rem;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                                    <p style="margin: 0; font-size: 0.95rem;">No <?= strtolower($groupTitle) ?> added yet.</p>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                <?php endforeach; ?>
                                
                                <div style="margin-top: 1rem; display: flex; justify-content: flex-end;">
                                    <button type="button" id="saveAllTeacherResourcesBtn" style="background: #E19864; color: #fff; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 15px rgba(225, 152, 100, 0.2); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(225, 152, 100, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(225, 152, 100, 0.2)'">
                                        Sauvegarder Tout
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>

<style>
    .neo-scrollbar::-webkit-scrollbar { width: 6px; }
    .neo-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .neo-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .neo-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @media (max-width: 992px) {
        .ressource-content-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<script>
(function () {
    const saveBtn = document.getElementById('saveAllResourcesBtn') || document.getElementById('saveAllTeacherResourcesBtn');
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
                window.location.href = '<?= APP_ENTRY ?>?url=event/evenements';
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

        window.location.href = '<?= APP_ENTRY ?>?url=event/evenements';
    });

    const generateAiBtn = document.getElementById('generateAiBtn');
    if (generateAiBtn) {
        generateAiBtn.addEventListener('click', async function() {
            const originalContent = generateAiBtn.innerHTML;
            generateAiBtn.disabled = true;
            generateAiBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="animation: spin 1s linear infinite;"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg> Génération...';
            
            try {
                const formData = new FormData();
                formData.append('evenement_id', <?= (int)$selectedEvenementId ?>);
                
                const response = await fetch('<?= APP_ENTRY ?>?url=ressource/generate-ai-resources', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Erreur: ' + data.message);
                }
            } catch (err) {
                console.error(err);
                alert('Erreur de connexion lors de la génération IA.');
            } finally {
                generateAiBtn.disabled = false;
                generateAiBtn.innerHTML = originalContent;
            }
        });
    }
})();
</script>
