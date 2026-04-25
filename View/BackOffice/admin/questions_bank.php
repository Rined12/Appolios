<?php
$adminSidebarActive = 'questions';
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

