<?php
/**
 * APPOLIOS - Add Evenement Page (neo theme)
 */

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

$minDate = date('Y-m-d', strtotime('+1 day'));
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1200px; width: 100%;">
        <div class="admin-layout">
            <?php $adminSidebarActive = 'evenements'; require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0;">
                

                <section class="neo-auth-wrap" style="background: transparent; font-family: 'Inter', sans-serif;">
                    
                    <div class="neo-glass-card" style="width: 100%; background: #ffffff; border-radius: 20px; box-shadow: 0 15px 40px rgba(43, 72, 101, 0.08); border: 1px solid rgba(233, 241, 250, 0.8); overflow: hidden; display: flex; flex-direction: column;">
                        
                        <!-- Header Area -->
                        <div style="padding: 3.5rem; background: #fcfcfc; position: relative; overflow: hidden; border-bottom: 1px solid #eef2f6;">
                            <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: #e9f1fa; border-radius: 50%; z-index: 0; opacity: 0.7;"></div>
                            <div style="position: absolute; bottom: -50px; right: 10%; width: 300px; height: 300px; background: #fef2f2; border-radius: 50%; z-index: 0; opacity: 0.4;"></div>
                            
                            <div style="position: relative; z-index: 2;">
                                <a href="<?= APP_ENTRY ?>?url=event/evenements" style="display: inline-flex; align-items: center; gap: 8px; font-size: 1.15rem; color: #548CA8; font-weight: 700; text-decoration: none; margin-bottom: 2rem; transition: color 0.2s;" onmouseover="this.style.color='#355C7D'" onmouseout="this.style.color='#548CA8'">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                                    Back to Evenements
                                </a>

                                <h2 style="font-size: 2.8rem; font-weight: 800; color: #2B4865; line-height: 1.15; margin: 0 0 0.5rem 0; letter-spacing: -0.02em;">
                                    Ajouter <span style="color: #548CA8;">Evenement</span>
                                </h2>
                                <p style="color: #64748b; font-size: 1.1rem; line-height: 1.6; margin: 0; max-width: 90%;">
                                    Create a new event for your platform by filling out the details below.
                                </p>
                            </div>
                        </div>

                        <!-- Content Area: Form -->
                        <div style="padding: 3rem; background: #ffffff;">
                            <form action="<?= APP_ENTRY ?>?url=event/store-evenement" method="POST" class="neo-form-grid" novalidate>
                                
                                <div class="neo-form-group col-span-2">
                                    <label for="title">Titre *</label>
                                    <input type="text" id="title" name="title" placeholder="Enter event title" value="<?= htmlspecialchars($old['title'] ?? '') ?>" class="neo-input <?= isset($errors['title']) ? 'neo-error-input' : '' ?>">
                                    <?php if (isset($errors['title'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['title']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group col-span-2">
                                    <label for="description">Description *</label>
                                    <textarea id="description" name="description" placeholder="Describe the event" class="neo-input <?= isset($errors['description']) ? 'neo-error-input' : '' ?>" style="min-height: 120px; resize: vertical;"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                                    <?php if (isset($errors['description'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['description']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group">
                                    <label for="date_debut">Date Debut *</label>
                                    <input type="date" id="date_debut" name="date_debut" data-js-min="<?= htmlspecialchars($minDate) ?>" value="<?= htmlspecialchars($old['date_debut'] ?? '') ?>" class="neo-input <?= isset($errors['date_debut']) ? 'neo-error-input' : '' ?>">
                                    <?php if (isset($errors['date_debut'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['date_debut']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group">
                                    <label for="date_fin">Date Fin</label>
                                    <input type="date" id="date_fin" name="date_fin" value="<?= htmlspecialchars($old['date_fin'] ?? '') ?>" class="neo-input <?= isset($errors['date_fin']) ? 'neo-error-input' : '' ?>">
                                    <?php if (isset($errors['date_fin'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['date_fin']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group">
                                    <label for="heure_debut">Heure Debut *</label>
                                    <input type="time" id="heure_debut" name="heure_debut" value="<?= htmlspecialchars($old['heure_debut'] ?? '') ?>" class="neo-input <?= isset($errors['heure_debut']) ? 'neo-error-input' : '' ?>">
                                    <?php if (isset($errors['heure_debut'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['heure_debut']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group">
                                    <label for="heure_fin">Heure Fin</label>
                                    <input type="time" id="heure_fin" name="heure_fin" value="<?= htmlspecialchars($old['heure_fin'] ?? '') ?>" class="neo-input <?= isset($errors['heure_fin']) ? 'neo-error-input' : '' ?>">
                                    <?php if (isset($errors['heure_fin'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['heure_fin']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group">
                                    <label for="lieu">Lieu</label>
                                    <input type="text" id="lieu" name="lieu" placeholder="Online / Room A / Main Hall" value="<?= htmlspecialchars($old['lieu'] ?? '') ?>" class="neo-input <?= isset($errors['lieu']) ? 'neo-error-input' : '' ?>">
                                    <?php if (isset($errors['lieu'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['lieu']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group">
                                    <label for="capacite_max">Capacite Max</label>
                                    <input type="number" id="capacite_max" name="capacite_max" data-js-min="0" placeholder="Ex: 200" value="<?= htmlspecialchars($old['capacite_max'] ?? '') ?>" class="neo-input <?= isset($errors['capacite_max']) ? 'neo-error-input' : '' ?>">
                                    <?php if (isset($errors['capacite_max'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['capacite_max']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group">
                                    <label for="type">Type</label>
                                    <input type="text" id="type" name="type" placeholder="conference, workshop..." value="<?= htmlspecialchars($old['type'] ?? '') ?>" class="neo-input <?= isset($errors['type']) ? 'neo-error-input' : '' ?>">
                                    <?php if (isset($errors['type'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['type']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="neo-form-group">
                                    <label for="statut">Statut</label>
                                    <select id="statut" name="statut" class="neo-input <?= isset($errors['statut']) ? 'neo-error-input' : '' ?>">
                                        <?php $selectedStatut = $old['statut'] ?? 'planifie'; ?>
                                        <option value="planifie" <?= $selectedStatut === 'planifie' ? 'selected' : '' ?>>Planifie</option>
                                        <option value="en_cours" <?= $selectedStatut === 'en_cours' ? 'selected' : '' ?>>En cours</option>
                                        <option value="termine" <?= $selectedStatut === 'termine' ? 'selected' : '' ?>>Termine</option>
                                        <option value="annule" <?= $selectedStatut === 'annule' ? 'selected' : '' ?>>Annule</option>
                                    </select>
                                    <?php if (isset($errors['statut'])): ?>
                                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['statut']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-span-2" style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                                    <button type="submit" name="action" value="save" class="neo-submit-btn" style="width: auto; padding: 14px 28px; background: #f8fafc; color: #64748b; border: 1.5px solid #e2e8f0; box-shadow: none; font-size: 1rem;">Create Only</button>
                                    <button type="submit" name="action" value="save_and_resources" class="neo-submit-btn" style="width: auto; padding: 14px 28px; font-size: 1rem;">Create & Add Resources</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<style>
    .neo-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    .col-span-2 {
        grid-column: span 2;
    }
    .neo-form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .neo-form-group label {
        color: #475569;
        font-size: 0.95rem;
        font-weight: 700;
        margin: 0;
    }
    .neo-input {
        width: 100%;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 1rem;
        color: #1e293b;
        outline: none;
        transition: all 0.2s ease;
        font-family: inherit;
    }
    .neo-input:focus {
        background: #ffffff;
        border-color: #E19864;
        box-shadow: 0 0 0 4px rgba(225, 152, 100, 0.1);
    }
    .neo-error-input {
        border-color: #ef4444 !important;
        background: #fef2f2 !important;
    }
    .neo-error-input:focus {
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15) !important;
    }
    .neo-error-text {
        color: #ef4444;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .neo-error-text svg {
        width: 14px;
        height: 14px;
        stroke-width: 2.5;
    }
    .neo-submit-btn {
        background: #E19864;
        color: #fff;
        border: none;
        width: 100%;
        padding: 16px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 15px rgba(225, 152, 100, 0.2);
    }
    .neo-submit-btn:hover {
        box-shadow: 0 8px 25px rgba(225, 152, 100, 0.3);
        transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
        .neo-form-grid {
            grid-template-columns: 1fr;
        }
        .col-span-2 {
            grid-column: span 1;
        }
    }
</style>
