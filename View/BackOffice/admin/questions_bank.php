<?php
$adminSidebarActive = 'questions';
$top = isset($qbTopStats) && is_array($qbTopStats) ? $qbTopStats : [];
$charts = $charts ?? [];
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Banque de questions (admin)</h1>
                        <p>Créez, modifiez et réutilisez des questions dans vos quiz.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=admin/add-question" class="btn btn-primary">Nouvelle question</a>
                    </div>
                </div>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
                <?php
                    $diff = isset($charts['difficulty']) && is_array($charts['difficulty']) ? $charts['difficulty'] : [];
                    $diffTotal = 0;
                    foreach ($diff as $k => $v) { $diffTotal += (int) $v; }
                    $diffColors = [
                        'beginner' => 'rgba(34,197,94,0.92)',
                        'intermediate' => 'rgba(250,204,21,0.92)',
                        'advanced' => 'rgba(244,63,94,0.92)',
                    ];
                    $circ = 2 * 3.141592653589793 * 46;
                    $acc = 0.0;
                    $segs = [];
                    if ($diffTotal > 0) {
                        foreach ($diff as $key => $v) {
                            $val = (int) $v;
                            if ($val <= 0) continue;
                            $pct = $val / $diffTotal;
                            $len = $circ * $pct;
                            $segs[] = [
                                'key' => (string) $key,
                                'val' => $val,
                                'dash' => $len . ' ' . ($circ - $len),
                                'offset' => -$acc,
                                'color' => $diffColors[$key] ?? 'rgba(96,165,250,0.9)',
                            ];
                            $acc += $len;
                        }
                    }
                ?>
                <div class="pro-stats-grid" style="grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);">
                    <div class="pro-stat-card">
                        <div class="pro-stat-top">
                            <div class="pro-stat-title">Répartition Difficulté</div>
                            <div class="pro-stat-icon"><i class="bi bi-pie-chart"></i></div>
                        </div>
                        <div class="pro-chart-grid">
                            <div class="pro-donut" aria-hidden="true">
                                <svg viewBox="0 0 120 120">
                                    <circle cx="60" cy="60" r="46" fill="none" stroke="rgba(148,163,184,0.14)" stroke-width="16" />
                                    <?php foreach ($segs as $s): ?>
                                        <circle cx="60" cy="60" r="46" fill="none" stroke="<?= htmlspecialchars($s['color']) ?>" stroke-width="16" stroke-linecap="round" stroke-dasharray="<?= htmlspecialchars((string) $s['dash']) ?>" stroke-dashoffset="<?= htmlspecialchars((string) $s['offset']) ?>" />
                                    <?php endforeach; ?>
                                </svg>
                                <div class="pro-donut-center">
                                    <div class="pro-donut-big"><?= (int) $diffTotal ?></div>
                                    <div class="pro-donut-sub">questions</div>
                                </div>
                            </div>
                            <div class="pro-legend">
                                <div class="pro-legend-row"><span class="dot" style="background: <?= htmlspecialchars($diffColors['beginner']) ?>;"></span> Débutant <span class="v"><?= (int) ($diff['beginner'] ?? 0) ?></span></div>
                                <div class="pro-legend-row"><span class="dot" style="background: <?= htmlspecialchars($diffColors['intermediate']) ?>;"></span> Intermédiaire <span class="v"><?= (int) ($diff['intermediate'] ?? 0) ?></span></div>
                                <div class="pro-legend-row"><span class="dot" style="background: <?= htmlspecialchars($diffColors['advanced']) ?>;"></span> Avancé <span class="v"><?= (int) ($diff['advanced'] ?? 0) ?></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="pro-stat-card">
                        <div class="pro-stat-top">
                            <div class="pro-stat-title">Qualité globale</div>
                            <div class="pro-stat-icon"><i class="bi bi-stars"></i></div>
                        </div>
                        <div class="pro-stat-value"><?= (int) ($top['attempts_total'] ?? 0) ?></div>
                        <div class="pro-stat-sub">Tentatives · Moyenne <?= htmlspecialchars(number_format((float) ($top['avg_percentage'] ?? 0), 1)) ?>%</div>
                    </div>
                </div>
                <div class="pro-stats-grid">
                    <div class="pro-stat-card">
                        <div class="pro-stat-top">
                            <div class="pro-stat-title">Questions</div>
                            <div class="pro-stat-icon"><i class="bi bi-journal-text"></i></div>
                        </div>
                        <div class="pro-stat-value"><?= (int) ($top['questions_total'] ?? 0) ?></div>
                        <div class="pro-stat-sub"><?= (int) ($top['used_questions'] ?? 0) ?> utilisées dans des quiz</div>
                    </div>
                    <div class="pro-stat-card">
                        <div class="pro-stat-top">
                            <div class="pro-stat-title">Engagement</div>
                            <div class="pro-stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
                        </div>
                        <div class="pro-stat-value"><?= (int) ($top['attempts_total'] ?? 0) ?></div>
                        <div class="pro-stat-sub">Tentatives · Moyenne <?= htmlspecialchars(number_format((float) ($top['avg_percentage'] ?? 0), 1)) ?>%</div>
                    </div>
                </div>
                <div class="pro-table-card">
                    <div class="pro-table-toolbar">
                        <div class="pro-table-toolbar-left">
                            <div class="pro-search">
                                <i class="bi bi-search"></i>
                                <input id="adminQbSearch" type="text" placeholder="Rechercher une question..." autocomplete="off">
                            </div>
                            <select id="adminQbDifficulty" class="pro-select" aria-label="Filtrer par niveau">
                                <option value="">Tous les niveaux</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                            <select id="adminQbSort" class="pro-select" aria-label="Trier">
                                <option value="title">Trier : Titre</option>
                                <option value="question">Trier : Question</option>
                                <option value="difficulty">Trier : Difficulté</option>
                            </select>
                        </div>
                        <div class="pro-table-toolbar-right">
                            <button type="button" class="btn btn-outline" id="adminQbExport">Exporter CSV</button>
                        </div>
                    </div>
                    <div class="pro-table-wrap">
                        <table class="pro-table" id="adminQbTable">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Question</th>
                                    <th>Difficulté</th>
                                    <th style="width:1%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($questions)): foreach ($questions as $q): ?>
                                    <?php
                                        $diff = (string) ($q['difficulty'] ?? 'beginner');
                                        $diffClass = 'pro-badge';
                                        if ($diff === 'beginner') { $diffClass .= ' pro-badge--beginner'; }
                                        elseif ($diff === 'intermediate') { $diffClass .= ' pro-badge--intermediate'; }
                                        elseif ($diff === 'advanced') { $diffClass .= ' pro-badge--advanced'; }
                                        $titleText = (string) ($q['title'] ?? '');
                                        $titleShown = $titleText !== '' ? $titleText : 'Sans titre';
                                        $author = (string) ($q['author_name'] ?? '');
                                        $questionText = (string) ($q['question_text'] ?? '');
                                    ?>
                                    <tr data-id="<?= (int) ($q['id'] ?? 0) ?>" data-title="<?= htmlspecialchars(mb_strtolower($titleShown)) ?>" data-question="<?= htmlspecialchars(mb_strtolower($questionText)) ?>" data-difficulty="<?= htmlspecialchars($diff) ?>">
                                        <td>
                                            <div class="pro-cell-title">
                                                <strong><?= htmlspecialchars($titleShown) ?></strong>
                                                <?php if ($author !== ''): ?>
                                                    <span class="pro-cell-sub"><?= htmlspecialchars($author) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><div class="pro-question-text"><?= htmlspecialchars($questionText) ?></div></td>
                                        <td><span class="<?= $diffClass ?>"><?= htmlspecialchars(difficulty_label_fr($diff)) ?></span></td>
                                        <td>
                                            <div class="pro-actions">
                                                <a href="<?= APP_ENTRY ?>?url=admin/edit-question/<?= (int) $q['id'] ?>" class="pro-icon-btn" title="Modifier" aria-label="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?= APP_ENTRY ?>?url=admin/delete-question/<?= (int) $q['id'] ?>" class="pro-icon-btn pro-icon-btn--danger" title="Supprimer" aria-label="Supprimer" onclick="return confirm('Supprimer ?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="4">Aucune question.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
  var input = document.getElementById('adminQbSearch');
  var sel = document.getElementById('adminQbDifficulty');
  var sortSel = document.getElementById('adminQbSort');
  var exportBtn = document.getElementById('adminQbExport');
  var table = document.getElementById('adminQbTable');
  if (!input || !sel || !sortSel || !exportBtn || !table) return;

  function norm(v) {
    return (v || '').toString().trim().toLowerCase();
  }

  function apply() {
    var q = norm(input.value);
    var d = norm(sel.value);
    table.querySelectorAll('tbody tr').forEach(function (tr) {
      var title = norm(tr.getAttribute('data-title'));
      var question = norm(tr.getAttribute('data-question'));
      var diff = norm(tr.getAttribute('data-difficulty'));
      var matchText = !q || title.indexOf(q) !== -1 || question.indexOf(q) !== -1;
      var matchDiff = !d || diff === d;
      tr.style.display = (matchText && matchDiff) ? '' : 'none';
    });
  }

  function visibleRows() {
    return Array.from(table.querySelectorAll('tbody tr')).filter(function (tr) {
      return tr.style.display !== 'none';
    });
  }

  function sortRows() {
    var key = norm(sortSel.value) || 'title';
    var rows = Array.from(table.querySelectorAll('tbody tr'));
    rows.sort(function (a, b) {
      var av = norm(a.getAttribute('data-' + key));
      var bv = norm(b.getAttribute('data-' + key));
      if (av < bv) return -1;
      if (av > bv) return 1;
      var aid = parseInt(a.getAttribute('data-id') || '0', 10);
      var bid = parseInt(b.getAttribute('data-id') || '0', 10);
      return aid - bid;
    });
    var tbody = table.querySelector('tbody');
    rows.forEach(function (tr) { tbody.appendChild(tr); });
  }

  function csvEscape(v) {
    var s = (v == null ? '' : String(v));
    if (s.indexOf('"') !== -1) s = s.replace(/\"/g, '""');
    if (/[\n\r,;"]/.test(s)) return '"' + s + '"';
    return s;
  }

  function exportCsv() {
    var rows = visibleRows();
    var lines = [];
    lines.push(['ID','Titre','Question','Difficulté'].map(csvEscape).join(','));
    rows.forEach(function (tr) {
      var id = tr.getAttribute('data-id') || '';
      var tds = tr.querySelectorAll('td');
      var title = (tds[0] ? tds[0].innerText : '').replace(/\s+/g, ' ').trim();
      var question = (tds[1] ? tds[1].innerText : '').replace(/\s+/g, ' ').trim();
      var diff = (tds[2] ? tds[2].innerText : '').replace(/\s+/g, ' ').trim();
      lines.push([id, title, question, diff].map(csvEscape).join(','));
    });
    var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'admin_questions_bank.csv';
    document.body.appendChild(a);
    a.click();
    a.remove();
    setTimeout(function () { URL.revokeObjectURL(url); }, 250);
  }

  input.addEventListener('input', apply);
  sel.addEventListener('change', apply);
  sortSel.addEventListener('change', function () { sortRows(); apply(); });
  exportBtn.addEventListener('click', function () { apply(); exportCsv(); });

  sortRows();
})();
</script>

