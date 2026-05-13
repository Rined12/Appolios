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
                        <button type="button" class="btn btn-primary" onclick="openAiQuestionModal()" style="background: linear-gradient(135deg, #8b5cf6, #3b82f6); border: none;">
                            <i class="bi bi-stars"></i> Générer avec l'IA
                        </button>
                        <a class="btn btn-outline" href="<?= APP_ENTRY ?>?url=teacher-quiz/questions">← Retour à la banque</a>
                    </div>
                </div>

                <!-- Modal IA -->
                <div id="aiQuestionModal" class="pro-modal" style="display:none; position: fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
                    <div class="pro-modal-content" style="background:var(--pro-card-bg); width:100%; max-width:500px; border-radius:12px; padding:24px; box-shadow:0 10px 25px rgba(0,0,0,0.1); margin: auto; margin-top: 10vh;">
                        <div class="pro-modal-header" style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--pro-border); padding-bottom:16px; margin-bottom:16px;">
                            <h2 style="margin:0; font-size:1.25rem;">✨ Générer une question</h2>
                            <button type="button" onclick="closeAiQuestionModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--pro-text-light);">&times;</button>
                        </div>
                        <div class="pro-modal-body">
                            <div class="form-group" style="margin-bottom:15px;">
                                <label style="display:block; margin-bottom:8px; font-weight:500;">Sujet / Thème</label>
                                <input type="text" id="aiQTopic" class="form-control" placeholder="ex: Les requêtes SQL JOIN" style="width:100%;">
                            </div>
                            <div class="form-group" style="margin-bottom:20px;">
                                <label style="display:block; margin-bottom:8px; font-weight:500;">Difficulté</label>
                                <select id="aiQDifficulty" class="form-control" style="width:100%;">
                                    <option value="beginner">Débutant</option>
                                    <option value="intermediate">Intermédiaire</option>
                                    <option value="advanced">Avancé</option>
                                </select>
                            </div>
                            <div class="pro-form-actions" style="display:flex; gap:10px; justify-content:flex-end;">
                                <button type="button" class="btn btn-outline" onclick="closeAiQuestionModal()">Annuler</button>
                                <button type="button" class="btn btn-primary" id="aiQSubmit" onclick="generateAiQuestion()" style="background: linear-gradient(135deg, #8b5cf6, #3b82f6); border: none;">
                                    <span class="btn-text">Générer</span>
                                    <span class="btn-loader" style="display:none;"><i class="bi bi-arrow-repeat spin" style="animation: spin 1s linear infinite;"></i> Génération...</span>
                                </button>
                            </div>
                            <div id="aiQError" style="color:var(--pro-danger); margin-top:15px; display:none;"></div>
                        </div>
                    </div>
                </div>
                <style>@keyframes spin { 100% { transform: rotate(360deg); } }</style>

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

function openAiQuestionModal() {
  document.getElementById('aiQuestionModal').style.display = 'flex';
  document.getElementById('aiQError').style.display = 'none';
  document.getElementById('aiQTopic').focus();
}

function closeAiQuestionModal() {
  document.getElementById('aiQuestionModal').style.display = 'none';
}

function generateAiQuestion() {
  var topic = document.getElementById('aiQTopic').value.trim();
  var difficulty = document.getElementById('aiQDifficulty').value;
  var errDiv = document.getElementById('aiQError');
  
  if (!topic) {
    errDiv.textContent = 'Veuillez saisir un sujet.';
    errDiv.style.display = 'block';
    return;
  }

  errDiv.style.display = 'none';
  var btnText = document.querySelector('#aiQSubmit .btn-text');
  var btnLoader = document.querySelector('#aiQSubmit .btn-loader');
  var btnSubmit = document.getElementById('aiQSubmit');

  btnText.style.display = 'none';
  btnLoader.style.display = 'inline-block';
  btnSubmit.disabled = true;

  var formData = new FormData();
  formData.append('topic', topic);
  formData.append('difficulty', difficulty);

  var url = '<?= APP_ENTRY ?>?url=teacher-quiz/generate-single-question-ai';

  fetch(url, {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    btnText.style.display = 'inline-block';
    btnLoader.style.display = 'none';
    btnSubmit.disabled = false;

    if (data.ok && data.question) {
      // Auto-fill form
      var q = data.question;
      document.getElementById('qf-text').value = q.question_text || '';
      document.getElementById('qf-title').value = q.title || '';
      
      var options = q.options || [];
      var optionInputs = document.querySelectorAll('input[name="options[]"]');
      
      // Ensure we have enough inputs
      for (var i = 0; i < optionInputs.length; i++) {
        optionInputs[i].value = options[i] || '';
      }
      
      document.getElementById('qf-correct').value = q.correct_answer || '0';
      document.getElementById('qf-tags').value = q.tags || '';
      document.getElementById('qf-difficulty').value = difficulty;
      
      closeAiQuestionModal();
      
      // Optional: highlight to show it worked
      document.getElementById('qf-text').style.backgroundColor = '#f0fdf4';
      setTimeout(() => { document.getElementById('qf-text').style.backgroundColor = ''; }, 1000);

    } else {
      errDiv.textContent = data.error || 'Erreur lors de la génération.';
      errDiv.style.display = 'block';
    }
  })
  .catch(err => {
    btnText.style.display = 'inline-block';
    btnLoader.style.display = 'none';
    btnSubmit.disabled = false;
    errDiv.textContent = 'Erreur réseau ou serveur.';
    errDiv.style.display = 'block';
    console.error(err);
  });
}
</script>

