<?php
$adminSidebarActive = 'quiz';
$isEdit = !empty($quiz);
$action = $isEdit
    ? APP_ENTRY . '?url=admin/update-quiz/' . (int) $quiz['id']
    : APP_ENTRY . '?url=admin/store-quiz';
$questions = $isEdit && !empty($quiz['questions']) ? $quiz['questions'] : [['question' => '', 'options' => ['', ''], 'correctAnswer' => 0]];
$questionBank = $questionBank ?? [];
?>
<style>
    .admin-qz-wrap { max-width: 1020px; }
    .admin-qz-hero {
        background: linear-gradient(140deg, #ffffff 0%, #f6f9ff 60%, #eef5ff 100%);
        border: 1px solid #dbe7f6;
        border-radius: 18px;
        padding: 1rem 1.2rem;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        margin-bottom: 1rem;
    }
    .admin-qz-hero h1 {
        margin: 0;
        color: #173b6d;
        letter-spacing: -0.02em;
        font-size: clamp(1.65rem, 2.4vw, 2.25rem);
    }
    .admin-qz-back {
        margin-top: 0.45rem;
        display: inline-flex;
        font-weight: 700;
        color: #2f6fed;
        text-decoration: none;
    }
    .admin-qz-back:hover { text-decoration: underline; }
    .admin-qz-card {
        background: #fff;
        border: 1px solid #dbe7f6;
        border-radius: 18px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        padding: 1.2rem 1.2rem 1.35rem;
    }
    .admin-qz-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.85rem 1rem;
    }
    .admin-qz-field { display: grid; gap: 0.35rem; }
    .admin-qz-field--full { grid-column: 1 / -1; }
    .admin-qz-field label {
        font-size: 0.86rem;
        font-weight: 700;
        color: #284668;
    }
    .admin-qz-card .form-control {
        width: 100%;
        border: 1px solid #cfdef1;
        border-radius: 11px;
        background: #f9fcff;
        box-shadow: none;
        padding: 0.62rem 0.7rem;
    }
    .admin-qz-card .form-control:focus {
        border-color: #6aa7ff;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(47, 111, 237, 0.12);
        background: #fff;
    }
    .admin-qz-section {
        margin-top: 1rem;
        border-top: 1px solid #e7eef9;
        padding-top: 1rem;
    }
    .admin-qz-section h3 {
        margin: 0 0 0.35rem;
        color: #173b6d;
    }
    .admin-qz-hint {
        font-size: 0.9rem;
        color: #5c708d;
        margin: 0 0 0.75rem;
        max-width: 700px;
    }
    .admin-qz-bank {
        max-height: 240px;
        overflow: auto;
        border: 1px solid #dbe7f6;
        border-radius: 12px;
        padding: 0.7rem 0.8rem;
        background: #f9fcff;
        margin-bottom: 1rem;
    }
    .admin-qz-bank-item {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin: 8px 0;
        cursor: pointer;
        padding: 0.38rem 0.45rem;
        border-radius: 10px;
    }
    .admin-qz-bank-item:hover { background: #eef5ff; }
    .admin-qz-questions { display: grid; gap: 0.8rem; }
    .q-block {
        border: 1px solid #dbe7f6;
        border-radius: 12px;
        padding: 0.95rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }
    .q-block-title {
        margin: 0 0 0.65rem;
        color: #26496f;
        font-size: 0.88rem;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }
    .q-block-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.7rem 0.8rem;
    }
    .q-block-grid .full { grid-column: 1 / -1; }
    .admin-qz-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e6eef9;
    }
    .admin-qz-actions .btn { border-radius: 11px; font-weight: 800; }
    .field-error {
        color: #dc2626;
        font-size: 0.8rem;
        font-weight: 700;
        margin-top: 0.2rem;
    }
    .form-control.input-error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14) !important;
        background: #fff7f7 !important;
    }
    @media (max-width: 820px) {
        .admin-qz-grid, .q-block-grid { grid-template-columns: 1fr; }
    }
</style>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main admin-qz-wrap">
                <header class="admin-qz-hero">
                    <h1><?= $isEdit ? 'Modifier le quiz' : 'Nouveau quiz' ?></h1>
                    <a class="admin-qz-back" href="<?= APP_ENTRY ?>?url=admin/quizzes">← Retour à la liste</a>
                </header>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
                <form method="post" action="<?= $action ?>" id="quiz-form" class="admin-qz-card" onsubmit="return appValidateQuizForm(this);">
                    <div class="admin-qz-grid">
                        <div class="admin-qz-field admin-qz-field--full">
                            <label for="qz-title">Titre du quiz</label>
                            <input id="qz-title" type="text" name="title" class="form-control" maxlength="255" value="<?= htmlspecialchars($quiz['title'] ?? '') ?>">
                        </div>

                        <div class="admin-qz-field admin-qz-field--full">
                            <label for="qz-chapter">Chapitre</label>
                            <select id="qz-chapter" name="chapter_id" class="form-control">
                            <option value="">— Choisir —</option>
                            <?php foreach (($chapters ?? []) as $ch): ?>
                                <option value="<?= (int) $ch['id'] ?>" <?= ($isEdit && (int)($quiz['chapter_id'] ?? 0) === (int)$ch['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars(($ch['course_title'] ?? '') . ' — ' . ($ch['title'] ?? '')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        </div>

                        <div class="admin-qz-field">
                            <label for="qz-difficulty">Difficulté</label>
                            <select id="qz-difficulty" name="difficulty" class="form-control">
                            <?php $d = $quiz['difficulty'] ?? 'beginner'; ?>
                            <option value="beginner" <?= $d === 'beginner' ? 'selected' : '' ?>>Débutant</option>
                            <option value="intermediate" <?= $d === 'intermediate' ? 'selected' : '' ?>>Intermédiaire</option>
                            <option value="advanced" <?= $d === 'advanced' ? 'selected' : '' ?>>Avancé</option>
                        </select>
                        </div>

                        <div class="admin-qz-field">
                            <label for="qz-tags">Tags (optionnel)</label>
                            <input id="qz-tags" type="text" name="tags" class="form-control" maxlength="500" value="<?= htmlspecialchars($quiz['tags'] ?? '') ?>">
                        </div>

                        <div class="admin-qz-field">
                            <label for="qz-time">Temps limite (secondes, optionnel)</label>
                            <input id="qz-time" type="text" name="time_limit_sec" class="form-control" maxlength="6" value="<?= htmlspecialchars((string)($quiz['time_limit_sec'] ?? '')) ?>">
                        </div>
                    </div>

                    <section class="admin-qz-section">
                    <h3>Importer depuis la banque de questions</h3>
                    <p class="admin-qz-hint">Cochez des questions déjà créées dans <a href="<?= APP_ENTRY ?>?url=admin/questions">Question</a> : elles seront ajoutées au quiz après les questions saisies ci-dessous.</p>
                    <?php if (!empty($questionBank)): ?>
                        <div class="admin-qz-bank">
                            <?php foreach ($questionBank as $bq): ?>
                                <label class="admin-qz-bank-item">
                                    <input type="checkbox" name="bank_question_ids[]" value="<?= (int) $bq['id'] ?>" style="margin-top:4px;">
                                    <span><strong>#<?= (int) $bq['id'] ?></strong> — <?= htmlspecialchars(substr((string) ($bq['question_text'] ?? ''), 0, 120)) ?><?= strlen((string) ($bq['question_text'] ?? '')) > 120 ? '…' : '' ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="admin-qz-hint">La banque est vide — ajoutez des questions ou saisissez-les manuellement.</p>
                    <?php endif; ?>
                    </section>

                    <section class="admin-qz-section">
                    <h3>Questions (saisie manuelle)</h3>
                    <div id="questions-wrap" class="admin-qz-questions">
                        <?php foreach ($questions as $qi => $q): ?>
                            <div class="q-block">
                                <p class="q-block-title">Question <?= (int) ($qi + 1) ?></p>
                                <div class="q-block-grid">
                                    <?php if (!empty($q['question_bank_id'])): ?>
                                        <input type="hidden" name="questions[<?= $qi ?>][question_bank_id]" value="<?= (int) $q['question_bank_id'] ?>">
                                        <div class="admin-qz-field full">
                                            <p class="admin-qz-hint" style="margin:0;">Question importée depuis la banque <strong>#<?= (int) $q['question_bank_id'] ?></strong></p>
                                        </div>
                                    <?php endif; ?>
                                    <div class="admin-qz-field full">
                                        <label>Intitulé</label>
                                        <input type="text" name="questions[<?= $qi ?>][question]" class="form-control" maxlength="8000" value="<?= htmlspecialchars($q['question'] ?? '') ?>">
                                    </div>

                                    <?php $opts = $q['options'] ?? ['', '']; foreach ($opts as $oi => $opt): ?>
                                        <div class="admin-qz-field">
                                            <label>Option <?= $oi + 1 ?></label>
                                            <input type="text" name="questions[<?= $qi ?>][options][]" class="form-control" maxlength="500" value="<?= htmlspecialchars($opt) ?>">
                                        </div>
                                    <?php endforeach; ?>

                                    <div class="admin-qz-field">
                                        <label>Index bonne réponse (0 = 1re option)</label>
                                        <input type="text" name="questions[<?= $qi ?>][correctAnswer]" class="form-control" maxlength="4" value="<?= (int)($q['correctAnswer'] ?? 0) ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    </section>
                    <button type="button" class="btn btn-outline" id="add-q">+ Question</button>
                    <div class="admin-qz-actions">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <a class="btn btn-outline" href="<?= APP_ENTRY ?>?url=admin/quizzes">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function appClearFieldErrors(form) {
  form.querySelectorAll('.field-error').forEach(function (el) { el.remove(); });
  form.querySelectorAll('.input-error').forEach(function (el) { el.classList.remove('input-error'); });
}

function appSetFieldError(input, message) {
  if (!input) return;
  input.classList.add('input-error');
  var msg = document.createElement('div');
  msg.className = 'field-error';
  msg.textContent = message;
  input.insertAdjacentElement('afterend', msg);
}

function appValidateQuizForm(f) {
  appClearFieldErrors(f);
  var ok = true;

  var titleInput = f.title;
  var t = String(titleInput.value || '').replace(/^\s+|\s+$/g,'');
  if (!t) {
    appSetFieldError(titleInput, 'Le titre est obligatoire.');
    ok = false;
  } else if (t.length < 3) {
    appSetFieldError(titleInput, 'Minimum 3 caractères.');
    ok = false;
  } else if (t.length > 255) {
    appSetFieldError(titleInput, 'Titre trop long (255 caractères max).');
    ok = false;
  }

  var ch = f.chapter_id;
  if (!ch || ch.selectedIndex === 0 || ch.value === '') {
    appSetFieldError(ch, 'Veuillez choisir un chapitre.');
    ok = false;
  }

  var tlInput = f.time_limit_sec;
  var tl = String(tlInput.value || '').replace(/^\s+|\s+$/g,'');
  if (tl !== '') {
    if (!/^\d+$/.test(tl)) {
      appSetFieldError(tlInput, 'Le temps limite doit être un entier positif.');
      ok = false;
    } else {
      var n = parseInt(tl, 10);
      if (n < 0 || n > 86400) {
        appSetFieldError(tlInput, 'Temps limite invalide (0 à 86400 secondes).');
        ok = false;
      }
    }
  }

  var qInputs = Array.from(f.querySelectorAll('input[name^="questions["][name$="[question]"]'));
  qInputs.forEach(function (inp) {
    var v = String(inp.value || '').replace(/^\s+|\s+$/g, '');
    if (v !== '' && v.length < 3) {
      appSetFieldError(inp, 'Minimum 3 caractères.');
      ok = false;
    }
  });

  var optionInputs = Array.from(f.querySelectorAll('input[name$="[options][]"]'));
  optionInputs.forEach(function (inp) {
    var v = String(inp.value || '').replace(/^\s+|\s+$/g, '');
    if (v !== '' && v.length < 3) {
      appSetFieldError(inp, 'Minimum 3 caractères.');
      ok = false;
    }
  });

  return ok;
}
(function(){
  var wrap = document.getElementById('questions-wrap');
  var idx = <?= count($questions) ?>;
  document.getElementById('add-q').addEventListener('click', function(){
    var d = document.createElement('div');
    d.className = 'q-block';
    d.innerHTML = '<p class=\"q-block-title\">Question ' + (idx + 1) + '</p>' +
      '<div class=\"q-block-grid\">' +
      '<div class=\"admin-qz-field full\"><label>Intitulé</label><input type=\"text\" name=\"questions['+idx+'][question]\" class=\"form-control\" maxlength=\"8000\"></div>' +
      '<div class=\"admin-qz-field\"><label>Option 1</label><input type=\"text\" name=\"questions['+idx+'][options][]\" class=\"form-control\" maxlength=\"500\"></div>' +
      '<div class=\"admin-qz-field\"><label>Option 2</label><input type=\"text\" name=\"questions['+idx+'][options][]\" class=\"form-control\" maxlength=\"500\"></div>' +
      '<div class=\"admin-qz-field\"><label>Index bonne réponse</label><input type=\"text\" name=\"questions['+idx+'][correctAnswer]\" value=\"0\" class=\"form-control\" maxlength=\"4\"></div>' +
      '</div>';
    wrap.appendChild(d);
    idx++;
  });
})();
</script>

