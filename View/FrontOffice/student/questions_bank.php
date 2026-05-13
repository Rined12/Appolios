<?php
/**
 * APPOLIOS - Student Questions Bank
 */

$studentSidebarActive = 'questions';
$userName = $_SESSION['user_name'] ?? 'Student';
$questions = $questions ?? [];
?>
<style>
/* Premium Questions Bank Styling */
.student-learning-page .pro-table-card {
    background: rgba(30, 41, 59, 0.4) !important;
    backdrop-filter: blur(16px) !important;
    -webkit-backdrop-filter: blur(16px) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    border-radius: 20px !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    overflow: hidden;
}

.pro-table-head h1 {
    font-size: 2.2rem !important;
    font-weight: 900 !important;
    background: linear-gradient(to right, #ffffff, #94a3b8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 8px !important;
}

.pro-table-head p {
    color: #94a3b8 !important;
    font-size: 1.05rem !important;
}

/* Toolbar & Search */
.pro-table-toolbar {
    background: rgba(15, 23, 42, 0.6) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
    padding: 16px 20px !important;
}

.pro-search input, .pro-select {
    background: rgba(255, 255, 255, 0.05) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: #f8fafc !important;
    border-radius: 10px !important;
    padding: 10px 16px !important;
    font-size: 0.95rem !important;
    transition: all 0.3s ease !important;
}
.pro-select option {
    background: #1e293b;
    color: #f8fafc;
}

.pro-search input:focus, .pro-select:focus {
    background: rgba(255, 255, 255, 0.1) !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
    outline: none !important;
}

.pro-search i {
    color: #94a3b8 !important;
}

.pro-table-toolbar-right .btn-outline {
    background: rgba(255, 255, 255, 0.05) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: #e2e8f0 !important;
    backdrop-filter: blur(8px) !important;
    border-radius: 12px !important;
    transition: all 0.2s ease !important;
}
.pro-table-toolbar-right .btn-outline:hover {
    background: rgba(255, 255, 255, 0.1) !important;
    border-color: #3b82f6 !important;
    color: #fff !important;
}

/* Table Styling */
.pro-table th {
    background: rgba(15, 23, 42, 0.6) !important;
    color: #94a3b8 !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    font-size: 0.8rem !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
    padding: 16px 20px !important;
}

.pro-table td {
    border-bottom: 1px solid rgba(255, 255, 255, 0.03) !important;
    padding: 20px !important;
    color: #e2e8f0 !important;
    font-size: 0.95rem !important;
    vertical-align: middle !important;
}

.pro-table tbody tr {
    transition: all 0.2s ease !important;
}

.pro-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.05) !important;
}

/* Badges */
.pro-badge {
    padding: 6px 12px !important;
    border-radius: 8px !important;
    font-weight: 700 !important;
    font-size: 0.75rem !important;
    text-transform: capitalize !important;
}
.pro-badge--beginner { background: rgba(16, 185, 129, 0.15) !important; color: #34d399 !important; border: 1px solid rgba(16, 185, 129, 0.3) !important; }
.pro-badge--intermediate { background: rgba(245, 158, 11, 0.15) !important; color: #fbbf24 !important; border: 1px solid rgba(245, 158, 11, 0.3) !important; }
.pro-badge--advanced { background: rgba(239, 68, 68, 0.15) !important; color: #f87171 !important; border: 1px solid rgba(239, 68, 68, 0.3) !important; }

/* Buttons */
.btn-primary-glow {
    background: linear-gradient(135deg, #3b82f6, #06b6d4) !important;
    border: none !important;
    color: #ffffff !important;
    border-radius: 12px !important;
    padding: 12px 24px !important;
    font-weight: 800 !important;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3) !important;
    transition: all 0.3s ease !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
}
.btn-primary-glow:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5) !important;
    color: #ffffff !important;
}

.pro-icon-btn {
    width: 36px !important;
    height: 36px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(255, 255, 255, 0.05) !important;
    border-radius: 10px !important;
    color: #cbd5e1 !important;
    border: none !important;
    transition: all 0.2s ease !important;
    cursor: pointer;
}

.pro-icon-btn:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    color: #3b82f6 !important;
    transform: scale(1.1) !important;
}
</style>

<div class="dashboard student-learning-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>

            <div class="admin-main pro-table-page">
                
                <div class="pro-table-head">
                    <div>
                        <h1>Banque de Questions</h1>
                        <p>Entraînez-vous en lisant les énoncés et les propositions (révision). Les quiz notés se passent depuis l’onglet Quiz.</p>
                    </div>
                    <div class="pro-table-actions">
                        <a href="<?= APP_ENTRY ?>?url=student-quiz/quiz" class="btn btn-outline" style="border: 1px solid rgba(255,255,255,0.1); color: #cbd5e1; border-radius: 12px; padding: 12px 24px;">Aller aux quiz</a>
                        <a href="<?= APP_ENTRY ?>?url=student/training" class="btn btn-primary-glow">Training Lab <i class="bi bi-play-fill" style="margin-left: 5px; font-size: 1.2rem;"></i></a>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:16px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <div class="pro-table-card" style="padding: 1.05rem;">
                    <div class="pro-table-toolbar" style="padding: 0 0 0.95rem; border-bottom: 1px solid rgba(148, 163, 184, 0.12); margin-bottom: 0.95rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                        <div class="pro-table-toolbar-left" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                            <div class="pro-search" style="min-width: min(360px, 100%); display: flex; align-items: center; position: relative;">
                                <i class="bi bi-search" style="position: absolute; left: 16px;"></i>
                                <input id="studentQbSearch" type="text" placeholder="Rechercher une question..." autocomplete="off" style="width: 100%; padding-left: 40px !important;">
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
                            <button type="button" class="btn btn-outline" id="studentQbExport" style="padding: 10px 20px;">Exporter CSV <i class="bi bi-download" style="margin-left: 5px;"></i></button>
                        </div>
                    </div>
                    <?php if (!empty($questions)): ?>
                        <div class="pro-table-wrap table-container">
                            <table class="pro-table" id="studentQbTable" style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th style="width:1%;">ID</th>
                                        <th>Titre</th>
                                        <th>Question</th>
                                        <th style="width:1%;">Niveau</th>
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
                                            <td>
                                                <div class="pro-actions">
                                                    <button type="button" class="pro-icon-btn js-qb-toggle" data-target="qb-detail-<?= (int) $qid ?>" title="Voir" aria-label="Voir">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="qb-detail-<?= (int) $qid ?>" data-qdetail="1" style="display:none;">
                                            <td colspan="5">
                                                <div class="pro-table-card" style="padding: 24px; background: rgba(255,255,255,.02); border: 1px solid rgba(255,255,255,0.05); margin: 10px 0;">
                                                    <div style="font-weight:950; color: #3b82f6; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.05em;">Énoncé</div>
                                                    <div style="margin-top:10px; font-weight:700; color: #f1f5f9; font-size: 1.1rem; line-height:1.6;">
                                                        <?= htmlspecialchars($text) ?>
                                                    </div>
                                                    <div style="margin-top:20px; font-weight:950; color: #3b82f6; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.05em;">Propositions</div>
                                                    <ol style="margin: 12px 0 0; padding-left: 1.2rem; color: #cbd5e1; font-weight:600;">
                                                        <?php foreach (($q['options'] ?? []) as $o): ?>
                                                            <li style="margin-bottom:8px; padding: 12px; background: rgba(255,255,255,0.03); border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);"><?= htmlspecialchars((string) $o) ?></li>
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
                        <div style="text-align: center; padding: 3rem 1rem; background: rgba(255,255,255,0.02); border: 2px dashed rgba(255,255,255,0.1); border-radius: 12px;">
                            <p style="color: #94a3b8; margin: 0; font-size: 1rem;">La banque est vide pour le moment.</p>
                        </div>
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
      
      // Toggle icon
      if (open) {
          btn.innerHTML = '<i class="bi bi-eye"></i>';
      } else {
          btn.innerHTML = '<i class="bi bi-eye-slash"></i>';
      }
    });
  });

  sortItems();
  apply();
})();
</script>
