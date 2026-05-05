<?php
$teacherSidebarActive = 'questions';
$isEdit = !empty($question);
$action = $isEdit
    ? APP_ENTRY . '?url=teacher-quiz/update-question/' . (int) $question['id']
    : APP_ENTRY . '?url=teacher-quiz/store-question';
$opts = $isEdit ? ($question['options'] ?? ['', '']) : ['', ''];
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page pro-form-page" style="max-width: 980px;">
                <div class="pro-table-head">
                    <div>
                        <h1><?= $isEdit ? 'Modifier la question' : 'Nouvelle question' ?></h1>
                        <p>Créez une question réutilisable dans vos quiz (banque de questions).</p>
                    </div>
                    <div class="pro-table-actions">
                        <a class="btn btn-outline" href="<?= APP_ENTRY ?>?url=teacher-quiz/questions">← Retour à la banque</a>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <div class="pro-table-card pro-form-card">
                    <form method="post" action="<?= $action ?>" onsubmit="return appValidateQuestionForm(this);">
                        <div class="pro-form-grid">
                            <div class="pro-form-field pro-form-field--full">
                                <label for="qf-title">Titre court (optionnel)</label>
                                <input id="qf-title" type="text" name="title" class="form-control" maxlength="255" value="<?= htmlspecialchars($question['title'] ?? '') ?>">
                            </div>

                            <div class="pro-form-field pro-form-field--full">
                                <label for="qf-text">Question</label>
                                <textarea id="qf-text" name="question_text" rows="4" class="form-control"><?= htmlspecialchars($question['question_text'] ?? '') ?></textarea>
                            </div>

                            <?php foreach ($opts as $i => $o): ?>
                                <div class="pro-form-field">
                                    <label for="qf-opt-<?= $i ?>">Option <?= $i + 1 ?></label>
                                    <input id="qf-opt-<?= $i ?>" type="text" name="options[]" class="form-control" maxlength="500" value="<?= htmlspecialchars($o) ?>">
                                </div>
                            <?php endforeach; ?>

                            <div class="pro-form-field">
                                <label for="qf-opt-extra">Option supplémentaire</label>
                                <input id="qf-opt-extra" type="text" name="options[]" class="form-control" maxlength="500" value="">
                            </div>

                            <div class="pro-form-field">
                                <label for="qf-correct">Bonne réponse (index, 0 = première option)</label>
                                <input id="qf-correct" type="text" name="correct_answer" class="form-control" maxlength="4" value="<?= (int)($question['correct_answer'] ?? 0) ?>">
                            </div>

                            <div class="pro-form-field">
                                <label for="qf-tags">Tags</label>
                                <input id="qf-tags" type="text" name="tags" class="form-control" maxlength="500" value="<?= htmlspecialchars($question['tags'] ?? '') ?>">
                            </div>

                            <div class="pro-form-field">
                                <label for="qf-difficulty">Difficulté</label>
                                <select id="qf-difficulty" name="difficulty" class="form-control">
                                <?php $d = $question['difficulty'] ?? 'beginner'; ?>
                                <option value="beginner" <?= $d === 'beginner' ? 'selected' : '' ?>>Débutant</option>
                                <option value="intermediate" <?= $d === 'intermediate' ? 'selected' : '' ?>>Intermédiaire</option>
                                <option value="advanced" <?= $d === 'advanced' ? 'selected' : '' ?>>Avancé</option>
                                </select>
                            </div>
                        </div>

                        <div class="pro-form-actions">
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                            <a class="btn btn-outline" href="<?= APP_ENTRY ?>?url=teacher-quiz/questions">Annuler</a>
                        </div>
                    </form>
                </div>
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

