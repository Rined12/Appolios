<?php
$adminSidebarActive = 'quiz_history';
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Historique des quiz</h1>
                        <p>Consultez tous les quiz et leur créateur.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=admin/quizStats" class="btn btn-stats-pro">
                            <i class="bi bi-graph-up" aria-hidden="true"></i>
                            Statistiques
                            <span class="btn-stats-pro-badge">PRO</span>
                        </a>
                        <a href="<?= APP_ENTRY ?>?url=admin/quizzes" class="btn btn-outline">Retour aux quiz</a>
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
                                <input id="adminQuizHistSearch" type="text" placeholder="Rechercher (quiz / cours / chapitre / professeur)..." autocomplete="off">
                            </div>
                            <select id="adminQuizHistStatus" class="pro-select" aria-label="Filtrer par statut">
                                <option value="">Tous les statuts</option>
                                <option value="approved">Approuvé</option>
                                <option value="pending">En attente</option>
                                <option value="rejected">Refusé</option>
                            </select>
                            <select id="adminQuizHistDifficulty" class="pro-select" aria-label="Filtrer par niveau">
                                <option value="">Tous les niveaux</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                            <select id="adminQuizHistSort" class="pro-select" aria-label="Trier">
                                <option value="title">Trier : Titre</option>
                                <option value="author">Trier : Professeur</option>
                                <option value="course">Trier : Cours</option>
                                <option value="chapter">Trier : Chapitre</option>
                                <option value="difficulty">Trier : Difficulté</option>
                                <option value="status">Trier : Statut</option>
                            </select>
                        </div>
                        <div class="pro-table-toolbar-right">
                            <button type="button" class="btn btn-outline" id="adminQuizHistExport">Exporter CSV</button>
                        </div>
                    </div>

                    <div class="pro-table-wrap">
                        <table class="pro-table" id="adminQuizHistTable">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Professeur</th>
                                    <th>Cours</th>
                                    <th>Chapitre</th>
                                    <th>Difficulté</th>
                                    <th>Statut</th>
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
                                        $st = (string) ($q['status'] ?? 'approved');
                                        $author = (string) ($q['author_name'] ?? '');
                                    ?>
                                    <tr data-id="<?= (int) ($q['id'] ?? 0) ?>"
                                        data-title="<?= htmlspecialchars(mb_strtolower((string) ($q['title'] ?? ''))) ?>"
                                        data-author="<?= htmlspecialchars(mb_strtolower($author)) ?>"
                                        data-course="<?= htmlspecialchars(mb_strtolower((string) ($q['course_title'] ?? ''))) ?>"
                                        data-chapter="<?= htmlspecialchars(mb_strtolower((string) ($q['chapter_title'] ?? ''))) ?>"
                                        data-difficulty="<?= htmlspecialchars($diff) ?>"
                                        data-status="<?= htmlspecialchars($st) ?>">
                                        <td>
                                            <span class="pro-dot"></span>
                                            <?= htmlspecialchars((string) ($q['title'] ?? '')) ?>
                                        </td>
                                        <td>
                                            <div class="pro-cell-title">
                                                <strong><?= htmlspecialchars($author !== '' ? $author : '—') ?></strong>
                                                <?php if (!empty($q['author_role'])): ?>
                                                    <span class="pro-cell-sub"><?= htmlspecialchars((string) $q['author_role']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars((string) ($q['course_title'] ?? '')) ?></td>
                                        <td><?= htmlspecialchars((string) ($q['chapter_title'] ?? '')) ?></td>
                                        <td><span class="<?= $diffClass ?>"><?= htmlspecialchars(difficulty_label_fr($diff)) ?></span></td>
                                        <td>
                                            <?php if ($st === 'approved'): ?>
                                                <span class="pro-status pro-status--approved"><i class="bi bi-check-circle"></i> Approuvé</span>
                                            <?php elseif ($st === 'pending'): ?>
                                                <span class="pro-status pro-status--pending"><i class="bi bi-clock"></i> En attente</span>
                                            <?php else: ?>
                                                <span class="pro-status pro-status--rejected"><i class="bi bi-x-circle"></i> Refusé</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="pro-actions">
                                                <?php if ($st === 'pending'): ?>
                                                    <a href="<?= APP_ENTRY ?>?url=admin/approve-quiz/<?= (int) $q['id'] ?>" class="pro-icon-btn" title="Approuver" aria-label="Approuver">
                                                        <i class="bi bi-check2"></i>
                                                    </a>
                                                    <a href="<?= APP_ENTRY ?>?url=admin/reject-quiz/<?= (int) $q['id'] ?>" class="pro-icon-btn pro-icon-btn--danger" title="Refuser" aria-label="Refuser" onclick="return confirm('Refuser ce quiz ?');">
                                                        <i class="bi bi-x"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?= APP_ENTRY ?>?url=admin/edit-quiz/<?= (int) $q['id'] ?>" class="pro-icon-btn" title="Modifier" aria-label="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?= APP_ENTRY ?>?url=admin/delete-quiz/<?= (int) $q['id'] ?>" class="pro-icon-btn pro-icon-btn--danger" title="Supprimer" aria-label="Supprimer" onclick="return confirm('Supprimer ?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="7">Aucun quiz.</td></tr>
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
  var input = document.getElementById('adminQuizHistSearch');
  var sel = document.getElementById('adminQuizHistDifficulty');
  var selSt = document.getElementById('adminQuizHistStatus');
  var sortSel = document.getElementById('adminQuizHistSort');
  var exportBtn = document.getElementById('adminQuizHistExport');
  var table = document.getElementById('adminQuizHistTable');
  if (!input || !sel || !selSt || !sortSel || !exportBtn || !table) return;

  function norm(v) {
    return (v || '').toString().trim().toLowerCase();
  }

  function apply() {
    var q = norm(input.value);
    var d = norm(sel.value);
    var st = norm(selSt.value);
    table.querySelectorAll('tbody tr').forEach(function (tr) {
      var title = norm(tr.getAttribute('data-title'));
      var author = norm(tr.getAttribute('data-author'));
      var course = norm(tr.getAttribute('data-course'));
      var chapter = norm(tr.getAttribute('data-chapter'));
      var diff = norm(tr.getAttribute('data-difficulty'));
      var status = norm(tr.getAttribute('data-status'));

      var matchText = !q || title.indexOf(q) !== -1 || author.indexOf(q) !== -1 || course.indexOf(q) !== -1 || chapter.indexOf(q) !== -1;
      var matchDiff = !d || diff === d;
      var matchStatus = !st || status === st;
      tr.style.display = (matchText && matchDiff && matchStatus) ? '' : 'none';
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
    lines.push(['Titre','Professeur','Cours','Chapitre','Difficulté','Statut'].map(csvEscape).join(','));
    rows.forEach(function (tr) {
      var tds = tr.querySelectorAll('td');
      var title = (tds[0] ? tds[0].innerText : '').replace(/\s+/g, ' ').trim();
      var author = (tds[1] ? tds[1].innerText : '').replace(/\s+/g, ' ').trim();
      var course = (tds[2] ? tds[2].innerText : '').replace(/\s+/g, ' ').trim();
      var chapter = (tds[3] ? tds[3].innerText : '').replace(/\s+/g, ' ').trim();
      var diff = (tds[4] ? tds[4].innerText : '').replace(/\s+/g, ' ').trim();
      var status = (tds[5] ? tds[5].innerText : '').replace(/\s+/g, ' ').trim();
      lines.push([title, author, course, chapter, diff, status].map(csvEscape).join(','));
    });
    var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'admin_quiz_history.csv';
    document.body.appendChild(a);
    a.click();
    a.remove();
    setTimeout(function () { URL.revokeObjectURL(url); }, 250);
  }

  input.addEventListener('input', apply);
  sel.addEventListener('change', apply);
  selSt.addEventListener('change', apply);
  sortSel.addEventListener('change', function () { sortRows(); apply(); });
  exportBtn.addEventListener('click', function () { apply(); exportCsv(); });

  sortRows();
})();
</script>
