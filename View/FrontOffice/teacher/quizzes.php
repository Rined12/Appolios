<?php
$teacherSidebarActive = 'quiz';
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Quiz</h1>
                        <p>Créez et gérez vos quiz.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=teacher/add-quiz" class="btn btn-primary">Nouveau quiz</a>
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
                                <input id="teacherQuizSearch" type="text" placeholder="Rechercher un quiz..." autocomplete="off">
                            </div>
                            <select id="teacherQuizDifficulty" class="pro-select" aria-label="Filtrer par niveau">
                                <option value="">Tous les niveaux</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                            <select id="teacherQuizSort" class="pro-select" aria-label="Trier">
                                <option value="title">Trier : Titre</option>
                                <option value="course">Trier : Cours</option>
                                <option value="chapter">Trier : Chapitre</option>
                                <option value="difficulty">Trier : Difficulté</option>
                            </select>
                        </div>
                        <div class="pro-table-toolbar-right">
                            <button type="button" class="btn btn-outline" id="teacherQuizExport">Exporter CSV</button>
                        </div>
                    </div>
                    <div class="pro-table-wrap">
                        <table class="pro-table" id="teacherQuizTable">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Cours</th>
                                    <th>Chapitre</th>
                                    <th>Difficulté</th>
                                    <th style="width:1%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($quizzes)): foreach ($quizzes as $q): ?>
                                    <?php
                                        $diff = (string) ($q['difficulty'] ?? 'beginner');
                                        $diffClass = 'pro-badge';
                                        if ($diff === 'beginner') { $diffClass .= ' pro-badge--beginner'; }
                                        elseif ($diff === 'intermediate') { $diffClass .= ' pro-badge--intermediate'; }
                                        elseif ($diff === 'advanced') { $diffClass .= ' pro-badge--advanced'; }
                                    ?>
                                    <tr data-id="<?= (int) ($q['id'] ?? 0) ?>" data-title="<?= htmlspecialchars(mb_strtolower((string) ($q['title'] ?? ''))) ?>" data-course="<?= htmlspecialchars(mb_strtolower((string) ($q['course_title'] ?? ''))) ?>" data-chapter="<?= htmlspecialchars(mb_strtolower((string) ($q['chapter_title'] ?? ''))) ?>" data-difficulty="<?= htmlspecialchars($diff) ?>">
                                        <td>
                                            <span class="pro-dot"></span>
                                            <?= htmlspecialchars($q['title']) ?>
                                        </td>
                                        <td><?= htmlspecialchars($q['course_title'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($q['chapter_title'] ?? '') ?></td>
                                        <td><span class="<?= $diffClass ?>"><?= htmlspecialchars(difficulty_label_fr($diff)) ?></span></td>
                                        <td>
                                            <div class="pro-actions">
                                                <a href="<?= APP_ENTRY ?>?url=teacher/edit-quiz/<?= (int) $q['id'] ?>" class="pro-icon-btn" title="Modifier" aria-label="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?= APP_ENTRY ?>?url=teacher/delete-quiz/<?= (int) $q['id'] ?>" class="pro-icon-btn pro-icon-btn--danger" title="Supprimer" aria-label="Supprimer" onclick="return confirm('Supprimer ?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="5">Aucun quiz. Créez des chapitres puis ajoutez un quiz.</td></tr>
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
  var input = document.getElementById('teacherQuizSearch');
  var sel = document.getElementById('teacherQuizDifficulty');
  var sortSel = document.getElementById('teacherQuizSort');
  var exportBtn = document.getElementById('teacherQuizExport');
  var table = document.getElementById('teacherQuizTable');
  if (!input || !sel || !sortSel || !exportBtn || !table) return;

  function norm(v) {
    return (v || '').toString().trim().toLowerCase();
  }

  function apply() {
    var q = norm(input.value);
    var d = norm(sel.value);
    table.querySelectorAll('tbody tr').forEach(function (tr) {
      var title = norm(tr.getAttribute('data-title'));
      var course = norm(tr.getAttribute('data-course'));
      var chapter = norm(tr.getAttribute('data-chapter'));
      var diff = norm(tr.getAttribute('data-difficulty'));

      var matchText = !q || title.indexOf(q) !== -1 || course.indexOf(q) !== -1 || chapter.indexOf(q) !== -1;
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
    lines.push(['Titre','Cours','Chapitre','Difficulté'].map(csvEscape).join(','));
    rows.forEach(function (tr) {
      var tds = tr.querySelectorAll('td');
      var title = (tds[0] ? tds[0].innerText : '').replace(/\s+/g, ' ').trim();
      var course = (tds[1] ? tds[1].innerText : '').replace(/\s+/g, ' ').trim();
      var chapter = (tds[2] ? tds[2].innerText : '').replace(/\s+/g, ' ').trim();
      var diff = (tds[3] ? tds[3].innerText : '').replace(/\s+/g, ' ').trim();
      lines.push([title, course, chapter, diff].map(csvEscape).join(','));
    });
    var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'teacher_quizzes.csv';
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

