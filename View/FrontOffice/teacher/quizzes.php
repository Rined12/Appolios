<?php
$teacherSidebarActive = 'quiz';
require_once __DIR__ . '/../../../Model/QuizServer.php';
$usage = isset($quizUsage) && is_array($quizUsage) ? $quizUsage : [];
$top = isset($quizTopStats) && is_array($quizTopStats) ? $quizTopStats : [];
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

<div id="teacherRemedModal" class="modal" style="display:none; position:fixed; inset:0; background: rgba(2, 6, 23, .72); z-index: 9999;">
  <div style="max-width: 980px; margin: 40px auto; background: rgba(15, 23, 42, .98); border: 1px solid rgba(148, 163, 184, 0.16); border-radius: 14px; overflow:hidden;">
    <div style="display:flex; align-items:center; justify-content:space-between; gap: 12px; padding: 14px 16px; border-bottom: 1px solid rgba(148, 163, 184, 0.14);">
      <div>
        <div style="font-weight: 950; font-size: 18px;">Plan de rattrapage (Smart)</div>
        <div class="pro-cell-sub">Recommandations automatiques basées sur les tentatives et la difficulté observée.</div>
      </div>
      <div style="display:flex; gap: 8px;">
        <button type="button" class="btn btn-outline" id="teacherRemedRefresh">Actualiser</button>
        <button type="button" class="btn btn-outline" id="teacherRemedClose">Fermer</button>
      </div>
    </div>
    <div style="padding: 14px 16px;">
      <div id="teacherRemedBody" class="pro-table-card" style="padding: 12px; background: rgba(255,255,255,.03);"></div>
    </div>
  </div>
</div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=teacher-quiz/quiz-stats" class="btn btn-stats-pro">
                            <i class="bi bi-graph-up" aria-hidden="true"></i>
                            Statistiques
                            <span class="btn-stats-pro-badge">PRO</span>
                        </a>
                        <button type="button" class="btn btn-training-pro" id="teacherRemedBtn">
                            <i class="bi bi-magic" aria-hidden="true"></i>
                            Plan de rattrapage
                            <span class="btn-training-pro-badge">PRO</span>
                        </button>
                        <a href="<?= APP_ENTRY ?>?url=teacher-quiz/add-quiz" class="btn btn-primary">Nouveau quiz</a>
                    </div>
                </div>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
                <div class="pro-stats-grid">
                    <div class="pro-stat-card">
                        <div class="pro-stat-top">
                            <div class="pro-stat-title">Tentatives</div>
                            <div class="pro-stat-icon"><i class="bi bi-lightning-charge"></i></div>
                        </div>
                        <div class="pro-stat-value"><?= (int) ($top['attempts_total'] ?? 0) ?></div>
                        <div class="pro-stat-sub">Total sur tous vos quiz</div>
                    </div>
                    <div class="pro-stat-card">
                        <div class="pro-stat-top">
                            <div class="pro-stat-title">Moyenne</div>
                            <div class="pro-stat-icon"><i class="bi bi-bar-chart"></i></div>
                        </div>
                        <div class="pro-stat-value"><?= htmlspecialchars(number_format((float) ($top['avg_percentage'] ?? 0), 1)) ?>%</div>
                        <div class="pro-stat-sub">Moyenne pondérée (toutes tentatives)</div>
                    </div>
                </div>
                <div class="pro-table-card">
                    <div class="pro-table-toolbar">
                        <div class="pro-table-toolbar-left">
                            <div class="pro-search">
                                <i class="bi bi-search"></i>
                                <input id="teacherQuizSearch" type="text" placeholder="Rechercher un quiz..." autocomplete="off">
                            </div>
                            <input id="teacherQuizTags" type="text" class="pro-select" placeholder="Tags (ex: sql, uml)" style="min-width: 200px;">
                            <select id="teacherQuizDifficulty" class="pro-select" aria-label="Filtrer par niveau">
                                <option value="">Tous les niveaux</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                            <select id="teacherQuizStatus" class="pro-select" aria-label="Filtrer par statut">
                                <option value="">Tous les statuts</option>
                                <option value="approved">Approuvé</option>
                                <option value="pending">En attente</option>
                                <option value="rejected">Rejeté</option>
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
                                    <th>Analytics</th>
                                    <th style="width:1%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($quizzes)): foreach ($quizzes as $q): ?>
                                    <?php
                                        $diff = $q instanceof QuizServer ? (string) $q->getDifficulty() : (string) (($q['difficulty'] ?? 'beginner'));
                                        $diffClass = 'pro-badge';
                                        if ($diff === 'beginner') { $diffClass .= ' pro-badge--beginner'; }
                                        elseif ($diff === 'intermediate') { $diffClass .= ' pro-badge--intermediate'; }
                                        elseif ($diff === 'advanced') { $diffClass .= ' pro-badge--advanced'; }

                                        $qid = $q instanceof QuizServer ? (int) ($q->getId() ?? 0) : (int) ($q['id'] ?? 0);
                                        $titleTxt = $q instanceof QuizServer ? (string) $q->getTitle() : (string) ($q['title'] ?? '');
                                        $courseTitle = $q instanceof QuizServer ? (string) $q->getCourseTitle() : (string) ($q['course_title'] ?? '');
                                        $chapterTitle = $q instanceof QuizServer ? (string) $q->getChapterTitle() : (string) ($q['chapter_title'] ?? '');
                                        $tagsTxt = $q instanceof QuizServer ? (string) ($q->getTags() ?? '') : (string) ($q['tags'] ?? '');
                                        $statusTxt = $q instanceof QuizServer ? (string) ($q->getStatus() ?? '') : (string) ($q['status'] ?? '');

                                        $u = $usage[(int) $qid] ?? null;
                                        $att = is_array($u) ? (int) ($u['attempts'] ?? 0) : 0;
                                        $avg = is_array($u) ? (float) ($u['avg'] ?? 0) : 0;
                                        $last = is_array($u) ? (string) ($u['last_attempt_at'] ?? '') : '';
                                        $anaBadge = 'pro-badge';
                                        $anaLabel = 'Données insuff.';
                                        if ($att >= 10) {
                                            if ($avg <= 40) { $anaLabel = 'À problème'; $anaBadge .= ' pro-badge--advanced'; }
                                            elseif ($avg >= 85) { $anaLabel = 'Trop facile'; $anaBadge .= ' pro-badge--beginner'; }
                                            else { $anaLabel = 'OK'; $anaBadge .= ' pro-badge--intermediate'; }
                                        }
                                    ?>
                                    <tr data-id="<?= (int) $qid ?>" data-title="<?= htmlspecialchars(mb_strtolower($titleTxt)) ?>" data-course="<?= htmlspecialchars(mb_strtolower($courseTitle)) ?>" data-chapter="<?= htmlspecialchars(mb_strtolower($chapterTitle)) ?>" data-difficulty="<?= htmlspecialchars($diff) ?>" data-tags="<?= htmlspecialchars(mb_strtolower($tagsTxt)) ?>" data-status="<?= htmlspecialchars(mb_strtolower($statusTxt)) ?>">
                                        <td>
                                            <span class="pro-dot"></span>
                                            <?= htmlspecialchars($titleTxt) ?>
                                        </td>
                                        <td><?= htmlspecialchars($courseTitle) ?></td>
                                        <td><?= htmlspecialchars($chapterTitle) ?></td>
                                        <td><span class="<?= $diffClass ?>"><?= htmlspecialchars(difficulty_label_fr($diff)) ?></span></td>
                                        <td>
                                            <span class="<?= $anaBadge ?>"><?= htmlspecialchars($anaLabel) ?></span>
                                            <div class="pro-cell-sub">
                                                <?= (int) $att ?> tentatives · <?= (int) round($avg) ?>%<?= $last !== '' ? ' · ' . htmlspecialchars(substr($last, 0, 10)) : '' ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="pro-actions">
                                                <a href="<?= APP_ENTRY ?>?url=teacher-quiz/duplicate-quiz/<?= (int) $qid ?>" class="pro-icon-btn" title="Dupliquer" aria-label="Dupliquer" onclick="return confirm('Dupliquer ce quiz ?');">
                                                    <i class="bi bi-copy"></i>
                                                </a>
                                                <a href="<?= APP_ENTRY ?>?url=teacher-quiz/edit-quiz/<?= (int) $qid ?>" class="pro-icon-btn" title="Modifier" aria-label="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?= APP_ENTRY ?>?url=teacher-quiz/delete-quiz/<?= (int) $qid ?>" class="pro-icon-btn pro-icon-btn--danger" title="Supprimer" aria-label="Supprimer" onclick="return confirm('Supprimer ?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="6">Aucun quiz. Créez des chapitres puis ajoutez un quiz.</td></tr>
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
  var tagsInput = document.getElementById('teacherQuizTags');
  var sel = document.getElementById('teacherQuizDifficulty');
  var statusSel = document.getElementById('teacherQuizStatus');
  var sortSel = document.getElementById('teacherQuizSort');
  var exportBtn = document.getElementById('teacherQuizExport');
  var table = document.getElementById('teacherQuizTable');
  if (!input || !tagsInput || !sel || !statusSel || !sortSel || !exportBtn || !table) return;

  function norm(v) {
    return (v || '').toString().trim().toLowerCase();
  }

  function apply() {
    var q = norm(input.value);
    var tg = norm(tagsInput.value);
    var d = norm(sel.value);
    var st = norm(statusSel.value);
    table.querySelectorAll('tbody tr').forEach(function (tr) {
      var title = norm(tr.getAttribute('data-title'));
      var course = norm(tr.getAttribute('data-course'));
      var chapter = norm(tr.getAttribute('data-chapter'));
      var diff = norm(tr.getAttribute('data-difficulty'));
      var tags = norm(tr.getAttribute('data-tags'));
      var status = norm(tr.getAttribute('data-status'));

      var matchText = !q || title.indexOf(q) !== -1 || course.indexOf(q) !== -1 || chapter.indexOf(q) !== -1;
      var matchTags = !tg || tags.indexOf(tg) !== -1;
      var matchDiff = !d || diff === d;
      var matchStatus = !st || status === st;
      tr.style.display = (matchText && matchTags && matchDiff && matchStatus) ? '' : 'none';
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
  tagsInput.addEventListener('input', apply);
  sel.addEventListener('change', apply);
  statusSel.addEventListener('change', apply);
  sortSel.addEventListener('change', function () { sortRows(); apply(); });
  exportBtn.addEventListener('click', function () { apply(); exportCsv(); });

  sortRows();
})();

(function(){
  var btn = document.getElementById('teacherRemedBtn');
  var modal = document.getElementById('teacherRemedModal');
  var closeBtn = document.getElementById('teacherRemedClose');
  var refreshBtn = document.getElementById('teacherRemedRefresh');
  var body = document.getElementById('teacherRemedBody');
  if(!btn || !modal || !closeBtn || !refreshBtn || !body) return;

  function esc(s){
    s = (s == null ? '' : String(s));
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
  }

  function open(){ modal.style.display = 'block'; }
  function close(){ modal.style.display = 'none'; }

  function render(data){
    if(!data || !Array.isArray(data.items) || data.items.length === 0){
      body.innerHTML = '<div class="pro-cell-sub">Aucune donnée.</div>';
      return;
    }
    var html = '';
    data.items.forEach(function(it){
      var recs = Array.isArray(it.recommendations) ? it.recommendations : [];
      var recHtml = recs.length ? ('<ul style="margin: 6px 0 0; padding-left: 18px;">' + recs.map(function(r){ return '<li class="pro-cell-sub">' + esc(r) + '</li>'; }).join('') + '</ul>') : '<div class="pro-cell-sub">Aucune recommandation.</div>';
      var badge = 'pro-badge';
      if(it.level === 'HIGH') badge += ' pro-badge--advanced';
      else if(it.level === 'MEDIUM') badge += ' pro-badge--intermediate';
      else badge += ' pro-badge--beginner';
      html += '<div style="padding: 12px; border: 1px solid rgba(148,163,184,0.14); border-radius: 12px; margin-bottom: 10px; background: rgba(2,6,23,.20);">' +
        '<div style="display:flex; justify-content:space-between; gap: 10px; align-items:flex-start;">' +
          '<div>' +
            '<div style="font-weight: 900;">' + esc(it.title) + ' <span class="pro-cell-sub">#' + parseInt(it.id||0,10) + '</span></div>' +
            '<div class="pro-cell-sub">' + esc(it.sub || '') + '</div>' +
          '</div>' +
          '<div style="text-align:right;">' +
            '<span class="' + esc(badge) + '">' + esc(it.level) + '</span>' +
            '<div class="pro-cell-sub">Impact: ' + parseInt(it.score||0,10) + '/100 · ' + parseInt(it.attempts||0,10) + ' tentatives · ' + Math.round(parseFloat(it.avg||0)) + '%</div>' +
            '<div style="margin-top: 6px; display:flex; gap: 8px; justify-content:flex-end; flex-wrap:wrap;">' +
              '<a class="btn btn-outline" style="padding: 6px 10px;" href="<?= APP_ENTRY ?>?url=teacher-quiz/edit-quiz/' + parseInt(it.id||0,10) + '">Éditer</a>' +
              '<a class="btn btn-outline" style="padding: 6px 10px;" href="<?= APP_ENTRY ?>?url=teacher-quiz/quiz-stats">Stats</a>' +
            '</div>' +
          '</div>' +
        '</div>' +
        recHtml +
      '</div>';
    });
    body.innerHTML = html;
  }

  function load(){
    body.innerHTML = '<div class="pro-cell-sub">Chargement…</div>';
    fetch('<?= APP_ENTRY ?>?url=teacher-quiz/remediation-plan', { credentials: 'same-origin' })
      .then(function(r){ return r.json(); })
      .then(function(data){ render(data); })
      .catch(function(){ body.innerHTML = '<div class="pro-cell-sub">Erreur de chargement.</div>'; });
  }

  btn.addEventListener('click', function(){ open(); load(); });
  closeBtn.addEventListener('click', function(){ close(); });
  refreshBtn.addEventListener('click', function(){ load(); });
  modal.addEventListener('click', function(e){ if(e.target === modal) close(); });
})();
</script>

