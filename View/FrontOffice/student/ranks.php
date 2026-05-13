<?php
$studentSidebarActive = 'ranks';
?>
<div class="dashboard student-events-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main" style="grid-template-rows: min-content; align-items: start; height: auto; align-content: start;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <div>
                        <h1 style="margin: 0 0 0.5rem 0; font-size: 24px;">Your Rank</h1>
                        <p style="color: #64748b; margin: 0;">Track your progress and level up</p>
                    </div>
                    <button onclick="document.getElementById('ranksModal').style.display='flex'" style="background: linear-gradient(135deg, #1e293b, #334155); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        View All Ranks
                    </button>
                </div>

                <div style="background: linear-gradient(135deg, #E19864, #C87B46); border-radius: 20px; padding: 2rem; text-align: center; color: white; margin-bottom: 2rem;">
                    <div style="font-size: 4rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($levelInfo['icon'] ?? '⭐') ?></div>
                    <div style="font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem;"><?= htmlspecialchars($levelInfo['level'] ?? 'Private') ?></div>
                    <div style="font-size: 1.2rem; opacity: 0.9;"><?= (int) $totalXP ?> XP</div>
                </div>

                <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <h3 style="margin: 0 0 1rem 0; color: #1e293b;">Progress to Next Level</h3>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #64748b;"><?= htmlspecialchars($levelInfo['next_level'] ?? 'Max Level') ?></span>
                        <span style="font-weight: 600; color: #E19864;"><?= (int) ($levelInfo['xp_to_next'] ?? 0) ?> XP needed</span>
                    </div>
                    <div style="width: 100%; background: #e2e8f0; border-radius: 99px; height: 12px; overflow: hidden;">
                        <div style="height: 100%; background: linear-gradient(90deg, #E19864, #f9b384); width: <?= (int) ($levelInfo['progress'] ?? 0) ?>%;"></div>
                    </div>
                </div>

                <div style="margin-top: 1.5rem; background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <h3 style="margin: 0 0 1rem 0; color: #1e293b;">How to Earn XP</h3>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: #f8fafc; border-radius: 8px;">
                            <span>Complete a lesson</span>
                            <span style="font-weight: 600; color: #10b981;">+15 XP</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: #f8fafc; border-radius: 8px;">
                            <span>Pass a quiz</span>
                            <span style="font-weight: 600; color: #10b981;">+50 XP</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: #f8fafc; border-radius: 8px;">
                            <span>Write a review</span>
                            <span style="font-weight: 600; color: #10b981;">+20 XP</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: #f8fafc; border-radius: 8px;">
                            <span>Enroll in a course</span>
                            <span style="font-weight: 600; color: #10b981;">+25 XP</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$allRanks = [
    ['name' => 'Private', 'min' => 0, 'icon' => '⭐'],
    ['name' => 'Corporal', 'min' => 100, 'icon' => '⭐⭐'],
    ['name' => 'Sergeant', 'min' => 250, 'icon' => '⭐⭐⭐'],
    ['name' => 'Lieutenant', 'min' => 500, 'icon' => '🔱'],
    ['name' => 'Captain', 'min' => 800, 'icon' => '🎖️'],
    ['name' => 'Major', 'min' => 1200, 'icon' => '⚔️'],
    ['name' => 'Colonel', 'min' => 1800, 'icon' => '🛡️'],
    ['name' => 'General', 'min' => 2500, 'icon' => '👑'],
    ['name' => 'Commander', 'min' => 3500, 'icon' => '🏆']
];
?>
<div id="ranksModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 10000; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; padding: 20px;" onclick="if(event.target===this) { this.style.opacity='0'; setTimeout(()=>this.style.display='none',300); }">
    <div style="background: white; border-radius: 16px; width: 100%; max-width: 450px; padding: 2rem; box-shadow: 0 20px 40px rgba(0,0,0,0.2); transform: translateY(20px); transition: transform 0.3s; max-height: 85vh; display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-shrink: 0;">
            <h3 style="margin: 0; font-size: 1.4rem; color: #1e293b; font-weight: 800;">Rank Progression</h3>
            <button onclick="document.getElementById('ranksModal').style.opacity='0'; setTimeout(()=>document.getElementById('ranksModal').style.display='none',300);" style="border: none; background: #f1f5f9; color: #64748b; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'; this.style.color='#1e293b'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div style="overflow-y: auto; flex: 1; padding-right: 5px; display: flex; flex-direction: column; gap: 0.5rem; scrollbar-width: thin;">
            <?php foreach ($allRanks as $r): ?>
                <div style="display: flex; align-items: center; padding: 12px 16px; background: <?= ($levelInfo['level'] ?? '') === $r['name'] ? '#eff6ff' : '#f8fafc' ?>; border-radius: 10px; border: 1px solid <?= ($levelInfo['level'] ?? '') === $r['name'] ? '#bfdbfe' : '#e2e8f0' ?>;">
                    <div style="min-width: 75px; font-size: 1.5rem; text-align: center; white-space: nowrap; letter-spacing: -2px;"><?= $r['icon'] ?></div>
                    <div style="flex: 1; margin-left: 1rem;">
                        <div style="font-weight: 700; color: #1e293b;"><?= $r['name'] ?></div>
                        <div style="font-size: 0.85rem; color: #64748b; font-weight: 600;"><?= number_format($r['min']) ?> XP</div>
                    </div>
                    <?php if (($levelInfo['level'] ?? '') === $r['name']): ?>
                        <div style="background: #3b82f6; color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">Current</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<script>
// Enhance modal opening with animation
const originalOpen = document.querySelector('button[onclick*="ranksModal"]').onclick;
document.querySelector('button[onclick*="ranksModal"]').onclick = function(e) {
    e.preventDefault();
    const modal = document.getElementById('ranksModal');
    modal.style.display = 'flex';
    // Small delay to allow display:flex to apply before transition
    setTimeout(() => {
        modal.style.opacity = '1';
        modal.querySelector('div').style.transform = 'translateY(0)';
    }, 10);
};
</script>