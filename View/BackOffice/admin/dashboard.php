<?php
/**
 * APPOLIOS - Admin Dashboard (Neo Admin Pro)
 */
?>

<!-- Quick Navigation Header (As requested) -->

<!-- Welcome Section -->
<div style="margin-bottom: 2.5rem;">
    <h1 style="font-size: 1.8rem; font-weight: 800; color: #1e293b; margin: 0 0 0.5rem 0;">Tableau de Bord</h1>
    <p style="color: #64748b; margin: 0;">Bienvenue, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong>. Voici l'état actuel de votre plateforme.</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid-pro">
    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background: #e0f2fe; color: #0369a1;">
            <i class="bi bi-people-fill"></i>
        </div>
        <div>
            <div class="stat-label">Total Utilisateurs</div>
            <div class="stat-value"><?= $totalUsers ?? 0 ?></div>
        </div>
    </div>

    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background: #fef3c7; color: #b45309;">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div>
            <div class="stat-label">Enseignants</div>
            <div class="stat-value"><?= $totalTeachers ?? 0 ?></div>
        </div>
    </div>

    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background: #dcfce7; color: #15803d;">
            <i class="bi bi-book-half"></i>
        </div>
        <div>
            <div class="stat-label">Cours Actifs</div>
            <div class="stat-value"><?= $totalCourses ?? 0 ?></div>
        </div>
    </div>

    <div class="stat-card-pro">
        <div class="stat-icon-pro" style="background: #fee2e2; color: #b91c1c;">
            <i class="bi bi-calendar-check-fill"></i>
        </div>
        <div>
            <div class="stat-label">Événements</div>
            <div class="stat-value"><?= $totalEvenements ?? 0 ?></div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.8fr 1fr; gap: 2rem;">
    
    <!-- Left Column: Tables -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        
        <!-- Recent Events -->
        <div class="admin-card" style="padding: 0; overflow: hidden;">
            <div class="admin-card-header" style="padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; margin-bottom: 0;">
                <h2 class="admin-card-title">Événements Récents</h2>
                <a href="<?= APP_ENTRY ?>?url=admin/evenements" class="btn-admin btn-admin-primary" style="padding: 6px 14px; font-size: 0.8rem;">Tout voir</a>
            </div>
            <div style="overflow-x: auto;">
                <table class="admin-table-pro">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Date</th>
                            <th>Lieu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentEvenements)): ?>
                            <?php foreach ($recentEvenements as $evenement): ?>
                                <tr>
                                    <td style="font-weight: 700;"><?= htmlspecialchars($evenement['title']) ?></td>
                                    <td><?= date('d M, Y H:i', strtotime($evenement['event_date'])) ?></td>
                                    <td><span class="admin-badge admin-badge-info"><?= htmlspecialchars($evenement['location'] ?: 'En ligne') ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center; padding: 2rem; color: #94a3b8;">Aucun événement.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pending Applications card removed -->


    </div>

    <!-- Right Column: Quick Actions & Status -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        
        <!-- Profile & Face ID -->
        <div class="admin-card" style="background: var(--admin-active-gradient); color: white;">
            <h2 class="admin-card-title" style="color: white; margin-bottom: 1rem;">Sécurité Biométrique</h2>
            <p style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 1.5rem;">Configurez votre Face ID pour une connexion plus rapide et sécurisée à votre espace admin.</p>
            <button onclick="openFaceSetupModal()" class="btn-admin" style="width: 100%; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white; justify-content: center;">
                <i class="bi bi-camera-fill"></i> Configurer Face ID
            </button>
        </div>

        <!-- System Health -->
        <div class="admin-card">
            <h2 class="admin-card-title" style="margin-bottom: 1.5rem;">État du Système</h2>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e;"></span>
                        <span style="font-size: 0.9rem; font-weight: 600;">Serveur Web</span>
                    </div>
                    <span class="admin-badge admin-badge-success">Stable</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e;"></span>
                        <span style="font-size: 0.9rem; font-weight: 600;">Base de données</span>
                    </div>
                    <span class="admin-badge admin-badge-success">Connecté</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></span>
                        <span style="font-size: 0.9rem; font-weight: 600;">Stockage</span>
                    </div>
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b;">72% utilisé</span>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Face ID Setup Modal (Re-using logic but with new styling) -->
<div id="face-setup-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(15,23,42,.8);backdrop-filter:blur(8px);align-items:center;justify-content:center;">
    <div style="background:white;border-radius:24px;padding:2.5rem;max-width:480px;width:95%;text-align:center;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);position:relative;">
        <button onclick="closeFaceSetupModal()" style="position:absolute;top:20px;right:20px;background:#f1f5f9;border:none;width:32px;height:32px;border-radius:50%;cursor:pointer;color:#64748b;">✕</button>
        
        <div style="width:60px;height:60px;background:var(--admin-active-gradient);border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <i class="bi bi-camera-fill" style="color:white; font-size:1.8rem;"></i>
        </div>
        <h3 style="margin:0 0 8px;color:#1e293b;font-size:1.4rem;font-weight:800;">Configurer Face ID</h3>
        <p id="fsm-status-text" style="margin:0 0 24px;color:#64748b;font-size:.95rem;">Initialisation des modèles...</p>
        
        <div style="position:relative;display:inline-block;border-radius:20px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,.2);">
            <video id="fsm-video" autoplay muted playsinline width="360" height="270" style="display:block;background:#0f172a;"></video>
            <canvas id="fsm-canvas" width="360" height="270" style="position:absolute;top:0;left:0;pointer-events:none;"></canvas>
            <div id="fsm-ring" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:180px;height:180px;border-radius:50%;border:4px solid rgba(255,255,255,.3);animation:pulse 2s infinite;pointer-events:none;"></div>
        </div>

        <div id="fsm-error" style="display:none;margin-top:20px;padding:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;color:#b91c1c;font-size:.9rem;"></div>
        <div id="fsm-success" style="display:none;margin-top:20px;padding:12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;color:#166534;font-size:.9rem;"></div>

        <div style="display:flex;gap:12px;justify-content:center;margin-top:24px;">
            <button id="fsm-capture-btn" onclick="captureAndSave()" class="btn-admin btn-admin-primary" style="justify-content:center; flex:1;">Capturer & Enregistrer</button>
            <button onclick="closeFaceSetupModal()" class="btn-admin" style="background:#f1f5f9; color:#64748b; flex:1; justify-content:center;">Annuler</button>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0% { transform: translate(-50%, -50%) scale(0.95); opacity: 0.5; }
    50% { transform: translate(-50%, -50%) scale(1.05); opacity: 1; }
    100% { transform: translate(-50%, -50%) scale(0.95); opacity: 0.5; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
(function() {
    const MODELS = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@0.22.2/weights';
    const SAVE_URL = '<?= APP_ENTRY ?>?url=auth/save-face-descriptor';
    let stream = null, loaded = false, detecting = false, lastDescriptor = null;

    function el(id) { return document.getElementById(id); }
    function setStatus(t) { el('fsm-status-text').textContent = t; }
    function showErr(m) { el('fsm-error').style.display = 'block'; el('fsm-error').textContent = m; el('fsm-success').style.display = 'none'; }
    function showOk(m) { el('fsm-success').style.display = 'block'; el('fsm-success').textContent = m; el('fsm-error').style.display = 'none'; }

    async function loadModels() {
        if (loaded) return;
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODELS),
            faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODELS),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODELS)
        ]);
        loaded = true;
    }

    window.openFaceSetupModal = async function() {
        el('face-setup-modal').style.display = 'flex';
        setStatus('Chargement des modèles...');
        try {
            await loadModels();
            setStatus('Démarrage de la caméra...');
            stream = await navigator.mediaDevices.getUserMedia({ video: { width: 360, height: 270 } });
            el('fsm-video').srcObject = stream;
            startPreview();
        } catch (e) { showErr('Erreur: ' + e.message); }
    };

    function startPreview() {
        const v = el('fsm-video'), c = el('fsm-canvas'), ctx = c.getContext('2d');
        const opts = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: .5 });
        const loop = setInterval(async () => {
            if (v.readyState < 2) return;
            ctx.clearRect(0, 0, c.width, c.height);
            const r = await faceapi.detectSingleFace(v, opts).withFaceLandmarks(true).withFaceDescriptor();
            if (r) {
                const dims = faceapi.matchDimensions(c, v, true);
                faceapi.draw.drawDetections(c, faceapi.resizeResults(r, dims));
                lastDescriptor = Array.from(r.descriptor);
                setStatus('Visage détecté ! Prêt pour la capture.');
            } else {
                lastDescriptor = null;
                setStatus('Positionnez votre visage devant la caméra.');
            }
        }, 500);
        window._fsmLoop = loop;
    }

    window.captureAndSave = async function() {
        if (!lastDescriptor) { showErr('Aucun visage détecté.'); return; }
        setStatus('Enregistrement...');
        try {
            const res = await fetch(SAVE_URL, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ descriptor: lastDescriptor }) });
            const d = await res.json();
            if (d.success) showOk('Face ID enregistré avec succès !');
            else showErr(d.message);
        } catch (e) { showErr('Erreur réseau.'); }
    };

    window.closeFaceSetupModal = function() {
        if (stream) stream.getTracks().forEach(t => t.stop());
        clearInterval(window._fsmLoop);
        el('face-setup-modal').style.display = 'none';
    };
})();
</script>