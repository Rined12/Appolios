<?php
$teacherSidebarActive = 'questions';
$usage = isset($questionUsage) && is_array($questionUsage) ? $questionUsage : [];
$top = isset($qbTopStats) && is_array($qbTopStats) ? $qbTopStats : [];
$collections = isset($collections) && is_array($collections) ? $collections : [];
$selectedCollectionId = (int) ($selectedCollectionId ?? 0);
$selectedMap = isset($collectionSelectedMap) && is_array($collectionSelectedMap) ? $collectionSelectedMap : [];
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Banque de questions</h1>
                        <p>Créez et réutilisez vos questions dans vos quiz.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=teacher/add-question" class="btn btn-primary">Nouvelle question</a>
                    </div>
                </div>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
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
                                <input id="teacherQbSearch" type="text" placeholder="Rechercher une question..." autocomplete="off">
                            </div>
                            <input id="teacherQbTags" type="text" class="pro-select" placeholder="Tags (ex: sql, uml)" style="min-width: 200px;">
                            <select id="teacherQbDifficulty" class="pro-select" aria-label="Filtrer par niveau">
                                <option value="">Tous les niveaux</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                            <select id="teacherQbCollection" class="pro-select" aria-label="Pack">
                                <option value="0">Tous les packs</option>
                                <?php foreach ($collections as $c): ?>
                                    <option value="<?= (int) ($c['id'] ?? 0) ?>" <?= (int) ($c['id'] ?? 0) === $selectedCollectionId ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string) ($c['title'] ?? 'Pack')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select id="teacherQbSort" class="pro-select" aria-label="Trier">
                                <option value="title">Trier : Titre</option>
                                <option value="question">Trier : Question</option>
                                <option value="difficulty">Trier : Difficulté</option>
                            </select>
                        </div>
                        <div class="pro-table-toolbar-right">
                            <button type="button" class="btn btn-outline" id="teacherQbExport">Exporter CSV</button>
                        </div>
                    </div>

                    <div class="pro-table-toolbar" style="margin-top: 10px;">
                        <div class="pro-table-toolbar-left" style="gap: 10px; flex-wrap: wrap;">
                            <form method="post" action="<?= APP_ENTRY ?>?url=teacher/create-question-collection" style="display:flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                                <input type="text" name="title" class="pro-select" placeholder="Nouveau pack (ex: Révision SQL)" style="min-width: 240px;" maxlength="255" required>
                                <button type="submit" class="btn btn-primary">Créer pack</button>
                            </form>
                        </div>
                        <div class="pro-table-toolbar-right" style="gap: 10px;">
                            <?php if ($selectedCollectionId > 0): ?>
                                <a class="btn btn-outline" href="<?= APP_ENTRY ?>?url=teacher/delete-question-collection/<?= (int) $selectedCollectionId ?>" onclick="return confirm('Supprimer ce pack ?');">Supprimer pack</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="pro-table-wrap">
                        <table class="pro-table" id="teacherQbTable">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Question</th>
                                    <th>Difficulté</th>
                                    <th>Qualité</th>
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
                                        $questionText = (string) ($q['question_text'] ?? '');
                                        $tagsTxt = (string) ($q['tags'] ?? '');
                                        $qid = (int) ($q['id'] ?? 0);
                                        $u = $usage[$qid] ?? null;
                                        $att = is_array($u) ? (int) ($u['attempts'] ?? 0) : 0;
                                        $avg = is_array($u) ? (float) ($u['avg'] ?? 0) : 0;
                                        $qz = is_array($u) ? (int) ($u['quizzes'] ?? 0) : 0;
                                        $last = is_array($u) ? (string) ($u['last_attempt_at'] ?? '') : '';
                                        $qualityLabel = 'Données insuff.';
                                        $qualityClass = 'pro-badge';
                                        if ($qz <= 0 || $att <= 0) {
                                            $qualityLabel = 'Non utilisée';
                                            $qualityClass .= '';
                                        } elseif ($att >= 10) {
                                            if ($avg >= 85) { $qualityLabel = 'Trop facile'; $qualityClass .= ' pro-badge--beginner'; }
                                            elseif ($avg <= 35) { $qualityLabel = 'Trop difficile'; $qualityClass .= ' pro-badge--advanced'; }
                                            else { $qualityLabel = 'OK'; $qualityClass .= ' pro-badge--intermediate'; }
                                        }
                                    ?>
                                    <tr data-id="<?= (int) $qid ?>" data-title="<?= htmlspecialchars(mb_strtolower($titleShown)) ?>" data-question="<?= htmlspecialchars(mb_strtolower($questionText)) ?>" data-difficulty="<?= htmlspecialchars($diff) ?>" data-tags="<?= htmlspecialchars(mb_strtolower($tagsTxt)) ?>">
                                        <td>
                                            <div class="pro-cell-title">
                                                <strong><?= htmlspecialchars($titleShown) ?></strong>
                                                <span class="pro-cell-sub">#<?= (int) $qid ?></span>
                                            </div>
                                        </td>
                                        <td><div class="pro-question-text"><?= htmlspecialchars($questionText) ?></div></td>
                                        <td><span class="<?= $diffClass ?>"><?= htmlspecialchars(difficulty_label_fr($diff)) ?></span></td>
                                        <td>
                                            <span class="<?= $qualityClass ?>"><?= htmlspecialchars($qualityLabel) ?></span>
                                            <div class="pro-cell-sub">
                                                <?= (int) $qz ?> quiz · <?= (int) $att ?> tentatives · <?= (int) round($avg) ?>%<?= $last !== '' ? ' · ' . htmlspecialchars(substr($last, 0, 10)) : '' ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="pro-actions">
                                                <?php if ($selectedCollectionId > 0): ?>
                                                    <?php $inPack = !empty($selectedMap[(int) $qid]); ?>
                                                    <?php if ($inPack): ?>
                                                        <a href="<?= APP_ENTRY ?>?url=teacher/remove-question-from-collection/<?= (int) $selectedCollectionId ?>/<?= (int) $qid ?>" class="pro-icon-btn pro-icon-btn--danger" title="Retirer du pack" aria-label="Retirer du pack">
                                                            <i class="bi bi-dash-circle"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?= APP_ENTRY ?>?url=teacher/add-question-to-collection/<?= (int) $selectedCollectionId ?>/<?= (int) $qid ?>" class="pro-icon-btn" title="Ajouter au pack" aria-label="Ajouter au pack">
                                                            <i class="bi bi-plus-circle"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <a href="<?= APP_ENTRY ?>?url=teacher/edit-question/<?= (int) $qid ?>" class="pro-icon-btn" title="Modifier" aria-label="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?= APP_ENTRY ?>?url=teacher/delete-question/<?= (int) $qid ?>" class="pro-icon-btn pro-icon-btn--danger" title="Supprimer" aria-label="Supprimer" onclick="return confirm('Supprimer ?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="5">Aucune question en banque.</td></tr>
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
  var input = document.getElementById('teacherQbSearch');
  var tagsInput = document.getElementById('teacherQbTags');
  var sel = document.getElementById('teacherQbDifficulty');
  var colSel = document.getElementById('teacherQbCollection');
  var sortSel = document.getElementById('teacherQbSort');
  var exportBtn = document.getElementById('teacherQbExport');
  var table = document.getElementById('teacherQbTable');
  if (!input || !tagsInput || !sel || !colSel || !sortSel || !exportBtn || !table) return;

  function norm(v) {
    return (v || '').toString().trim().toLowerCase();
  }

  function apply() {
    var q = norm(input.value);
    var tg = norm(tagsInput.value);
    var d = norm(sel.value);
    var selectedCollection = norm(colSel.value);
    table.querySelectorAll('tbody tr').forEach(function (tr) {
      var title = norm(tr.getAttribute('data-title'));
      var question = norm(tr.getAttribute('data-question'));
      var diff = norm(tr.getAttribute('data-difficulty'));
      var tags = norm(tr.getAttribute('data-tags'));
      var matchText = !q || title.indexOf(q) !== -1 || question.indexOf(q) !== -1;
      var matchTags = !tg || tags.indexOf(tg) !== -1;
      var matchDiff = !d || diff === d;
      tr.style.display = (matchText && matchTags && matchDiff) ? '' : 'none';
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
    a.download = 'teacher_questions_bank.csv';
    document.body.appendChild(a);
    a.click();
    a.remove();
    setTimeout(function () { URL.revokeObjectURL(url); }, 250);
  }

  input.addEventListener('input', apply);
  tagsInput.addEventListener('input', apply);
  sel.addEventListener('change', apply);
  colSel.addEventListener('change', function () {
    var id = String(colSel.value || '0');
    var url = '<?= APP_ENTRY ?>?url=teacher/questions';
    if (id !== '0') url += '&collection_id=' + encodeURIComponent(id);
    window.location.href = url;
  });
  sortSel.addEventListener('change', function () { sortRows(); apply(); });
  exportBtn.addEventListener('click', function () { apply(); exportCsv(); });

  sortRows();
})();
</script>

