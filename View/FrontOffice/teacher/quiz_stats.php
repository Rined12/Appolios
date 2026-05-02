<?php
$teacherSidebarActive = 'quiz';
$kpis = $kpis ?? [
    'total_quizzes' => 0,
    'total_attempts' => 0,
    'overall_avg' => 0,
    'coverage_pct' => 0,
    'engagement_pct' => 0,
    'attempts_per_quiz' => 0,
];
$rows = $rows ?? [];
$series = $series ?? [];
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Statistiques quiz</h1>
                        <p>Vue globale + détails par quiz (tentatives, moyenne, meilleur score).</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=teacher/quiz" class="btn btn-outline">Retour aux quiz</a>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <?php
                    $totalQuizzes = (int) ($kpis['total_quizzes'] ?? 0);
                    $totalAttempts = (int) ($kpis['total_attempts'] ?? 0);
                    $overallAvg = (float) ($kpis['overall_avg'] ?? 0);
                    $coveragePct = (float) ($kpis['coverage_pct'] ?? 0);
                    $engagementPct = (float) ($kpis['engagement_pct'] ?? 0);
                    $attemptsPerQuiz = (float) ($kpis['attempts_per_quiz'] ?? 0);

                    $ringC = 2 * 3.141592653589793 * 18;
                    $ringOffset = function ($pct) use ($ringC) {
                        $p = (float) $pct;
                        if ($p < 0) $p = 0;
                        if ($p > 100) $p = 100;
                        return $ringC * (1 - ($p / 100));
                    };
                ?>

                <div class="pro-table-card pro-kpi-panel">
                    <div class="pro-kpi-grid pro-kpi-grid--blocks" style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;">
                        <div class="pro-kpi-card pro-kpi-card--radial">
                            <div class="pro-kpi-main">
                                <div class="pro-kpi-left">
                                    <div class="pro-kpi-top">
                                        <div class="pro-kpi-label">Engagement</div>
                                        <div class="pro-kpi-icon"><i class="bi bi-lightning-charge"></i></div>
                                    </div>
                                    <div class="pro-kpi-value"><?= htmlspecialchars((string) $attemptsPerQuiz) ?></div>
                                    <div class="pro-kpi-sub">Tentatives / quiz (normalisé)</div>
                                </div>
                                <div class="pro-kpi-right" aria-hidden="true">
                                    <svg class="pro-kpi-ring" viewBox="0 0 44 44">
                                        <circle class="pro-kpi-ring-bg" cx="22" cy="22" r="18" fill="none" stroke="rgba(148,163,184,0.18)" stroke-width="4" />
                                        <circle class="pro-kpi-ring-fg" cx="22" cy="22" r="18" fill="none" stroke="rgba(96,165,250,0.92)" stroke-width="4" stroke-linecap="round" stroke-dasharray="<?= htmlspecialchars((string) $ringC) ?>" stroke-dashoffset="<?= htmlspecialchars((string) $ringOffset($engagementPct)) ?>" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="pro-kpi-card pro-kpi-card--radial">
                            <div class="pro-kpi-main">
                                <div class="pro-kpi-left">
                                    <div class="pro-kpi-top">
                                        <div class="pro-kpi-label">Moyenne</div>
                                        <div class="pro-kpi-icon"><i class="bi bi-graph-up"></i></div>
                                    </div>
                                    <div class="pro-kpi-value"><?= htmlspecialchars((string) $overallAvg) ?>%</div>
                                    <div class="pro-kpi-sub">Moyenne pondérée (toutes tentatives)</div>
                                </div>
                                <div class="pro-kpi-right" aria-hidden="true">
                                    <svg class="pro-kpi-ring" viewBox="0 0 44 44">
                                        <circle class="pro-kpi-ring-bg" cx="22" cy="22" r="18" fill="none" stroke="rgba(148,163,184,0.18)" stroke-width="4" />
                                        <circle class="pro-kpi-ring-fg" cx="22" cy="22" r="18" fill="none" stroke="rgba(96,165,250,0.92)" stroke-width="4" stroke-linecap="round" stroke-dasharray="<?= htmlspecialchars((string) $ringC) ?>" stroke-dashoffset="<?= htmlspecialchars((string) $ringOffset($overallAvg)) ?>" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="pro-kpi-card pro-kpi-card--radial">
                            <div class="pro-kpi-main">
                                <div class="pro-kpi-left">
                                    <div class="pro-kpi-top">
                                        <div class="pro-kpi-label">Couverture</div>
                                        <div class="pro-kpi-icon"><i class="bi bi-ui-checks"></i></div>
                                    </div>
                                    <div class="pro-kpi-value"><?= htmlspecialchars((string) round($coveragePct, 1)) ?>%</div>
                                    <div class="pro-kpi-sub">Quiz joués / total</div>
                                </div>
                                <div class="pro-kpi-right" aria-hidden="true">
                                    <svg class="pro-kpi-ring" viewBox="0 0 44 44">
                                        <circle class="pro-kpi-ring-bg" cx="22" cy="22" r="18" fill="none" stroke="rgba(148,163,184,0.18)" stroke-width="4" />
                                        <circle class="pro-kpi-ring-fg" cx="22" cy="22" r="18" fill="none" stroke="rgba(96,165,250,0.92)" stroke-width="4" stroke-linecap="round" stroke-dasharray="<?= htmlspecialchars((string) $ringC) ?>" stroke-dashoffset="<?= htmlspecialchars((string) $ringOffset($coveragePct)) ?>" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="pro-kpi-card pro-kpi-card--radial">
                            <div class="pro-kpi-main">
                                <div class="pro-kpi-left">
                                    <div class="pro-kpi-top">
                                        <div class="pro-kpi-label">Tentatives</div>
                                        <div class="pro-kpi-icon"><i class="bi bi-collection"></i></div>
                                    </div>
                                    <div class="pro-kpi-value"><?= (int) $totalAttempts ?></div>
                                    <div class="pro-kpi-sub"><?= (int) $totalQuizzes ?> quiz au total</div>
                                </div>
                                <div class="pro-kpi-right" aria-hidden="true">
                                    <svg class="pro-kpi-ring" viewBox="0 0 44 44">
                                        <circle class="pro-kpi-ring-bg" cx="22" cy="22" r="18" fill="none" stroke="rgba(148,163,184,0.18)" stroke-width="4" />
                                        <circle class="pro-kpi-ring-fg" cx="22" cy="22" r="18" fill="none" stroke="rgba(96,165,250,0.92)" stroke-width="4" stroke-linecap="round" stroke-dasharray="<?= htmlspecialchars((string) $ringC) ?>" stroke-dashoffset="<?= htmlspecialchars((string) $ringOffset($engagementPct)) ?>" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pro-table-card">
                    <div class="pro-table-toolbar">
                        <div class="pro-table-toolbar-left">
                            <div class="pro-search">
                                <i class="bi bi-search"></i>
                                <input id="teacherQuizStatsSearch" type="text" placeholder="Rechercher (quiz / cours / chapitre)..." autocomplete="off">
                            </div>
                            <select id="teacherQuizStatsDifficulty" class="pro-select" aria-label="Filtrer par niveau">
                                <option value="">Tous les niveaux</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                            <select id="teacherQuizStatsSort" class="pro-select" aria-label="Trier">
                                <option value="attempts">Trier : Tentatives</option>
                                <option value="avg">Trier : Moyenne %</option>
                                <option value="best">Trier : Meilleur %</option>
                                <option value="title">Trier : Titre</option>
                            </select>
                        </div>
                        <div class="pro-table-toolbar-right">
                            <button type="button" class="btn btn-outline" id="teacherQuizStatsExport">Exporter CSV</button>
                        </div>
                    </div>

                    <div class="pro-table-wrap">
                        <table class="pro-table" id="teacherQuizStatsTable">
                            <thead>
                                <tr>
                                    <th>Quiz</th>
                                    <th style="width:1%;">Détails</th>
                                    <th>Courbe</th>
                                    <th>Cours / Chapitre</th>
                                    <th>Difficulté</th>
                                    <th>Tentatives</th>
                                    <th>Moyenne</th>
                                    <th>Meilleur</th>
                                    <th>Dernière tentative</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($rows)): foreach ($rows as $r): ?>
                                    <?php
                                        $diff = (string) ($r['difficulty'] ?? 'beginner');
                                        $diffClass = 'pro-badge';
                                        if ($diff === 'beginner') { $diffClass .= ' pro-badge--beginner'; }
                                        elseif ($diff === 'intermediate') { $diffClass .= ' pro-badge--intermediate'; }
                                        elseif ($diff === 'advanced') { $diffClass .= ' pro-badge--advanced'; }

                                        $attempts = (int) ($r['attempts_count'] ?? 0);
                                        $avg = (float) ($r['avg_percentage'] ?? 0);
                                        $best = (int) ($r['best_percentage'] ?? 0);
                                        $last = (string) ($r['last_attempt_at'] ?? '');
                                        $title = (string) ($r['title'] ?? '');
                                        $course = (string) ($r['course_title'] ?? '');
                                        $chapter = (string) ($r['chapter_title'] ?? '');
                                    ?>
                                    <tr
                                        data-id="<?= (int) ($r['id'] ?? 0) ?>"
                                        data-title="<?= htmlspecialchars(mb_strtolower($title)) ?>"
                                        data-course="<?= htmlspecialchars(mb_strtolower($course)) ?>"
                                        data-chapter="<?= htmlspecialchars(mb_strtolower($chapter)) ?>"
                                        data-difficulty="<?= htmlspecialchars($diff) ?>"
                                        data-attempts="<?= (int) $attempts ?>"
                                        data-avg="<?= htmlspecialchars((string) $avg) ?>"
                                        data-best="<?= (int) $best ?>">
                                        <td>
                                            <div class="pro-cell-title">
                                                <strong><?= htmlspecialchars($title !== '' ? $title : ('Quiz #' . (int) ($r['id'] ?? 0))) ?></strong>
                                                <span class="pro-cell-sub">#<?= (int) ($r['id'] ?? 0) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="pro-quiz-inspect" data-inspect="1" aria-label="Détails">
                                                <i class="bi bi-gear"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <svg class="pro-spark" viewBox="0 0 120 36" data-spark="1" aria-hidden="true">
                                                <path class="pro-spark-area" d=""></path>
                                                <path class="pro-spark-line" d=""></path>
                                            </svg>
                                        </td>
                                        <td>
                                            <div class="pro-cell-title">
                                                <strong><?= htmlspecialchars($course) ?></strong>
                                                <span class="pro-cell-sub"><?= htmlspecialchars($chapter) ?></span>
                                            </div>
                                        </td>
                                        <td><span class="<?= $diffClass ?>"><?= htmlspecialchars(difficulty_label_fr($diff)) ?></span></td>
                                        <td><span class="pro-badge"><?= (int) $attempts ?></span></td>
                                        <td>
                                            <span class="pro-badge" style="background: rgba(59,130,246,0.14); border-color: rgba(59,130,246,0.22); color: rgba(191,219,254,0.95);">
                                                <?= htmlspecialchars(number_format($avg, 1)) ?>%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="pro-badge" style="background: rgba(34,197,94,0.14); border-color: rgba(34,197,94,0.22); color: rgba(134,239,172,0.95);">
                                                <?= (int) $best ?>%
                                            </span>
                                            <?php if (!empty($r['best_total'])): ?>
                                                <span class="pro-cell-sub" style="display:block; margin-top:4px;">Best: <?= (int) ($r['best_score'] ?? 0) ?>/<?= (int) ($r['best_total'] ?? 0) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $last !== '' ? htmlspecialchars($last) : '—' ?></td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="9">Aucune statistique pour le moment.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="pro-modal" id="quizInspectModal" aria-hidden="true">
    <div class="pro-modal-card" role="dialog" aria-modal="true" aria-labelledby="quizInspectTitle">
        <div class="pro-modal-head">
            <div>
                <h2 class="pro-modal-title" id="quizInspectTitle">Détails</h2>
                <div class="pro-modal-sub" id="quizInspectSub">—</div>
            </div>
            <button type="button" class="pro-modal-close" id="quizInspectClose" aria-label="Fermer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="pro-modal-body">
            <svg class="pro-geo" viewBox="0 0 600 600" aria-hidden="true">
                <defs>
                    <linearGradient id="g1t" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0" stop-color="rgba(59,130,246,0.55)" />
                        <stop offset="1" stop-color="rgba(99,102,241,0.10)" />
                    </linearGradient>
                </defs>
                <g fill="none" stroke="url(#g1t)" stroke-width="2">
                    <path d="M120 90 L420 60 L520 240 L380 480 L140 520 L70 310 Z" opacity="0.7" />
                    <path d="M170 140 L390 120 L470 260 L360 430 L170 460 L115 300 Z" opacity="0.5" />
                    <path d="M210 190 L360 175 L420 275 L340 390 L210 410 L175 305 Z" opacity="0.35" />
                </g>
            </svg>
            <div class="pro-gear pro-gear--a" aria-hidden="true"></div>
            <div class="pro-gear pro-gear--b" aria-hidden="true"></div>
            <div class="pro-gear pro-gear--c" aria-hidden="true"></div>

            <div class="pro-kpi-grid" style="margin-top: 0;">
                <div class="pro-kpi-card">
                    <div class="pro-kpi-top">
                        <div class="pro-kpi-label">Tentatives</div>
                        <div class="pro-kpi-icon"><i class="bi bi-lightning-charge"></i></div>
                    </div>
                    <div class="pro-kpi-value" id="qiAttempts">0</div>
                    <div class="pro-kpi-sub">Nombre de tentatives</div>
                </div>
                <div class="pro-kpi-card">
                    <div class="pro-kpi-top">
                        <div class="pro-kpi-label">Moyenne</div>
                        <div class="pro-kpi-icon"><i class="bi bi-graph-up"></i></div>
                    </div>
                    <div class="pro-kpi-value" id="qiAvg">0%</div>
                    <div class="pro-kpi-sub">Moyenne des résultats</div>
                </div>
                <div class="pro-kpi-card">
                    <div class="pro-kpi-top">
                        <div class="pro-kpi-label">Meilleur</div>
                        <div class="pro-kpi-icon"><i class="bi bi-trophy"></i></div>
                    </div>
                    <div class="pro-kpi-value" id="qiBest">0%</div>
                    <div class="pro-kpi-sub">Meilleure performance</div>
                </div>
                <div class="pro-kpi-card">
                    <div class="pro-kpi-top">
                        <div class="pro-kpi-label">Dernière tentative</div>
                        <div class="pro-kpi-icon"><i class="bi bi-clock"></i></div>
                    </div>
                    <div class="pro-kpi-value" id="qiLast">—</div>
                    <div class="pro-kpi-sub">Date du dernier passage</div>
                </div>
            </div>

            <div class="pro-chart" aria-label="Courbe" role="img">
                <svg viewBox="0 0 900 220" preserveAspectRatio="none">
                    <g class="grid">
                        <line x1="0" y1="20" x2="900" y2="20"></line>
                        <line x1="0" y1="70" x2="900" y2="70"></line>
                        <line x1="0" y1="120" x2="900" y2="120"></line>
                        <line x1="0" y1="170" x2="900" y2="170"></line>
                    </g>
                    <line id="qiHoverLine" x1="0" y1="0" x2="0" y2="220" stroke="rgba(148,163,184,0.22)" stroke-width="1" opacity="0"></line>
                    <path class="area" id="qiChartArea" d=""></path>
                    <path class="line" id="qiChartLine" d=""></path>
                    <g id="qiChartDots"></g>
                </svg>
                <div class="pro-chart-tooltip" id="qiChartTip" aria-hidden="true">
                    <strong id="qiTipVal">0%</strong>
                    <span id="qiTipMeta">—</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
  var input = document.getElementById('teacherQuizStatsSearch');
  var sel = document.getElementById('teacherQuizStatsDifficulty');
  var sortSel = document.getElementById('teacherQuizStatsSort');
  var exportBtn = document.getElementById('teacherQuizStatsExport');
  var table = document.getElementById('teacherQuizStatsTable');
  if (!input || !sel || !sortSel || !exportBtn || !table) return;

  var modal = document.getElementById('quizInspectModal');
  var closeBtn = document.getElementById('quizInspectClose');
  var titleEl = document.getElementById('quizInspectTitle');
  var subEl = document.getElementById('quizInspectSub');
  var qiAttempts = document.getElementById('qiAttempts');
  var qiAvg = document.getElementById('qiAvg');
  var qiBest = document.getElementById('qiBest');
  var qiLast = document.getElementById('qiLast');
  var qiChartLine = document.getElementById('qiChartLine');
  var qiChartArea = document.getElementById('qiChartArea');
  var qiChartDots = document.getElementById('qiChartDots');
  var qiChartTip = document.getElementById('qiChartTip');
  var qiTipVal = document.getElementById('qiTipVal');
  var qiTipMeta = document.getElementById('qiTipMeta');
  var qiHoverLine = document.getElementById('qiHoverLine');

  var SERIES = <?php echo json_encode($series, JSON_UNESCAPED_UNICODE); ?>;

  function norm(v) { return (v || '').toString().trim().toLowerCase(); }

  function buildPath(points, w, h) {
    if (!points || points.length === 0) return '';
    var min = 0;
    var max = 100;
    var dx = points.length > 1 ? (w / (points.length - 1)) : 0;
    var d = '';
    for (var i = 0; i < points.length; i++) {
      var x = dx * i;
      var v = points[i];
      var y = h - ((v - min) / (max - min)) * h;
      if (i === 0) d += 'M ' + x.toFixed(2) + ' ' + y.toFixed(2);
      else d += ' L ' + x.toFixed(2) + ' ' + y.toFixed(2);
    }
    return d;
  }

  function buildArea(lineD, w, h) {
    if (!lineD) return '';
    return lineD + ' L ' + w.toFixed(2) + ' ' + h.toFixed(2) + ' L 0 ' + h.toFixed(2) + ' Z';
  }

  function seriesPointsForQuizId(id) {
    var k = String(id || '');
    var arr = (SERIES && SERIES[k]) ? SERIES[k] : [];
    return arr.map(function (p) {
      return { t: String(p.t || ''), v: Math.max(0, Math.min(100, parseFloat(p.p || 0))) };
    });
  }

  function renderSparkForRow(tr) {
    if (!tr) return;
    var id = tr.getAttribute('data-id') || '';
    var pts = seriesPointsForQuizId(id);
    var values = pts.map(function (x) { return x.v; });
    if (!values || values.length === 0) values = [0];
    var svg = tr.querySelector('svg[data-spark="1"]');
    if (!svg) return;
    var line = svg.querySelector('.pro-spark-line');
    var area = svg.querySelector('.pro-spark-area');
    var w = 120;
    var h = 36;
    var lineD = buildPath(values, w, h);
    if (line) line.setAttribute('d', lineD);
    if (area) area.setAttribute('d', buildArea(lineD, w, h));
  }

  function renderAllSparks() {
    Array.from(table.querySelectorAll('tbody tr')).forEach(renderSparkForRow);
  }

  function renderModalChart(id) {
    if (!qiChartLine || !qiChartArea) return;
    var pts = seriesPointsForQuizId(id);
    var values = pts.map(function (x) { return x.v; });
    if (!values || values.length === 0) {
      qiChartLine.setAttribute('d', '');
      qiChartArea.setAttribute('d', '');
      if (qiChartDots) qiChartDots.innerHTML = '';
      return;
    }
    var w = 900;
    var h = 220;
    var lineD = buildPath(values, w, h);
    qiChartLine.setAttribute('d', lineD);
    qiChartLine.classList.remove('is-animate');
    void qiChartLine.getBoundingClientRect();
    qiChartLine.classList.add('is-animate');
    qiChartArea.setAttribute('d', buildArea(lineD, w, h));

    if (qiChartDots) {
      var dx = values.length > 1 ? (w / (values.length - 1)) : 0;
      var dots = '';
      for (var i = 0; i < values.length; i++) {
        var x = dx * i;
        var y = h - (values[i] / 100) * h;
        dots += '<circle class="dot" cx="' + x.toFixed(2) + '" cy="' + y.toFixed(2) + '" r="4"></circle>';
      }
      qiChartDots.innerHTML = dots;
      qiChartDots.setAttribute('data-qid', String(id || ''));
    }

    if (qiHoverLine) {
      qiHoverLine.setAttribute('x1', '0');
      qiHoverLine.setAttribute('x2', '0');
      qiHoverLine.setAttribute('opacity', '0');
    }
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

  function sortRows() {
    var key = norm(sortSel.value) || 'attempts';
    var rows = Array.from(table.querySelectorAll('tbody tr'));
    rows.sort(function (a, b) {
      if (key === 'attempts' || key === 'best') {
        var an = parseInt(a.getAttribute('data-' + key) || '0', 10);
        var bn = parseInt(b.getAttribute('data-' + key) || '0', 10);
        return bn - an;
      }
      if (key === 'avg') {
        var af = parseFloat(a.getAttribute('data-avg') || '0');
        var bf = parseFloat(b.getAttribute('data-avg') || '0');
        return bf - af;
      }
      var av = norm(a.getAttribute('data-' + key));
      var bv = norm(b.getAttribute('data-' + key));
      if (av < bv) return -1;
      if (av > bv) return 1;
      return 0;
    });
    var tbody = table.querySelector('tbody');
    rows.forEach(function (tr) { tbody.appendChild(tr); });
  }

  function visibleRows() {
    return Array.from(table.querySelectorAll('tbody tr')).filter(function (tr) {
      return tr.style.display !== 'none';
    });
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
    lines.push(['Quiz','Cours','Chapitre','Difficulté','Tentatives','Moyenne','Meilleur','Dernière tentative'].map(csvEscape).join(','));
    rows.forEach(function (tr) {
      var tds = tr.querySelectorAll('td');
      var quiz = (tds[0] ? tds[0].innerText : '').replace(/\s+/g, ' ').trim();
      var courseChapter = (tds[3] ? tds[3].innerText : '').replace(/\s+/g, ' ').trim();
      var diff = (tds[4] ? tds[4].innerText : '').replace(/\s+/g, ' ').trim();
      var attempts = (tds[5] ? tds[5].innerText : '').replace(/\s+/g, ' ').trim();
      var avg = (tds[6] ? tds[6].innerText : '').replace(/\s+/g, ' ').trim();
      var best = (tds[7] ? tds[7].innerText : '').replace(/\s+/g, ' ').trim();
      var last = (tds[8] ? tds[8].innerText : '').replace(/\s+/g, ' ').trim();
      lines.push([quiz, courseChapter, '', diff, attempts, avg, best, last].map(csvEscape).join(','));
    });
    var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'teacher_quiz_stats.csv';
    document.body.appendChild(a);
    a.click();
    a.remove();
    setTimeout(function () { URL.revokeObjectURL(url); }, 250);
  }

  function openModalFromRow(tr) {
    if (!modal || !tr) return;
    var id = tr.getAttribute('data-id') || '';
    var title = (tr.querySelector('td strong') ? tr.querySelector('td strong').innerText : '').trim();
    var course = (tr.getAttribute('data-course') || '').trim();
    var chapter = (tr.getAttribute('data-chapter') || '').trim();
    var attempts = tr.getAttribute('data-attempts') || '0';
    var avg = tr.getAttribute('data-avg') || '0';
    var best = tr.getAttribute('data-best') || '0';

    if (titleEl) titleEl.textContent = title !== '' ? (title + '  #' + id) : ('Quiz #' + id);
    if (subEl) subEl.textContent = (course ? ('Cours: ' + course + ' · ') : '') + (chapter ? ('Chapitre: ' + chapter) : '');
    if (qiAttempts) qiAttempts.textContent = String(attempts);
    if (qiAvg) qiAvg.textContent = String(parseFloat(avg || '0').toFixed(1)) + '%';
    if (qiBest) qiBest.textContent = String(parseInt(best || '0', 10)) + '%';

    try {
      var tds = tr.querySelectorAll('td');
      var last = (tds[8] ? tds[8].innerText : '').replace(/\s+/g, ' ').trim();
      if (qiLast) qiLast.textContent = last || '—';
    } catch (e) {
      if (qiLast) qiLast.textContent = '—';
    }

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    renderModalChart(id);
  }

  function closeModal() {
    if (!modal) return;
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    if (qiChartTip) {
      qiChartTip.classList.remove('is-on');
      qiChartTip.setAttribute('aria-hidden', 'true');
    }
  }

  function bindChartHover() {
    var chart = modal ? modal.querySelector('.pro-chart') : null;
    if (!chart || !qiChartDots) return;

    function onMove(e) {
      var qid = qiChartDots.getAttribute('data-qid') || '';
      if (!qid) return;
      var pts = seriesPointsForQuizId(qid);
      if (!pts || pts.length === 0) return;

      var rect = chart.getBoundingClientRect();
      var x = Math.max(0, Math.min(rect.width, e.clientX - rect.left));
      var idx = 0;
      if (pts.length > 1) {
        idx = Math.round((x / rect.width) * (pts.length - 1));
      }
      idx = Math.max(0, Math.min(pts.length - 1, idx));

      var val = pts[idx].v;
      var meta = pts[idx].t ? pts[idx].t : '';

      if (qiTipVal) qiTipVal.textContent = val.toFixed(1) + '%';
      if (qiTipMeta) qiTipMeta.textContent = meta;

      if (qiChartTip) {
        qiChartTip.classList.add('is-on');
        qiChartTip.setAttribute('aria-hidden', 'false');
        qiChartTip.style.left = x + 'px';
      }

      if (qiHoverLine) {
        qiHoverLine.setAttribute('x1', String((x / rect.width) * 900));
        qiHoverLine.setAttribute('x2', String((x / rect.width) * 900));
        qiHoverLine.setAttribute('opacity', '1');
      }
    }

    chart.addEventListener('mousemove', onMove);
    chart.addEventListener('mouseleave', function () {
      if (qiChartTip) {
        qiChartTip.classList.remove('is-on');
        qiChartTip.setAttribute('aria-hidden', 'true');
      }
      if (qiHoverLine) {
        qiHoverLine.setAttribute('opacity', '0');
      }
    });
  }

  function csvEscape2(v) {
    var s = (v == null ? '' : String(v));
    if (s.indexOf('"') !== -1) s = s.replace(/\"/g, '""');
    if (/[\n\r,;"]/.test(s)) return '"' + s + '"';
    return s;
  }

  table.addEventListener('click', function (e) {
    var btn = e.target && e.target.closest ? e.target.closest('.pro-quiz-inspect') : null;
    if (!btn) return;
    var tr = btn.closest('tr');
    if (!tr) return;
    openModalFromRow(tr);
  });

  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  if (modal) modal.addEventListener('click', function (e) {
    if (e.target === modal) closeModal();
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeModal();
  });

  function exportCsvFixed() {
    exportCsv();
  }

  input.addEventListener('input', apply);
  sel.addEventListener('change', apply);
  sortSel.addEventListener('change', function () { sortRows(); apply(); });
  exportBtn.addEventListener('click', function () { apply(); exportCsvFixed(); });

  sortRows();
  apply();
  renderAllSparks();
  bindChartHover();
})();
</script>
