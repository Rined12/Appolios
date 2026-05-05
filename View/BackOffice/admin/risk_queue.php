<?php
$adminSidebarActive = 'quiz';
$items = isset($items) && is_array($items) ? $items : [];
$filters = isset($filters) && is_array($filters) ? $filters : [];
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Risk Queue (Quiz/Question)</h1>
                        <p>Priorisez les contenus à revoir via un score de risque calculé automatiquement.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=admin-quiz/quizzes" class="btn btn-outline">Retour</a>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <div class="pro-table-card">
                    <div class="pro-table-toolbar">
                        <div class="pro-table-toolbar-left">
                            <div class="pro-search">
                                <i class="bi bi-search"></i>
                                <input id="riskSearch" type="text" placeholder="Rechercher..." autocomplete="off">
                            </div>
                            <select id="riskType" class="pro-select" aria-label="Filtrer par type">
                                <option value="">Tous</option>
                                <option value="quiz">Quiz</option>
                                <option value="question">Question</option>
                            </select>
                            <select id="riskLevel" class="pro-select" aria-label="Filtrer par niveau">
                                <option value="">Tous niveaux</option>
                                <option value="HIGH">HIGH</option>
                                <option value="MEDIUM">MEDIUM</option>
                                <option value="LOW">LOW</option>
                            </select>
                        </div>
                        <div class="pro-table-toolbar-right">
                            <button type="button" class="btn btn-outline" id="riskExport">Exporter CSV</button>
                        </div>
                    </div>

                    <div class="pro-table-wrap">
                        <table class="pro-table" id="riskTable">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Objet</th>
                                    <th>Risque</th>
                                    <th>Raisons</th>
                                    <th style="width:1%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($items)): foreach ($items as $it): ?>
                                    <?php
                                        $type = (string) ($it['type'] ?? '');
                                        $id = (int) ($it['id'] ?? 0);
                                        $title = (string) ($it['title'] ?? '');
                                        $sub = (string) ($it['sub'] ?? '');
                                        $risk = (int) ($it['risk_score'] ?? 0);
                                        $level = (string) ($it['risk_level'] ?? 'LOW');
                                        $reasons = isset($it['reasons']) && is_array($it['reasons']) ? $it['reasons'] : [];

                                        $badge = 'pro-badge pro-badge--beginner';
                                        if ($level === 'HIGH') $badge = 'pro-badge pro-badge--advanced';
                                        elseif ($level === 'MEDIUM') $badge = 'pro-badge pro-badge--intermediate';

                                        $typeBadge = 'pro-badge';
                                        if ($type === 'quiz') $typeBadge .= ' pro-badge--intermediate';
                                        if ($type === 'question') $typeBadge .= ' pro-badge--beginner';
                                    ?>
                                    <tr data-type="<?= htmlspecialchars($type) ?>" data-level="<?= htmlspecialchars($level) ?>" data-search="<?= htmlspecialchars(mb_strtolower($title . ' ' . $sub)) ?>" data-risk="<?= (int) $risk ?>">
                                        <td>
                                            <span class="<?= htmlspecialchars($typeBadge) ?>"><?= $type === 'quiz' ? 'Quiz' : 'Question' ?></span>
                                            <div class="pro-cell-sub">#<?= (int) $id ?></div>
                                        </td>
                                        <td>
                                            <div class="pro-cell-title"><strong><?= htmlspecialchars($title) ?></strong></div>
                                            <?php if ($sub !== ''): ?>
                                                <div class="pro-cell-sub"><?= htmlspecialchars($sub) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="<?= htmlspecialchars($badge) ?>"><?= htmlspecialchars($level) ?></span>
                                            <div class="pro-cell-sub">Score: <?= (int) $risk ?>/100</div>
                                        </td>
                                        <td>
                                            <?php if (!empty($reasons)): ?>
                                                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                                                    <?php foreach ($reasons as $r): ?>
                                                        <span class="pro-badge" style="background: rgba(148, 163, 184, 0.10); border-color: rgba(148, 163, 184, 0.18); color: rgba(226, 232, 240, 0.88);">
                                                            <?= htmlspecialchars((string) $r) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="pro-cell-sub">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="pro-actions">
                                                <?php if ($type === 'quiz'): ?>
                                                    <a href="<?= APP_ENTRY ?>?url=admin-quiz/approveQuiz/<?= (int) $id ?>" class="pro-icon-btn" title="Approuver" aria-label="Approuver"><i class="bi bi-check2"></i></a>
                                                    <a href="<?= APP_ENTRY ?>?url=admin-quiz/rejectQuiz/<?= (int) $id ?>" class="pro-icon-btn pro-icon-btn--danger" title="Refuser" aria-label="Refuser" onclick="return confirm('Refuser ce quiz ?');"><i class="bi bi-x"></i></a>
                                                    <a href="<?= APP_ENTRY ?>?url=admin-quiz/edit-quiz/<?= (int) $id ?>" class="pro-icon-btn" title="Modifier" aria-label="Modifier"><i class="bi bi-pencil"></i></a>
                                                <?php else: ?>
                                                    <a href="<?= APP_ENTRY ?>?url=admin-quiz/edit-question/<?= (int) $id ?>" class="pro-icon-btn" title="Modifier" aria-label="Modifier"><i class="bi bi-pencil"></i></a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <script>
                (function(){
                    var search = document.getElementById('riskSearch');
                    var typeSel = document.getElementById('riskType');
                    var lvlSel = document.getElementById('riskLevel');
                    var table = document.getElementById('riskTable');
                    if(!table) return;

                    function apply(){
                        var q = (search && search.value ? search.value : '').toLowerCase().trim();
                        var t = typeSel ? typeSel.value : '';
                        var l = lvlSel ? lvlSel.value : '';
                        Array.from(table.querySelectorAll('tbody tr')).forEach(function(tr){
                            var ok = true;
                            if(q){
                                var s = (tr.getAttribute('data-search') || '');
                                if(s.indexOf(q) === -1) ok = false;
                            }
                            if(ok && t){
                                if((tr.getAttribute('data-type')||'') !== t) ok = false;
                            }
                            if(ok && l){
                                if((tr.getAttribute('data-level')||'') !== l) ok = false;
                            }
                            tr.style.display = ok ? '' : 'none';
                        });
                    }

                    if(search) search.addEventListener('input', apply);
                    if(typeSel) typeSel.addEventListener('change', apply);
                    if(lvlSel) lvlSel.addEventListener('change', apply);

                    var exportBtn = document.getElementById('riskExport');
                    if(exportBtn){
                        exportBtn.addEventListener('click', function(){
                            var rows = [];
                            rows.push(['type','id','title','risk_score','risk_level','reasons'].join(','));
                            Array.from(table.querySelectorAll('tbody tr')).forEach(function(tr){
                                if(tr.style.display === 'none') return;
                                var t = tr.getAttribute('data-type') || '';
                                var lvl = tr.getAttribute('data-level') || '';
                                var id = (tr.querySelector('td .pro-cell-sub') ? tr.querySelector('td .pro-cell-sub').textContent.replace('#','').trim() : '');
                                var title = (tr.querySelector('td .pro-cell-title strong') ? tr.querySelector('td .pro-cell-title strong').textContent.trim() : '');
                                var score = (tr.getAttribute('data-risk') || '0');
                                var rs = [];
                                Array.from(tr.querySelectorAll('td:nth-child(4) .pro-badge')).forEach(function(b){ rs.push(b.textContent.trim()); });
                                function esc(v){
                                    v = String(v||'');
                                    if(v.indexOf('"') !== -1) v = v.replace(/"/g,'""');
                                    if(v.indexOf(',') !== -1 || v.indexOf('\n') !== -1) v = '"' + v + '"';
                                    return v;
                                }
                                rows.push([esc(t), esc(id), esc(title), esc(score), esc(lvl), esc(rs.join(' | '))].join(','));
                            });
                            var blob = new Blob([rows.join('\n')], {type:'text/csv;charset=utf-8;'});
                            var url = URL.createObjectURL(blob);
                            var a = document.createElement('a');
                            a.href = url;
                            a.download = 'risk_queue.csv';
                            document.body.appendChild(a);
                            a.click();
                            a.remove();
                            URL.revokeObjectURL(url);
                        });
                    }
                })();
                </script>
            </div>
        </div>
    </div>
</div>
