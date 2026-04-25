<?php
$studentSidebarActive = 'questions';
$questions = $questions ?? [];
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Banque de questions</h1>
                        <p>Entraînez-vous en lisant les énoncés et les propositions (révision). Les quiz notés se passent depuis l’onglet Quiz.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=student/quiz" class="btn btn-primary">Aller aux quiz</a>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:16px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <div class="pro-table-card" style="padding: 1.05rem;">
                    <div class="pro-table-toolbar" style="padding: 0 0 0.95rem; border-bottom: 1px solid rgba(148, 163, 184, 0.12); margin-bottom: 0.95rem;">
                        <div class="pro-table-toolbar-left">
                            <div class="pro-search" style="min-width: min(360px, 100%);">
                                <i class="bi bi-search"></i>
                                <input id="studentQbSearch" type="text" placeholder="Rechercher une question..." autocomplete="off">
                            </div>
                            <select id="studentQbDifficulty" class="pro-select" aria-label="Filtrer par niveau">
                                <option value="">Tous les niveaux</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                            <select id="studentQbSort" class="pro-select" aria-label="Trier">
                                <option value="id">Trier : ID</option>
                                <option value="title">Trier : Titre</option>
                                <option value="difficulty">Trier : Difficulté</option>
                            </select>
                        </div>
                        <div class="pro-table-toolbar-right">
                            <button type="button" class="btn btn-outline" id="studentQbExport">Exporter CSV</button>
                        </div>
                    </div>
                    <?php if (!empty($questions)): ?>
                        <div class="student-qbank-grid" style="display:flex;flex-direction:column;gap:12px;">
                            <?php foreach ($questions as $idx => $q): ?>
                                <?php
                                    $diff = (string) ($q['difficulty'] ?? 'beginner');
                                    $titleShown = (string) ($q['title'] ?? '');
                                    $titleShown = $titleShown !== '' ? $titleShown : 'Question';
                                    $text = (string) ($q['question_text'] ?? '');
                                ?>
                                <details class="pro-qbank-item" <?= $idx === 0 ? 'open' : '' ?>>
                                    <summary class="pro-qbank-summary">
                                        <span class="pro-qbank-num">#<?= (int) ($q['id'] ?? $idx + 1) ?></span>
                                        <span class="pro-qbank-head">
                                            <strong><?= htmlspecialchars($titleShown) ?></strong>
                                            <?php if (!empty($q['author_name'])): ?>
                                                <span class="pro-qbank-sub"> · <?= htmlspecialchars($q['author_name'] ?? '') ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($q['difficulty'])): ?>
                                                <?php
                                                    $diffClass = 'pro-badge';
                                                    if ($diff === 'beginner') { $diffClass .= ' pro-badge--beginner'; }
                                                    elseif ($diff === 'intermediate') { $diffClass .= ' pro-badge--intermediate'; }
                                                    elseif ($diff === 'advanced') { $diffClass .= ' pro-badge--advanced'; }
                                                ?>
                                                <span class="<?= $diffClass ?>" style="margin-left:8px;">
                                                    <?= htmlspecialchars(difficulty_label_fr($diff)) ?>
                                                </span>
                                            <?php endif; ?>
                                        </span>
                                    </summary>
                                    <div class="pro-qbank-body">
                                        <p class="pro-qbank-qtext"><?= htmlspecialchars($q['question_text'] ?? '') ?></p>
                                        <p class="pro-qbank-label">Propositions :</p>
                                        <ol class="pro-qbank-options">
                                            <?php foreach (($q['options'] ?? []) as $o): ?>
                                                <li><?= htmlspecialchars($o) ?></li>
                                            <?php endforeach; ?>
                                        </ol>
                                    </div>
                                </details>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="margin:0;color: rgba(226, 232, 240, 0.7);">La banque est vide pour le moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
  var input = document.getElementById('studentQbSearch');
  var selDiff = document.getElementById('studentQbDifficulty');
  var sortSel = document.getElementById('studentQbSort');
  var exportBtn = document.getElementById('studentQbExport');
  var grid = document.querySelector('.student-qbank-grid');
  if (!input || !selDiff || !sortSel || !exportBtn || !grid) return;

  function norm(v) { return (v || '').toString().trim().toLowerCase(); }

  function items() {
    return Array.from(grid.querySelectorAll('details.pro-qbank-item'));
  }

  function getMeta(it) {
    var num = it.querySelector('.pro-qbank-num');
    var head = it.querySelector('.pro-qbank-head strong');
    var badge = it.querySelector('.pro-qbank-head .pro-badge');
    var qtext = it.querySelector('.pro-qbank-qtext');
    var id = num ? norm(num.textContent).replace('#','') : '';
    var title = head ? norm(head.textContent) : '';
    var diff = badge ? norm(badge.textContent) : '';
    var text = qtext ? norm(qtext.textContent) : '';
    return { id: id, title: title, diff: diff, text: text };
  }

  function apply() {
    var q = norm(input.value);
    var d = norm(selDiff.value);
    items().forEach(function (it) {
      var m = getMeta(it);
      var matchText = !q || m.title.indexOf(q) !== -1 || m.text.indexOf(q) !== -1;
      var matchDiff = !d || norm(it.querySelector('.pro-qbank-head .pro-badge') && it.querySelector('.pro-qbank-head .pro-badge').className).indexOf(d) !== -1 || m.diff.indexOf(d) !== -1;
      it.style.display = (matchText && matchDiff) ? '' : 'none';
    });
  }

  function visibleItems() {
    return items().filter(function (it) { return it.style.display !== 'none'; });
  }

  function sortItems() {
    var key = norm(sortSel.value) || 'id';
    var list = items();
    list.sort(function (a, b) {
      var am = getMeta(a);
      var bm = getMeta(b);
      var av = am[key] || '';
      var bv = bm[key] || '';
      if (key === 'id') {
        return (parseInt(av || '0', 10) - parseInt(bv || '0', 10));
      }
      if (av < bv) return -1;
      if (av > bv) return 1;
      return 0;
    });
    list.forEach(function (it) { grid.appendChild(it); });
  }

  function csvEscape(v) {
    var s = (v == null ? '' : String(v));
    if (s.indexOf('"') !== -1) s = s.replace(/\"/g, '""');
    if (/[\n\r,;"]/.test(s)) return '"' + s + '"';
    return s;
  }

  function exportCsv() {
    var list = visibleItems();
    var lines = [];
    lines.push(['ID','Titre','Difficulté','Question'].map(csvEscape).join(','));
    list.forEach(function (it) {
      var m = getMeta(it);
      lines.push([m.id, m.title, m.diff, m.text].map(csvEscape).join(','));
    });
    var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'student_questions_bank.csv';
    document.body.appendChild(a);
    a.click();
    a.remove();
    setTimeout(function () { URL.revokeObjectURL(url); }, 250);
  }

  input.addEventListener('input', apply);
  selDiff.addEventListener('change', apply);
  sortSel.addEventListener('change', function () { sortItems(); apply(); });
  exportBtn.addEventListener('click', function () { apply(); exportCsv(); });

  sortItems();
  apply();
})();
</script>

