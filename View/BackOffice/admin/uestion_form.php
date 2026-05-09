<?php
$adminSidebarActive = 'questions';
$isEdit = !empty($question);
$action = $isEdit
    ? APP_ENTRY . '?url=admin-quiz/update-question/' . (int) $question['id']
    : APP_ENTRY . '?url=admin-quiz/store-question';
$opts = $isEdit ? ($question['options'] ?? ['', '']) : ['', ''];
?>
<style>
    .admin-qf-wrap { max-width: 920px; }
    .admin-qf-hero {
        background: linear-gradient(140deg, #ffffff 0%, #f6f9ff 60%, #eef5ff 100%);
        border: 1px solid #dbe7f6;
        border-radius: 18px;
        padding: 1rem 1.2rem;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        margin-bottom: 1rem;
    }
    .admin-qf-hero h1 {
        margin: 0;
        color: #173b6d;
        letter-spacing: -0.02em;
        font-size: clamp(1.65rem, 2.4vw, 2.25rem);
    }
    .admin-qf-back {
        margin-top: 0.45rem;
        display: inline-flex;
        font-weight: 700;
        color: #2f6fed;
        text-decoration: none;
    }
    .admin-qf-back:hover { text-decoration: underline; }
    .admin-qf-card {
        background: #fff;
        border: 1px solid #dbe7f6;
        border-radius: 18px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        padding: 1.2rem 1.2rem 1.35rem;
    }
    .admin-qf-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.85rem 1rem;
    }
    .admin-qf-field { display: grid; gap: 0.35rem; }
    .admin-qf-field--full { grid-column: 1 / -1; }
    .admin-qf-field label {
        font-size: 0.86rem;
        font-weight: 700;
        color: #284668;
    }
    .admin-qf-card .form-control {
        width: 100%;
        border: 1px solid #cfdef1;
        border-radius: 11px;
        background: #f9fcff;
        box-shadow: none;
        padding: 0.62rem 0.7rem;
    }
    .admin-qf-card .form-control:focus {
        border-color: #6aa7ff;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(47, 111, 237, 0.12);
        background: #fff;
    }
    .admin-qf-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e6eef9;
    }
    .admin-qf-actions .btn { border-radius: 11px; font-weight: 800; }
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
    @media (max-width: 780px) {
        .admin-qf-grid { grid-template-columns: 1fr; }
    }
</style>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main admin-qf-wrap">
                <header class="admin-qf-hero">
                    <h1><?= $isEdit ? 'Modifier la question' : 'Nouvelle question' ?></h1>
                    <a class="admin-qf-back" href="<?= APP_ENTRY ?>?url=admin-quiz/questions">← Retour à la banque</a>
                </header>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
                <form method="post" action="<?= $action ?>" class="admin-qf-card" onsubmit="return appValidateQuestionForm(this);">
                    <div class="admin-qf-grid">
                        <div class="admin-qf-field admin-qf-field--full">
                            <label for="qf-title">Titre court (optionnel)</label>
                            <input id="qf-title" type="text" name="title" class="form-control" maxlength="255" value="<?= htmlspecialchars($question['title'] ?? '') ?>">
                        </div>

                        <div class="admin-qf-field admin-qf-field--full">
                            <label for="qf-text">Question</label>
                            <textarea id="qf-text" name="question_text" rows="4" class="form-control"><?= htmlspecialchars($question['question_text'] ?? '') ?></textarea>
                        </div>

                        <?php foreach ($opts as $i => $o): ?>
                            <div class="admin-qf-field">
                                <label for="qf-opt-<?= $i ?>">Option <?= $i + 1 ?></label>
                                <input id="qf-opt-<?= $i ?>" type="text" name="options[]" class="form-control" maxlength="500" value="<?= htmlspecialchars($o) ?>">
                            </div>
                        <?php endforeach; ?>

                        <div class="admin-qf-field">
                            <label for="qf-opt-extra">Option supplémentaire</label>
                            <input id="qf-opt-extra" type="text" name="options[]" class="form-control" maxlength="500" value="">
                        </div>

                        <div class="admin-qf-field">
                            <label for="qf-correct">Bonne réponse (index, 0 = première option)</label>
                            <input id="qf-correct" type="text" name="correct_answer" class="form-control" maxlength="4" value="<?= (int)($question['correct_answer'] ?? 0) ?>">
                        </div>

                        <div class="admin-qf-field">
                            <label for="qf-tags">Tags</label>
                            <input id="qf-tags" type="text" name="tags" class="form-control" maxlength="500" value="<?= htmlspecialchars($question['tags'] ?? '') ?>">
                        </div>

                        <div class="admin-qf-field">
                            <label for="qf-difficulty">Difficulté</label>
                            <select id="qf-difficulty" name="difficulty" class="form-control">
                            <?php $d = $question['difficulty'] ?? 'beginner'; ?>
                            <option value="beginner" <?= $d === 'beginner' ? 'selected' : '' ?>>Débutant</option>
                            <option value="intermediate" <?= $d === 'intermediate' ? 'selected' : '' ?>>Intermédiaire</option>
                            <option value="advanced" <?= $d === 'advanced' ? 'selected' : '' ?>>Avancé</option>
                            </select>
                        </div>
                    </div>
                    <div class="admin-qf-actions">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <a class="btn btn-outline" href="<?= APP_ENTRY ?>?url=admin-quiz/questions">Annuler</a>
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

function appValidateQuestionForm(f) {
  appClearFieldErrors(f);
  var ok = true;

  var qInput = f.question_text;
  var q = String(qInput.value || '').replace(/^\s+|\s+$/g, '');
  if (!q) {
    appSetFieldError(qInput, 'Le texte de la question est obligatoire.');
    ok = false;
  } else if (q.length < 3) {
    appSetFieldError(qInput, 'Minimum 3 caractères.');
    ok = false;
  } else if (q.length > 8000) {
    appSetFieldError(qInput, 'Texte trop long (8000 caractères max).');
    ok = false;
  }

  var optionInputs = Array.from(f.querySelectorAll('input[name="options[]"]'));
  optionInputs.forEach(function (inp) {
    var val = String(inp.value || '').replace(/^\s+|\s+$/g, '');
    if (val !== '' && val.length < 3) {
      appSetFieldError(inp, 'Minimum 3 caractères.');
      ok = false;
    }
  });
  var filledOptions = optionInputs.filter(function (inp) {
    return String(inp.value || '').replace(/^\s+|\s+$/g, '') !== '';
  });
  if (filledOptions.length < 2) {
    if (optionInputs[0]) appSetFieldError(optionInputs[0], 'Au moins deux options non vides sont requises.');
    ok = false;
  }

  var caInput = f.correct_answer;
  var ca = String(caInput.value || '').replace(/^\s+|\s+$/g, '');
  if (ca === '') {
    appSetFieldError(caInput, 'L\'index de bonne réponse est obligatoire.');
    ok = false;
  } else if (!/^\d+$/.test(ca)) {
    appSetFieldError(caInput, 'L\'index doit être un entier positif.');
    ok = false;
  } else {
    var caIdx = parseInt(ca, 10);
    if (caIdx >= filledOptions.length) {
      appSetFieldError(caInput, 'Index invalide : il doit correspondre à une option remplie.');
      ok = false;
    }
  }

  return ok;
}
</script>

