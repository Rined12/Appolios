<?php
$adminSidebarActive = 'quiz';
$items = isset($items) && is_array($items) ? $items : [];
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>AI Risk Review</h1>
                        <p>Analyse IA des items à risque (causes + plan d’action) sans modifier la base automatiquement.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=admin-quiz/riskQueue" class="btn btn-outline">Retour Risk Queue</a>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <div class="pro-table-card">
                    <div class="pro-table-wrap">
                        <table class="pro-table" id="aiRiskTable">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Objet</th>
                                    <th>Risque</th>
                                    <th style="width:1%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $it): ?>
                                    <?php
                                        $type = (string) ($it['type'] ?? '');
                                        $id = (int) ($it['id'] ?? 0);
                                        $title = (string) ($it['title'] ?? '');
                                        $sub = (string) ($it['sub'] ?? '');
                                        $risk = (int) ($it['risk_score'] ?? 0);
                                        $level = (string) ($it['risk_level'] ?? 'LOW');

                                        $badge = 'pro-badge pro-badge--beginner';
                                        if ($level === 'HIGH') $badge = 'pro-badge pro-badge--advanced';
                                        elseif ($level === 'MEDIUM') $badge = 'pro-badge pro-badge--intermediate';

                                        $typeBadge = 'pro-badge';
                                        if ($type === 'quiz') $typeBadge .= ' pro-badge--intermediate';
                                        if ($type === 'question') $typeBadge .= ' pro-badge--beginner';
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="<?= htmlspecialchars($typeBadge) ?>"><?= $type === 'quiz' ? 'Quiz' : 'Question' ?></span>
                                            <div class="pro-cell-sub">#<?= (int) $id ?></div>
                                        </td>
                                        <td>
                                            <div class="pro-cell-title"><strong><?= htmlspecialchars($title) ?></strong></div>
                                            <?php if ($sub !== ''): ?><div class="pro-cell-sub"><?= htmlspecialchars($sub) ?></div><?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="<?= htmlspecialchars($badge) ?>"><?= htmlspecialchars($level) ?></span>
                                            <div class="pro-cell-sub">Score: <?= (int) $risk ?>/100</div>
                                        </td>
                                        <td>
                                            <div class="pro-actions">
                                                <button type="button" class="btn btn-outline ai-risk-btn" data-type="<?= htmlspecialchars($type) ?>" data-id="<?= (int) $id ?>">Analyser IA</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="ai-risk-out" style="display:none;">
                                        <td colspan="4">
                                            <div class="pro-table-card" style="padding: 12px; background: rgba(255,255,255,.03);" data-out>
                                                <div class="pro-cell-sub">—</div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <script>
                (function(){
                    var table = document.getElementById('aiRiskTable');
                    if(!table) return;

                    function esc(s){
                        s = (s == null ? '' : String(s));
                        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
                    }

                    function render(outEl, data){
                        if(!data || !data.ok){
                            outEl.innerHTML = '<div class="pro-cell-sub">Erreur IA</div>';
                            return;
                        }
                        var a = data.analysis || {};
                        var html = '';
                        if(a.summary){
                            html += '<div style="font-weight: 900; margin-bottom: 6px;">Résumé</div>';
                            html += '<div class="pro-cell-sub">' + esc(a.summary) + '</div>';
                        }
                        if(Array.isArray(a.root_causes) && a.root_causes.length){
                            html += '<div style="font-weight: 900; margin: 10px 0 6px;">Causes probables</div>';
                            html += '<ul style="margin:0; padding-left: 18px;">' + a.root_causes.map(function(x){ return '<li class="pro-cell-sub">' + esc(x) + '</li>'; }).join('') + '</ul>';
                        }
                        if(Array.isArray(a.actions) && a.actions.length){
                            html += '<div style="font-weight: 900; margin: 10px 0 6px;">Plan d\'action</div>';
                            html += a.actions.map(function(act){
                                var steps = Array.isArray(act.steps) ? act.steps : [];
                                return '<div style="padding:10px; border: 1px solid rgba(148,163,184,0.14); border-radius: 12px; margin-bottom: 8px; background: rgba(2,6,23,.18);">' +
                                    '<div style="display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap;">' +
                                      '<div style="font-weight: 900;">' + esc(act.title || 'Action') + '</div>' +
                                      '<div class="pro-cell-sub">Priorité: ' + esc(act.priority || 'MEDIUM') + '</div>' +
                                    '</div>' +
                                    (steps.length ? ('<ul style="margin: 6px 0 0; padding-left: 18px;">' + steps.map(function(s){ return '<li class="pro-cell-sub">' + esc(s) + '</li>'; }).join('') + '</ul>') : '') +
                                '</div>';
                            }).join('');
                        }
                        if(a.quick_fix){
                            html += '<div style="font-weight: 900; margin: 10px 0 6px;">Quick fix</div>';
                            html += '<div class="pro-cell-sub">' + esc(a.quick_fix) + '</div>';
                        }
                        outEl.innerHTML = html || '<div class="pro-cell-sub">Aucune analyse.</div>';
                    }

                    table.querySelectorAll('.ai-risk-btn').forEach(function(btn){
                        btn.addEventListener('click', function(){
                            var tr = btn.closest('tr');
                            var outTr = tr ? tr.nextElementSibling : null;
                            if(!outTr || !outTr.classList.contains('ai-risk-out')) return;
                            var outEl = outTr.querySelector('[data-out]');
                            if(!outEl) return;

                            outTr.style.display = '';
                            outEl.innerHTML = '<div class="pro-cell-sub">Analyse IA…</div>';

                            var fd = new FormData();
                            fd.append('type', btn.getAttribute('data-type') || '');
                            fd.append('id', btn.getAttribute('data-id') || '0');

                            fetch('<?= APP_ENTRY ?>?url=admin-quiz/analyze-risk-ai', {
                                method: 'POST',
                                body: fd,
                                credentials: 'same-origin'
                            }).then(function(r){ return r.json(); })
                              .then(function(data){ render(outEl, data); })
                              .catch(function(){ outEl.innerHTML = '<div class="pro-cell-sub">Erreur IA</div>'; });
                        });
                    });
                })();
                </script>
            </div>
        </div>
    </div>
</div>
