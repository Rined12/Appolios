<?php
$studentSidebarActive = 'quiz';
$cert = isset($cert) && is_array($cert) ? $cert : null;
$attempt = isset($attempt) && is_array($attempt) ? $attempt : null;
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Certificat QR</h1>
                        <p>Scannez pour vérifier l'authenticité du résultat.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=student-quiz/quiz" class="btn btn-outline">Retour Quiz</a>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <?php if (!$cert || empty($cert['verify_url'])): ?>
                    <div class="pro-table-card" style="padding: 14px; background: rgba(255,255,255,.03);">
                        <div class="pro-cell-sub">Certificat indisponible.</div>
                    </div>
                <?php else: ?>
                    <?php
                        $qrData = (string) ($cert['verify_url'] ?? '');
                        $qrImg = 'https://api.qrserver.com/v1/create-qr-code/?size=340x340&data=' . rawurlencode($qrData);
                        $isLocalhost = (strpos($qrData, '://localhost') !== false) || (strpos($qrData, '://127.0.0.1') !== false);
                        $lanGuess = '';
                        if (defined('APP_LAN_HOST') && is_string(APP_LAN_HOST) && trim(APP_LAN_HOST) !== '') {
                            $lanGuess = trim((string) APP_LAN_HOST);
                        } else {
                            try {
                                $h = gethostbyname(gethostname());
                                if (is_string($h) && $h !== '' && $h !== '127.0.0.1') {
                                    $lanGuess = $h;
                                }
                            } catch (Throwable $e) {
                                $lanGuess = '';
                            }
                        }
                    ?>

                    <?php if ($attempt): ?>
                        <div class="pro-table-card" style="padding: 14px; background: rgba(255,255,255,.03); margin-bottom: 12px;">
                            <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px;">
                                <div>
                                    <div class="pro-cell-sub" style="font-weight: 900; opacity:.8;">Quiz</div>
                                    <div style="font-weight: 950; margin-top: 4px;">
                                        <?= htmlspecialchars((string) ($attempt['quiz_title'] ?? '')) ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="pro-cell-sub" style="font-weight: 900; opacity:.8;">Résultat</div>
                                    <div style="font-weight: 950; margin-top: 4px;">
                                        <?= (int) ($attempt['percentage'] ?? 0) ?>% · <?= (int) ($attempt['score'] ?? 0) ?>/<?= (int) ($attempt['total'] ?? 0) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="pro-table-card" style="padding: 14px; background: rgba(255,255,255,.03);">
                        <div style="display:grid; grid-template-columns: 360px minmax(0, 1fr); gap: 16px; align-items: start;">
                            <div class="pro-table-card" style="padding: 14px; text-align:center; background: rgba(2, 6, 23, 0.25);">
                                <img id="certQrImg" src="<?= htmlspecialchars($qrImg) ?>" alt="QR" width="340" height="340" style="max-width:100%; border-radius: 18px; border: 1px solid rgba(148,163,184,0.16);" />
                                <div style="margin-top:10px; font-weight: 950;">Valide 12 mois</div>
                                <div style="margin-top:6px; opacity:.82; font-weight: 800; font-size:.88rem;">Caméra iPhone / Android · Scanner QR</div>
                            </div>

                            <div class="pro-table-card" style="padding: 14px; background: rgba(2, 6, 23, 0.25);">
                                <div style="font-weight: 950;">Lien de vérification</div>

                                <?php if ($isLocalhost): ?>
                                    <div class="flash flash-error" style="margin-top:10px;">
                                        Le QR contient <strong>localhost</strong> : sur téléphone, ça ne marche pas.
                                        Solution rapide : utilise l'<strong>IP du PC</strong> (même Wi‑Fi).
                                    </div>

                                    <div class="pro-table-card" style="margin-top:12px; padding: 12px; background: rgba(255,255,255,.03); border: 1px solid rgba(148,163,184,0.14);">
                                        <div style="font-weight: 950;">Rendre le QR scannable</div>
                                        <div style="margin-top:8px; display:flex; gap: 10px; align-items:center; flex-wrap: wrap;">
                                            <label style="font-weight: 900; opacity:.85;">IP du PC</label>
                                            <input id="certLanHost" type="text" value="<?= htmlspecialchars($lanGuess !== '' ? $lanGuess : '192.168.x.x') ?>" style="flex:1; min-width: 180px; padding: 10px 12px; border-radius: 12px; border: 1px solid rgba(148,163,184,0.14); background: rgba(2, 6, 23, 0.25); color: rgba(226,232,240,0.95); font-weight: 800;" />
                                            <button type="button" class="btn btn-primary" id="applyLanHost">Appliquer</button>
                                        </div>
                                        <div style="margin-top:8px; opacity:.85; font-weight: 750;">Ton téléphone et ton PC doivent être sur le même Wi‑Fi.</div>
                                    </div>
                                <?php endif; ?>

                                <div style="margin-top:8px; display:flex; gap: 10px; align-items:center; flex-wrap: wrap;">
                                    <input id="certVerifyUrl" type="text" value="<?= htmlspecialchars((string) ($cert['verify_url'] ?? '')) ?>" readonly style="flex:1; min-width: 220px; padding: 10px 12px; border-radius: 12px; border: 1px solid rgba(148,163,184,0.14); background: rgba(2, 6, 23, 0.25); color: rgba(226,232,240,0.95); font-weight: 800;" />
                                    <button type="button" class="btn btn-outline" id="copyCertUrl">Copier</button>
                                    <a class="btn btn-primary" id="certOpenUrl" href="<?= htmlspecialchars((string) ($cert['verify_url'] ?? '')) ?>" target="_blank" rel="noopener">Ouvrir</a>
                                </div>
                                <div style="margin-top:10px; opacity:.8; font-weight: 750;">Le scanner ouvre une page sécurisée qui confirme si le certificat est authentique.</div>
                            </div>
                        </div>
                    </div>

                    <script>
                        (function () {
                            var copyBtn = document.getElementById('copyCertUrl');
                            var urlInput = document.getElementById('certVerifyUrl');
                            var openLink = document.getElementById('certOpenUrl');
                            var qrImg = document.getElementById('certQrImg');
                            var lanHostInput = document.getElementById('certLanHost');
                            var applyLanHostBtn = document.getElementById('applyLanHost');

                            if (copyBtn && urlInput) {
                                copyBtn.addEventListener('click', async function () {
                                    try {
                                        await navigator.clipboard.writeText(urlInput.value);
                                        copyBtn.textContent = 'Copié';
                                        setTimeout(function () { copyBtn.textContent = 'Copier'; }, 900);
                                    } catch (err) {
                                        urlInput.select();
                                        document.execCommand('copy');
                                    }
                                });
                            }

                            function applyLanHost() {
                                if (!lanHostInput || !urlInput || !qrImg || !openLink) return;
                                var host = (lanHostInput.value || '').trim();
                                if (!host) return;
                                var cur = urlInput.value;
                                var next = cur
                                    .replace('://localhost', '://' + host)
                                    .replace('://127.0.0.1', '://' + host);
                                urlInput.value = next;
                                openLink.setAttribute('href', next);
                                qrImg.setAttribute('src', 'https://api.qrserver.com/v1/create-qr-code/?size=340x340&data=' + encodeURIComponent(next));
                            }

                            if (applyLanHostBtn) applyLanHostBtn.addEventListener('click', applyLanHost);
                        })();
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
