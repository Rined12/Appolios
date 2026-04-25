<?php
$studentSidebarActive = 'quiz';
$quizzes = $quizzes ?? [];

$courses = [];
foreach ($quizzes as $q) {
    $cid = (int) ($q['course_id'] ?? 0);
    $ct = (string) ($q['course_title'] ?? '');
    if ($cid > 0) {
        $courses[$cid] = $ct;
    }
}
ksort($courses, SORT_NUMERIC);
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Mes quiz</h1>
                        <p>Quiz des cours où vous êtes inscrit. Chaque tentative est enregistrée dans votre historique.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=student/quiz-history" class="btn btn-outline">Historique</a>
                        <a href="<?= APP_ENTRY ?>?url=student/chapitres" class="btn btn-outline">Chapitres</a>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:16px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <?php if (!empty($quizzes)): ?>
                    <div class="pro-table-card">
                        <div class="pro-table-toolbar">
                            <div class="pro-table-toolbar-left">
                                <div class="pro-search">
                                    <i class="bi bi-search"></i>
                                    <input id="studentQuizSearch" type="text" placeholder="Rechercher un quiz..." autocomplete="off">
                                </div>
                                <select id="studentQuizCourse" class="pro-select" aria-label="Filtrer par cours">
                                    <option value="">Tous les cours</option>
                                    <?php foreach ($courses as $cid => $ct): ?>
                                        <option value="<?= (int) $cid ?>"><?= htmlspecialchars($ct) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select id="studentQuizDifficulty" class="pro-select" aria-label="Filtrer par niveau">
                                    <option value="">Tous les niveaux</option>
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                </select>
                                <select id="studentQuizSort" class="pro-select" aria-label="Trier">
                                    <option value="title">Trier : Quiz</option>
                                    <option value="course">Trier : Cours</option>
                                    <option value="chapter">Trier : Chapitre</option>
                                    <option value="difficulty">Trier : Difficulté</option>
                                </select>
                            </div>
                            <div class="pro-table-toolbar-right">
                                <button type="button" class="btn btn-outline" id="studentQuizExport">Exporter CSV</button>
                            </div>
                        </div>
                        <div class="pro-table-wrap">
                            <table class="pro-table" id="studentQuizTable">
                                <thead>
                                    <tr>
                                        <th>Quiz</th>
                                        <th>Cours</th>
                                        <th>Chapitre</th>
                                        <th>Infos</th>
                                        <th style="width:1%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($quizzes as $q): ?>
                                        <?php
                                            $diff = (string) ($q['difficulty'] ?? 'beginner');
                                            $diffClass = 'pro-badge';
                                            if ($diff === 'beginner') { $diffClass .= ' pro-badge--beginner'; }
                                            elseif ($diff === 'intermediate') { $diffClass .= ' pro-badge--intermediate'; }
                                            elseif ($diff === 'advanced') { $diffClass .= ' pro-badge--advanced'; }
                                            $cid = (int) ($q['course_id'] ?? 0);
                                        ?>
                                        <tr data-id="<?= (int) ($q['id'] ?? 0) ?>" data-title="<?= htmlspecialchars(mb_strtolower((string) ($q['title'] ?? ''))) ?>" data-course="<?= htmlspecialchars(mb_strtolower((string) ($q['course_title'] ?? ''))) ?>" data-chapter="<?= htmlspecialchars(mb_strtolower((string) ($q['chapter_title'] ?? ''))) ?>" data-difficulty="<?= htmlspecialchars($diff) ?>" data-course-id="<?= (int) $cid ?>">
                                            <td>
                                                <span class="pro-dot"></span>
                                                <?= htmlspecialchars((string) ($q['title'] ?? '')) ?>
                                            </td>
                                            <td><?= htmlspecialchars((string) ($q['course_title'] ?? '')) ?></td>
                                            <td><?= htmlspecialchars((string) ($q['chapter_title'] ?? '')) ?></td>
                                            <td>
                                                <span class="pro-badge"><?= (int) ($q['question_count'] ?? 0) ?> question(s)</span>
                                                <span class="<?= $diffClass ?>"><?= htmlspecialchars(difficulty_label_fr($diff)) ?></span>
                                                <?php if (!empty($q['time_limit_sec'])): ?>
                                                    <span class="pro-badge"><?= (int) $q['time_limit_sec'] ?> s max</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a class="btn btn-primary" style="white-space:nowrap;" href="<?= APP_ENTRY ?>?url=student/quiz/<?= (int) $q['id'] ?>">Commencer</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="pro-table-card" style="padding: 1.2rem;">
                        <p style="margin:0;color: rgba(226, 232, 240, 0.7);">Aucun quiz pour l’instant. Inscrivez-vous à un cours qui propose des quiz, ou revenez plus tard.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
  var input = document.getElementById('studentQuizSearch');
  var selCourse = document.getElementById('studentQuizCourse');
  var selDiff = document.getElementById('studentQuizDifficulty');
  var sortSel = document.getElementById('studentQuizSort');
  var exportBtn = document.getElementById('studentQuizExport');
  var table = document.getElementById('studentQuizTable');
  if (!input || !selCourse || !selDiff || !sortSel || !exportBtn || !table) return;

  function norm(v) {
    return (v || '').toString().trim().toLowerCase();
  }

  function apply() {
    var q = norm(input.value);
    var d = norm(selDiff.value);
    var c = norm(selCourse.value);
    table.querySelectorAll('tbody tr').forEach(function (tr) {
      var title = norm(tr.getAttribute('data-title'));
      var course = norm(tr.getAttribute('data-course'));
      var chapter = norm(tr.getAttribute('data-chapter'));
      var diff = norm(tr.getAttribute('data-difficulty'));
      var courseId = norm(tr.getAttribute('data-course-id'));

      var matchText = !q || title.indexOf(q) !== -1 || course.indexOf(q) !== -1 || chapter.indexOf(q) !== -1;
      var matchDiff = !d || diff === d;
      var matchCourse = !c || courseId === c;
      tr.style.display = (matchText && matchDiff && matchCourse) ? '' : 'none';
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
    lines.push(['Quiz','Cours','Chapitre','Questions','Difficulté','Temps (s)'].map(csvEscape).join(','));
    rows.forEach(function (tr) {
      var tds = tr.querySelectorAll('td');
      var quiz = (tds[0] ? tds[0].innerText : '').replace(/\s+/g, ' ').trim();
      var course = (tds[1] ? tds[1].innerText : '').replace(/\s+/g, ' ').trim();
      var chapter = (tds[2] ? tds[2].innerText : '').replace(/\s+/g, ' ').trim();
      var infos = (tds[3] ? tds[3].innerText : '').replace(/\s+/g, ' ').trim();
      var qs = '';
      var diff = '';
      var time = '';
      try {
        var mQ = infos.match(/(\d+)\s*question/);
        if (mQ) qs = mQ[1];
        if (/Débutant/i.test(infos)) diff = 'Débutant';
        else if (/Interm/i.test(infos)) diff = 'Intermédiaire';
        else if (/Avanc/i.test(infos)) diff = 'Avancé';
        var mT = infos.match(/(\d+)\s*s\s*max/);
        if (mT) time = mT[1];
      } catch (e) {}
      lines.push([quiz, course, chapter, qs, diff, time].map(csvEscape).join(','));
    });
    var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'student_quizzes.csv';
    document.body.appendChild(a);
    a.click();
    a.remove();
    setTimeout(function () { URL.revokeObjectURL(url); }, 250);
  }

  input.addEventListener('input', apply);
  selCourse.addEventListener('change', apply);
  selDiff.addEventListener('change', apply);
  sortSel.addEventListener('change', function () { sortRows(); apply(); });
  exportBtn.addEventListener('click', function () { apply(); exportCsv(); });

  sortRows();
})();
</script>

