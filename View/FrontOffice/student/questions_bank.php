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
                        <a href="<?= APP_ENTRY ?>?url=student/training" class="btn btn-training-pro">
                            <i class="bi bi-lightning-charge" aria-hidden="true"></i>
                            Training Lab
                            <span class="btn-training-pro-badge">LIVE</span>
                        </a>
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
                        <div class="pro-table-wrap">
                            <table class="pro-table" id="studentQbTable">
                                <thead>
                                    <tr>
                                        <th style="width:1%;">ID</th>
                                        <th>Titre</th>
                                        <th>Question</th>
                                        <th style="width:1%;">Niveau</th>
                                        <th style="width:1%;">Auteur</th>
                                        <th style="width:1%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($questions as $idx => $q): ?>
                                        <?php
                                            $qid = (int) ($q['id'] ?? 0);
                                            $diff = strtolower((string) ($q['difficulty'] ?? 'beginner'));
                                            $titleShown = (string) ($q['title'] ?? '');
                                            $titleShown = $titleShown !== '' ? $titleShown : 'Question';
                                            $text = (string) ($q['question_text'] ?? '');
                                            $author = (string) ($q['author_name'] ?? '');

                                            $diffClass = 'pro-badge';
                                            if ($diff === 'beginner') { $diffClass .= ' pro-badge--beginner'; }
                                            elseif ($diff === 'intermediate') { $diffClass .= ' pro-badge--intermediate'; }
                                            elseif ($diff === 'advanced') { $diffClass .= ' pro-badge--advanced'; }
                                        ?>
                                        <tr data-qrow="1" data-id="<?= (int) $qid ?>" data-title="<?= htmlspecialchars(mb_strtolower($titleShown)) ?>" data-question="<?= htmlspecialchars(mb_strtolower($text)) ?>" data-difficulty="<?= htmlspecialchars($diff) ?>" data-author="<?= htmlspecialchars(mb_strtolower($author)) ?>">
                                            <td><span class="pro-badge">#<?= (int) $qid ?></span></td>
                                            <td style="font-weight:900;"><?= htmlspecialchars($titleShown) ?></td>
                                            <td style="opacity:.92; font-weight:800; max-width: 520px;">
                                                <?= htmlspecialchars(mb_strlen($text) > 120 ? (mb_substr($text, 0, 120) . '…') : $text) ?>
                                            </td>
                                            <td><span class="<?= $diffClass ?>"><?= htmlspecialchars(difficulty_label_fr($diff)) ?></span></td>
                                            <td style="opacity:.9; font-weight:800; white-space:nowrap;">
                                                <?= $author !== '' ? htmlspecialchars($author) : '-' ?>
                                            </td>
                                            <td>
                                                <div class="pro-actions">
                                                    <button type="button" class="pro-icon-btn js-qb-toggle" data-target="qb-detail-<?= (int) $qid ?>" title="Voir" aria-label="Voir">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="qb-detail-<?= (int) $qid ?>" data-qdetail="1" style="display:none;">
                                            <td colspan="6">
                                                <div class="pro-table-card" style="padding: 12px; background: rgba(255,255,255,.03);">
                                                    <div style="font-weight:950;">Énoncé</div>
                                                    <div style="margin-top:6px; font-weight:800; opacity:.92; line-height:1.55;">
                                                        <?= htmlspecialchars($text) ?>
                                                    </div>
                                                    <div style="margin-top:10px; font-weight:950;">Propositions</div>
                                                    <ol style="margin: 8px 0 0; padding-left: 1.2rem; opacity:.92; font-weight:800;">
                                                        <?php foreach (($q['options'] ?? []) as $o): ?>
                                                            <li style="margin-top:6px;"><?= htmlspecialchars((string) $o) ?></li>
                                                        <?php endforeach; ?>
                                                    </ol>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
  var table = document.getElementById('studentQbTable');
  if (!input || !selDiff || !sortSel || !exportBtn || !table) return;

  function norm(v) { return (v || '').toString().trim().toLowerCase(); }

  function rows() {
    return Array.from(table.querySelectorAll('tbody tr[data-qrow="1"]'));
  }

  function detailRowFor(main) {
    var btn = main.querySelector('.js-qb-toggle');
    if (!btn) return null;
    var id = btn.getAttribute('data-target');
    if (!id) return null;
    return document.getElementById(id);
  }

  function getMeta(tr) {
    return {
      id: norm(tr.getAttribute('data-id')),
      title: norm(tr.getAttribute('data-title')),
      diff: norm(tr.getAttribute('data-difficulty')),
      text: norm(tr.getAttribute('data-question')),
      author: norm(tr.getAttribute('data-author'))
    };
  }

  function apply() {
    var q = norm(input.value);
    var d = norm(selDiff.value);
    rows().forEach(function (tr) {
      var m = getMeta(tr);
      var matchText = !q || m.title.indexOf(q) !== -1 || m.text.indexOf(q) !== -1 || m.author.indexOf(q) !== -1;
      var matchDiff = !d || m.diff === d;
      var show = (matchText && matchDiff);
      tr.style.display = show ? '' : 'none';
      var det = detailRowFor(tr);
      if (det) {
        det.style.display = (show && det.getAttribute('data-open') === '1') ? '' : 'none';
      }
    });
  }

  function visibleItems() {
    return rows().filter(function (tr) { return tr.style.display !== 'none'; });
  }

  function sortItems() {
    var key = norm(sortSel.value) || 'id';
    var body = table.querySelector('tbody');
    var list = rows().map(function (tr) {
      return { main: tr, detail: detailRowFor(tr), meta: getMeta(tr) };
    });
    list.sort(function (a, b) {
      var av = (a.meta[key] || '');
      var bv = (b.meta[key] || '');
      if (key === 'id') {
        return (parseInt(av || '0', 10) - parseInt(bv || '0', 10));
      }
      if (av < bv) return -1;
      if (av > bv) return 1;
      return 0;
    });
    list.forEach(function (x) {
      body.appendChild(x.main);
      if (x.detail) body.appendChild(x.detail);
    });
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
    list.forEach(function (tr) {
      var m = getMeta(tr);
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

  table.querySelectorAll('.js-qb-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var id = btn.getAttribute('data-target');
      if (!id) return;
      var det = document.getElementById(id);
      if (!det) return;
      var open = det.getAttribute('data-open') === '1';
      det.setAttribute('data-open', open ? '0' : '1');
      det.style.display = open ? 'none' : '';
    });
  });

  sortItems();
  apply();
})();
</script>

