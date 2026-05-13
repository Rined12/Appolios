<?php
/**
 * APPOLIOS - Admin Add Teacher Page
 */

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>

<div style="padding: 2rem; max-width: 900px; margin: 0 auto;">
    <div class="neo-glass-card" style="width: 100%; background: #ffffff; border-radius: 20px; box-shadow: 0 15px 40px rgba(43, 72, 101, 0.08); border: 1px solid rgba(233, 241, 250, 0.8); overflow: hidden; display: flex; flex-direction: column;">
        
        <!-- Header Area -->
        <div style="padding: 3.5rem; background: #fcfcfc; position: relative; overflow: hidden; border-bottom: 1px solid #eef2f6;">
            <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: #e9f1fa; border-radius: 50%; z-index: 0; opacity: 0.7;"></div>
            <div style="position: absolute; bottom: -50px; right: 10%; width: 300px; height: 300px; background: #fef2f2; border-radius: 50%; z-index: 0; opacity: 0.4;"></div>
            
            <div style="position: relative; z-index: 2;">
                <a href="javascript:history.back()" style="display: inline-flex; align-items: center; gap: 8px; font-size: 1.15rem; color: #548CA8; font-weight: 700; text-decoration: none; margin-bottom: 2rem; transition: color 0.2s;" onmouseover="this.style.color='#355C7D'" onmouseout="this.style.color='#548CA8'">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Back
                </a>

                <h2 style="font-size: 2.8rem; font-weight: 800; color: #2B4865; line-height: 1.15; margin: 0 0 0.5rem 0; letter-spacing: -0.02em;">
                    Add <span style="color: #548CA8;">Teacher</span>
                </h2>
                <p style="color: #64748b; font-size: 1.1rem; line-height: 1.6; margin: 0; max-width: 90%;">
                    Create a teacher account manually. Only administrators have this privilege.
                </p>
            </div>
        </div>

        <!-- Content Area: Form -->
        <div style="padding: 3rem; background: #ffffff;">
            <form action="<?= APP_ENTRY ?>?url=admin/store-teacher" method="POST" class="neo-form-grid" novalidate>
                
                <div class="neo-form-group col-span-2">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" placeholder="Enter teacher's full name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" class="neo-input <?= isset($errors['name']) ? 'neo-error-input' : '' ?>">
                    <?php if (isset($errors['name'])): ?>
                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['name']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="neo-form-group col-span-2">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" placeholder="Enter teacher's email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="neo-input <?= isset($errors['email']) ? 'neo-error-input' : '' ?>">
                    <?php if (isset($errors['email'])): ?>
                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="neo-form-group col-span-2">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" placeholder="Create a password (min 6 characters)" class="neo-input <?= isset($errors['password']) ? 'neo-error-input' : '' ?>">
                    <?php if (isset($errors['password'])): ?>
                        <div class="neo-error-text"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><?= htmlspecialchars($errors['password']) ?></div>
                    <?php else: ?>
                        <small style="color: #94a3b8; font-size: 0.85rem; margin-top: 6px; display: block;">Minimum 6 characters</small>
                    <?php endif; ?>
                </div>

                <div class="col-span-2" style="background: #fffbeb; border: 1px solid #fef3c7; padding: 1rem 1.5rem; border-radius: 12px; margin: 1rem 0; display: flex; gap: 12px; align-items: flex-start;">
                    <div style="color: #d97706; margin-top: 2px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </div>
                    <div>
                        <p style="margin: 0; font-size: 0.95rem; color: #92400e; font-weight: 600; line-height: 1.5;">
                            Important Notice
                        </p>
                        <p style="margin: 4px 0 0 0; font-size: 0.85rem; color: #b45309; line-height: 1.5;">
                            Teacher accounts can create and manage their own events and resources. Only administrators can create teacher accounts to ensure platform security.
                        </p>
                    </div>
                </div>

                <div class="col-span-2" style="margin-top: 2rem;">
                    <button type="submit" class="neo-btn-primary" style="width: 100%; justify-content: center; padding: 1.2rem; font-size: 1.1rem; border-radius: 12px; box-shadow: 0 8px 20px rgba(84,140,168,0.25);">
                        Create Teacher Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .neo-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2.5rem 1.5rem;
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
        box-sizing: border-box;
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
        border-color: #548CA8;
        box-shadow: 0 0 0 4px rgba(84, 140, 168, 0.1);
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
    .neo-btn-primary {
        background: linear-gradient(135deg, #2B4865, #548CA8);
        color: #fff;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }
    .neo-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(84, 140, 168, 0.35) !important;
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
