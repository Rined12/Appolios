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
<style>
/* Browse quiz list — same light shell as certificates / courses (no theme-quiz-pro on this route) */
.student-quiz-browse-page .student-quiz-list-page .pro-table-head h1 {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1e293b;
    margin: 0 0 0.35rem 0;
}
.student-quiz-browse-page .student-quiz-list-page .pro-table-head p {
    color: #64748b;
    font-size: 0.95rem;
    margin: 0;
    max-width: 42rem;
    line-height: 1.5;
}
.student-quiz-browse-page .student-quiz-list-page .pro-table-head {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
}
.student-quiz-browse-page .student-quiz-list-page .pro-table-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 0.6rem; }
.student-quiz-browse-page .student-quiz-list-page .student-quiz-rank-card {
    padding: 12px 14px;
    background: linear-gradient(135deg, #eff6ff, #faf5ff);
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
}
.student-quiz-browse-page .student-quiz-list-page .student-quiz-rank-card .rq-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.35);
}
.student-quiz-browse-page .student-quiz-list-page .student-quiz-rank-card .rq-icon i { color: #fff; font-size: 1.2rem; }
.student-quiz-browse-page .student-quiz-list-page .student-quiz-rank-card .rq-title { font-weight: 800; color: #1e293b; font-size: 0.95rem; }
.student-quiz-browse-page .student-quiz-list-page .student-quiz-rank-card .rq-sub { font-weight: 700; font-size: 0.85rem; color: #475569; margin-top: 2px; }
.student-quiz-browse-page .student-quiz-list-page .student-quiz-rank-card .rq-meta { font-size: 0.75rem; font-weight: 700; color: #64748b; }
.student-quiz-browse-page .student-quiz-list-page .student-quiz-rank-card .rq-bar-bg {
    margin-top: 6px;
    width: 100%;
    height: 8px;
    border-radius: 999px;
    overflow: hidden;
    background: #e2e8f0;
    border: 1px solid #cbd5e1;
}
.student-quiz-browse-page .student-quiz-list-page .student-quiz-rank-card .rq-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
}
.student-quiz-browse-page .student-quiz-list-page .btn-coach-pro {
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    color: #fff;
    border: none;
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35);
    font-weight: 800;
    padding: 10px 18px;
    border-radius: 10px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.student-quiz-browse-page .student-quiz-list-page .btn-coach-pro:hover { filter: brightness(1.05); color: #fff; }
.student-quiz-browse-page .student-quiz-list-page .btn-coach-pro-badge {
    background: #fff;
    color: #d97706;
    padding: 2px 6px;
    border-radius: 6px;
    font-size: 0.65rem;
    font-weight: 900;
    margin-left: 4px;
}
.student-quiz-browse-page .student-quiz-list-page .pro-table-actions .btn-outline {
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #475569;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    padding: 8px 14px;
}
.student-quiz-browse-page .student-quiz-list-page .pro-table-actions .btn-outline:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #1e293b;
}
.student-quiz-browse-page .student-quiz-list-page .student-quiz-filter-bar {
    margin-top: 12px;
    display: inline-flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 6px;
    background: #f1f5f9;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}
.student-quiz-browse-page .student-quiz-list-page .student-quiz-filter-bar .btn {
    border-radius: 10px;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid transparent;
}
.student-quiz-browse-page .student-quiz-list-page .student-quiz-filter-bar .btn-outline {
    background: transparent;
    color: #64748b;
    border-color: transparent;
}
.student-quiz-browse-page .student-quiz-list-page .student-quiz-filter-bar .btn-outline:hover { color: #1e293b; background: rgba(255,255,255,0.7); }
.student-quiz-browse-page .student-quiz-list-page .student-quiz-filter-bar .btn-primary {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #fff;
    border: none;
    box-shadow: 0 2px 10px rgba(37, 99, 235, 0.25);
}
.student-quiz-browse-page .student-quiz-list-page .pro-table-card {
    background: #fff;
    border: 1px solid #eef2f6;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(15, 23, 42, 0.06);
    overflow: hidden;
}
.student-quiz-browse-page .student-quiz-list-page .pro-table-toolbar {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 14px 18px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.student-quiz-browse-page .student-quiz-list-page .pro-table-toolbar-left { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
.student-quiz-browse-page .student-quiz-list-page .pro-table-toolbar-right .btn-outline {
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #475569;
    border-radius: 10px;
    font-weight: 600;
}
.student-quiz-browse-page .student-quiz-list-page .pro-search { display: inline-flex; align-items: center; gap: 8px; }
.student-quiz-browse-page .student-quiz-list-page .pro-search input,
.student-quiz-browse-page .student-quiz-list-page .pro-select {
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #1e293b;
    border-radius: 10px;
    padding: 9px 14px;
    font-size: 0.9rem;
}
.student-quiz-browse-page .student-quiz-list-page .pro-search input:focus,
.student-quiz-browse-page .student-quiz-list-page .pro-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    outline: none;
}
.student-quiz-browse-page .student-quiz-list-page .pro-search i { color: #94a3b8; }
.student-quiz-browse-page .student-quiz-list-page .pro-table th {
    background: #f8fafc;
    color: #64748b;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: 0.72rem;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 16px;
}
.student-quiz-browse-page .student-quiz-list-page .pro-table td {
    border-bottom: 1px solid #f1f5f9;
    padding: 16px;
    color: #334155;
    font-size: 0.92rem;
    vertical-align: middle;
}
.student-quiz-browse-page .student-quiz-list-page .pro-table tbody tr:hover { background: #fafbfc; }
.student-quiz-browse-page .student-quiz-list-page .pro-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #3b82f6;
    margin-right: 8px;
    vertical-align: middle;
}
.student-quiz-browse-page .student-quiz-list-page .pro-badge {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #475569;
    padding: 4px 10px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.78rem;
}
.student-quiz-browse-page .student-quiz-list-page .pro-badge--beginner { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
.student-quiz-browse-page .student-quiz-list-page .pro-badge--intermediate { background: #fffbeb; color: #b45309; border-color: #fde68a; }
.student-quiz-browse-page .student-quiz-list-page .pro-badge--advanced { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
.student-quiz-browse-page .student-quiz-list-page .pro-tag-chip {
    background: #f5f3ff;
    color: #6d28d9;
    border: 1px solid #ddd6fe;
    border-radius: 8px;
    padding: 2px 8px;
    font-size: 0.75rem;
    font-weight: 600;
}
.student-quiz-browse-page .student-quiz-list-page .pro-table .btn-primary {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    border: none;
    border-radius: 10px;
    padding: 8px 16px;
    font-weight: 700;
    color: #fff;
    text-decoration: none;
    box-shadow: 0 2px 10px rgba(37, 99, 235, 0.25);
}
.student-quiz-browse-page .student-quiz-list-page .pro-table .btn-primary:hover { filter: brightness(1.05); color: #fff; }
.student-quiz-browse-page .student-quiz-list-page .pro-icon-btn {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    border-radius: 10px;
    color: #475569;
    text-decoration: none;
    border: 1px solid #e2e8f0;
}
.student-quiz-browse-page .student-quiz-list-page .pro-icon-btn:hover { background: #e2e8f0; color: #1e293b; }
.student-quiz-browse-page .student-quiz-list-page .student-quiz-empty {
    padding: 1.25rem 1.35rem;
    color: #64748b;
    line-height: 1.55;
    margin: 0;
}
</style>
<div class="dashboard student-events-page student-quiz-browse-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page student-quiz-list-page">
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
                            <div class="student-quiz-rank-card">
                                <div class="rq-icon" aria-hidden="true">
                                    <i class="bi bi-trophy"></i>
                                </div>
                                <div style="line-height:1.2; min-width: 170px;">
                                    <div class="rq-title">
                                        <?= htmlspecialchars((string) ($rank['league'] ?? 'Bronze')) ?> <?= htmlspecialchars((string) ($rank['division'] ?? 'III')) ?>
                                    </div>
                                    <div class="rq-sub">Rating <?= (int) ($rank['rating'] ?? 1000) ?></div>
                                    <div style="margin-top:6px;">
                                        <div class="rq-meta" style="display:flex; justify-content:space-between;">
                                            <span><?= htmlspecialchars($rpNext) ?></span>
                                            <span><?= (int) $rpPct ?>%</span>
                                        </div>
                                        <div class="rq-bar-bg">
                                            <div class="rq-bar-fill" style="width: <?= max(0, min(100, $rpPct)) ?>%;"></div>
                                        </div>
                                        <div class="rq-meta" style="margin-top:5px;">
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
                                    <svg width="<?= (int) $w ?>" height="<?= (int) $h ?>" viewBox="0 0 <?= (int) $w ?> <?= (int) $h ?>" style="display:block;">
                                        <polyline points="<?= htmlspecialchars(implode(' ', $pts)) ?>" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <a href="<?= APP_ENTRY ?>?url=student/coach&return=student-quiz/quiz" class="btn btn-coach-pro">
                            <i class="bi bi-stars" aria-hidden="true"></i>
                            Coach
                            <span class="btn-coach-pro-badge">PRO</span>
                        </a>
                        <a href="<?= APP_ENTRY ?>?url=student-quiz/quiz-history" class="btn btn-outline">Historique</a>
                        <a href="<?= APP_ENTRY ?>?url=student/chapitres" class="btn btn-outline">Chapitres</a>
                    </div>
                </div>

                <div class="student-quiz-filter-bar">
                    <a class="btn <?= $filter === '' ? 'btn-primary' : 'btn-outline' ?>" href="<?= APP_ENTRY ?>?url=student-quiz/quiz" style="padding:8px 12px;">Tous</a>
                    <a class="btn <?= $filter === 'favorites' ? 'btn-primary' : 'btn-outline' ?>" href="<?= APP_ENTRY ?>?url=student-quiz/quiz&filter=favorites" style="padding:8px 12px;">Favoris</a>
                    <a class="btn <?= $filter === 'redo' ? 'btn-primary' : 'btn-outline' ?>" href="<?= APP_ENTRY ?>?url=student-quiz/quiz&filter=redo" style="padding:8px 12px;">À refaire</a>
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
                                                <a class="pro-icon-btn" href="<?= APP_ENTRY ?>?url=student-quiz/toggle-favorite-quiz/<?= (int) $qid ?>" title="Favori" aria-label="Favori" style="<?= $isFav ? 'color:#fbbf24;' : '' ?>">
                                                    <i class="bi <?= $isFav ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                                </a>
                                                <a class="pro-icon-btn" href="<?= APP_ENTRY ?>?url=student-quiz/toggle-redo-quiz/<?= (int) $qid ?>" title="À refaire" aria-label="À refaire" style="<?= $isRedo ? 'color:#60a5fa;' : '' ?>">
                                                    <i class="bi <?= $isRedo ? 'bi-arrow-repeat' : 'bi-arrow-repeat' ?>"></i>
                                                </a>
                                                <a class="btn btn-primary" style="white-space:nowrap;" href="<?= APP_ENTRY ?>?url=student-quiz/quiz/<?= (int) $qid ?>">Commencer</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="pro-table-card">
                        <p class="student-quiz-empty">Aucun quiz pour l’instant. Inscrivez-vous à un cours qui propose des quiz, ou revenez plus tard.</p>
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
