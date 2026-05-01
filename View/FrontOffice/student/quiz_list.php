<?php
$studentSidebarActive = 'quiz';
$quizzes = $quizzes ?? [];

$rank = isset($rank) && is_array($rank) ? $rank : null;

$rankProgress = isset($rankProgress) && is_array($rankProgress) ? $rankProgress : null;
$rankSpark = isset($rankSpark) && is_array($rankSpark) ? $rankSpark : [];

$flags = isset($flags) && is_array($flags) ? $flags : [];
$filter = isset($filter) ? (string) $filter : '';

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
                        <?php if (!empty($rank)): ?>
                            <?php
                                $rp = is_array($rankProgress) ? $rankProgress : null;
                                $rpPct = (int) ($rp['pct'] ?? 0);
                                $rpToNext = (int) ($rp['to_next'] ?? 0);
                                $rpNext = (string) ($rp['next_label'] ?? 'Next');
                            ?>
                            <div class="pro-table-card" style="padding: 10px 12px; background: linear-gradient(135deg, rgba(88, 202, 255, 0.14), rgba(170, 106, 255, 0.12)); border: 1px solid rgba(120, 190, 255, 0.25); display:flex; align-items:center; gap:12px;">
                                <div style="width:34px;height:34px;border-radius:10px; background: rgba(12, 24, 45, 0.55); border: 1px solid rgba(120, 190, 255, 0.25); display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-trophy" style="color: rgba(170, 220, 255, 0.95);"></i>
                                </div>
                                <div style="line-height:1.1; min-width: 170px;">
                                    <div style="font-weight:900; letter-spacing:.2px;">
                                        <?= htmlspecialchars((string) ($rank['league'] ?? 'Bronze')) ?> <?= htmlspecialchars((string) ($rank['division'] ?? 'III')) ?>
                                    </div>
                                    <div style="opacity:.9; font-weight:800; font-size:.9rem;">Rating <?= (int) ($rank['rating'] ?? 1000) ?></div>
                                    <div style="margin-top:6px;">
                                        <div style="display:flex; justify-content:space-between; font-size:.78rem; font-weight:800; opacity:.92;">
                                            <span><?= htmlspecialchars($rpNext) ?></span>
                                            <span><?= (int) $rpPct ?>%</span>
                                        </div>
                                        <div style="margin-top:5px; width: 100%; height: 8px; border-radius: 999px; overflow:hidden; background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.12);">
                                            <div style="height:100%; width: <?= max(0, min(100, $rpPct)) ?>%; background: linear-gradient(90deg, rgba(96,165,250,.95), rgba(167,139,250,.95));"></div>
                                        </div>
                                        <div style="margin-top:5px; font-size:.78rem; opacity:.88; font-weight:800;">
                                            <?= $rpToNext > 0 ? '~' . (int) $rpToNext . ' pts' : 'palier proche' ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($rankSpark) && count($rankSpark) >= 2): ?>
                                    <?php
                                        $pts = [];
                                        $n = count($rankSpark);
                                        $w = 90;
                                        $h = 34;
                                        for ($i = 0; $i < $n; $i++) {
                                            $x = (int) round(($w - 2) * ($i / max(1, $n - 1))) + 1;
                                            $y = (int) round(($h - 2) * (1 - (max(0, min(100, (int) $rankSpark[$i])) / 100))) + 1;
                                            $pts[] = $x . ',' . $y;
                                        }
                                    ?>
                                    <svg width="<?= (int) $w ?>" height="<?= (int) $h ?>" viewBox="0 0 <?= (int) $w ?> <?= (int) $h ?>" style="display:block; opacity:.95;">
                                        <polyline points="<?= htmlspecialchars(implode(' ', $pts)) ?>" fill="none" stroke="rgba(170, 220, 255, 0.95)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <a href="<?= APP_ENTRY ?>?url=student/quiz-history" class="btn btn-outline">Historique</a>
                        <a href="<?= APP_ENTRY ?>?url=student/chapitres" class="btn btn-outline">Chapitres</a>
                    </div>
                </div>

                <div style="margin-top: 10px; display:flex; gap: 10px; flex-wrap: wrap;">
                    <a class="btn <?= $filter === '' ? 'btn-primary' : 'btn-outline' ?>" href="<?= APP_ENTRY ?>?url=student/quiz" style="padding:8px 12px;">Tous</a>
                    <a class="btn <?= $filter === 'favorites' ? 'btn-primary' : 'btn-outline' ?>" href="<?= APP_ENTRY ?>?url=student/quiz&filter=favorites" style="padding:8px 12px;">Favoris</a>
                    <a class="btn <?= $filter === 'redo' ? 'btn-primary' : 'btn-outline' ?>" href="<?= APP_ENTRY ?>?url=student/quiz&filter=redo" style="padding:8px 12px;">À refaire</a>
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
                                            $tagsTxt = (string) ($q['tags'] ?? '');
                                        ?>
                                        <?php
                                        $qid = (int) ($q['id'] ?? 0);
                                        $f = $flags[$qid] ?? ['favorite' => false, 'redo' => false];
                                        $isFav = !empty($f['favorite']);
                                        $isRedo = !empty($f['redo']);
                                    ?>
                                    <tr data-id="<?= (int) $qid ?>" data-title="<?= htmlspecialchars(mb_strtolower((string) ($q['title'] ?? ''))) ?>" data-course="<?= htmlspecialchars(mb_strtolower((string) ($q['course_title'] ?? ''))) ?>" data-chapter="<?= htmlspecialchars(mb_strtolower((string) ($q['chapter_title'] ?? ''))) ?>" data-difficulty="<?= htmlspecialchars($diff) ?>" data-tags="<?= htmlspecialchars(mb_strtolower($tagsTxt)) ?>" data-course-id="<?= (int) $cid ?>" data-favorite="<?= $isFav ? '1' : '0' ?>" data-redo="<?= $isRedo ? '1' : '0' ?>">
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
                                            <?php if (trim($tagsTxt) !== ''): ?>
                                                <?php foreach (array_slice(array_filter(array_map('trim', preg_split('/[;,]+/', $tagsTxt))), 0, 3) as $tg): ?>
                                                    <span class="pro-tag-chip"><?= htmlspecialchars($tg) ?></span>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="display:flex; align-items:center; gap:10px; justify-content:flex-end;">
                                                <a class="pro-icon-btn" href="<?= APP_ENTRY ?>?url=student/toggle-favorite-quiz/<?= (int) $qid ?>" title="Favori" aria-label="Favori" style="<?= $isFav ? 'color:#fbbf24;' : '' ?>">
                                                    <i class="bi <?= $isFav ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                                </a>
                                                <a class="pro-icon-btn" href="<?= APP_ENTRY ?>?url=student/toggle-redo-quiz/<?= (int) $qid ?>" title="À refaire" aria-label="À refaire" style="<?= $isRedo ? 'color:#60a5fa;' : '' ?>">
                                                    <i class="bi <?= $isRedo ? 'bi-arrow-repeat' : 'bi-arrow-repeat' ?>"></i>
                                                </a>
                                                <a class="btn btn-primary" style="white-space:nowrap;" href="<?= APP_ENTRY ?>?url=student/quiz/<?= (int) $qid ?>">Commencer</a>
                                            </div>
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
  var courseSel = document.getElementById('studentQuizCourse');
  var diffSel = document.getElementById('studentQuizDifficulty');
  var sortSel = document.getElementById('studentQuizSort');
  var exportBtn = document.getElementById('studentQuizExport');
  var table = document.getElementById('studentQuizTable');
  if (!input || !courseSel || !diffSel || !sortSel || !exportBtn || !table) return;

  var initialFilter = '<?= htmlspecialchars($filter, ENT_QUOTES) ?>';

  function norm(v) {
    return (v || '').toString().trim().toLowerCase();
  }

  function apply() {
    var q = norm(input.value);
    var d = norm(diffSel.value);
    var c = norm(courseSel.value);
    table.querySelectorAll('tbody tr').forEach(function (tr) {
      var title = norm(tr.getAttribute('data-title'));
      var course = norm(tr.getAttribute('data-course'));
      var chapter = norm(tr.getAttribute('data-chapter'));
      var diff = norm(tr.getAttribute('data-difficulty'));
      var tags = norm(tr.getAttribute('data-tags'));
      var courseId = norm(tr.getAttribute('data-course-id'));

      var matchText = !q || title.indexOf(q) !== -1 || course.indexOf(q) !== -1 || chapter.indexOf(q) !== -1 || tags.indexOf(q) !== -1;
      var matchDiff = !d || diff === d;
      var matchCourse = !c || courseId === c;

      var matchFlag = true;
      if (initialFilter === 'favorites') {
        matchFlag = tr.getAttribute('data-favorite') === '1';
      } else if (initialFilter === 'redo') {
        matchFlag = tr.getAttribute('data-redo') === '1';
      }

      tr.style.display = (matchText && matchDiff && matchCourse && matchFlag) ? '' : 'none';
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
  courseSel.addEventListener('change', apply);
  diffSel.addEventListener('change', apply);
  sortSel.addEventListener('change', function () { sortRows(); apply(); });
  exportBtn.addEventListener('click', function () { apply(); exportCsv(); });

  sortRows();
})();
</script>

