<?php
$adminSidebarActive = 'quizzes';
$isEdit = !empty($quiz);
$action = $isEdit
    ? APP_ENTRY . '?url=admin-quiz/update-quiz/' . (int) $quiz['id']
    : APP_ENTRY . '?url=admin-quiz/store-quiz';
$questions = $isEdit && !empty($quiz['questions']) ? $quiz['questions'] : [['question' => '', 'options' => ['', ''], 'correctAnswer' => 0]];
$questionBank = $questionBank ?? [];
?>

            <div class="pro-table-page pro-form-page" style="padding: 0;">
                <div class="pro-table-head">
                    <div>
                        <h1><?= $isEdit ? 'Modifier le quiz' : 'Nouveau quiz' ?></h1>
                        <p>Créez et configurez le quiz (questions + banque de questions).</p>
                    </div>
                    <div class="pro-table-actions">
                        <a class="btn btn-outline" href="<?= APP_ENTRY ?>?url=admin-quiz/quizzes">← Retour à la liste</a>
                    </div>
                </div>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
                <div class="pro-table-card pro-form-card">
                <form method="post" action="<?= $action ?>" id="quiz-form" onsubmit="return appValidateQuizForm(this);">
                    <div class="pro-form-grid">
                        <div class="pro-form-field pro-form-field--full">
                            <label for="qz-title">Titre du quiz</label>
                            <input id="qz-title" type="text" name="title" class="form-control" maxlength="255" value="<?= htmlspecialchars($quiz['title'] ?? '') ?>">
                        </div>

                        <div class="pro-form-field pro-form-field--full">
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

                        <div class="pro-form-field">
                            <label for="qz-difficulty">Difficulté</label>
                            <select id="qz-difficulty" name="difficulty" class="form-control">
                            <?php $d = $quiz['difficulty'] ?? 'beginner'; ?>
                            <option value="beginner" <?= $d === 'beginner' ? 'selected' : '' ?>>Débutant</option>
                            <option value="intermediate" <?= $d === 'intermediate' ? 'selected' : '' ?>>Intermédiaire</option>
                            <option value="advanced" <?= $d === 'advanced' ? 'selected' : '' ?>>Avancé</option>
                        </select>
                        </div>

                        <div class="pro-form-field">
                            <label for="qz-tags">Tags (optionnel)</label>
                            <input id="qz-tags" type="text" name="tags" class="form-control" maxlength="500" value="<?= htmlspecialchars($quiz['tags'] ?? '') ?>">
                        </div>

                        <div class="pro-form-field">
                            <label for="qz-time">Temps limite (secondes, optionnel)</label>
                            <input id="qz-time" type="text" name="time_limit_sec" class="form-control" maxlength="6" value="<?= htmlspecialchars((string)($quiz['time_limit_sec'] ?? '')) ?>">
                        </div>
                    </div>

                    <section class="pro-form-section">
                    <h3>Importer depuis la banque de questions</h3>
                    <p class="pro-form-hint">Cochez des questions déjà créées dans <a href="<?= APP_ENTRY ?>?url=admin-quiz/questions">Question</a> : elles seront ajoutées au quiz après les questions saisies ci-dessous.</p>

                    <div class="pro-table-card" style="padding: 12px; margin: 10px 0; background: rgba(255,255,255,.03);">
                        <div style="font-weight: 950; margin-bottom: 6px;">Génération automatique (Blueprint)</div>
                        <div class="pro-form-hint" style="margin: 0 0 8px;">
                            Décris ton objectif (ex: "UML héritage, polymorphisme, 10 questions intermédiaires") puis clique sur <strong>Blueprint IA</strong>.
                        </div>
                        <div style="display:flex; gap: 10px; flex-wrap:wrap; align-items:flex-end; margin-bottom: 10px;">
                            <div style="flex: 1 1 520px;">
                                <label for="ai-blueprint-objective" class="pro-cell-sub" style="display:block; margin-bottom: 6px;">Objectif IA</label>
                                <input id="ai-blueprint-objective" type="text" class="form-control" maxlength="350" placeholder="Ex: SQL JOIN + GROUP BY, 12 questions avancées">
                            </div>
                            <div style="flex: 0 0 auto;">
                                <button type="button" class="btn-ai-magic" id="ai-blueprint-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                                    Blueprint IA
                                </button>
                            </div>
                        </div>
                        <div class="pro-form-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin: 0;">
                            <div class="pro-form-field">
                                <label for="auto-count">Nombre</label>
                                <input id="auto-count" type="text" name="auto_bank_count" class="form-control" maxlength="2" placeholder="0">
                            </div>
                            <div class="pro-form-field">
                                <label for="auto-diff">Difficulté</label>
                                <select id="auto-diff" name="auto_bank_difficulty" class="form-control">
                                    <option value="">Auto</option>
                                    <option value="beginner">Débutant</option>
                                    <option value="intermediate">Intermédiaire</option>
                                    <option value="advanced">Avancé</option>
                                </select>
                            </div>
                            <div class="pro-form-field">
                                <label for="auto-tags">Tags (optionnel)</label>
                                <input id="auto-tags" type="text" name="auto_bank_tags" class="form-control" maxlength="200" placeholder="sql, uml">
                            </div>
                        </div>
                        <div class="pro-form-hint" style="margin: 6px 0 0;">Si "Nombre" > 0, le système choisit des questions de ta banque selon les critères et les ajoute automatiquement.</div>
                    </div>

                    <?php if (!empty($questionBank)): ?>
                        <div class="pro-form-bank">
                            <?php foreach ($questionBank as $bq): ?>
                                <label class="pro-form-bank-item">
                                    <input type="checkbox" name="bank_question_ids[]" value="<?= (int) $bq['id'] ?>" style="margin-top:4px;">
                                    <span><strong>#<?= (int) $bq['id'] ?></strong> — <?= htmlspecialchars(substr((string) ($bq['question_text'] ?? ''), 0, 120)) ?><?= strlen((string) ($bq['question_text'] ?? '')) > 120 ? '…' : '' ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="pro-form-hint">La banque est vide — ajoutez des questions ou saisissez-les manuellement.</p>
                    <?php endif; ?>
                    </section>

                    <section class="pro-form-section">
                    <h3>Questions (saisie manuelle)</h3>
                    <div id="questions-wrap" class="pro-form-questions">
                        <?php foreach ($questions as $qi => $q): ?>
                            <div class="q-block">
                                <p class="q-block-title">Question <?= (int) ($qi + 1) ?></p>
                                <div class="q-block-grid">
                                    <?php if (!empty($q['question_bank_id'])): ?>
                                        <input type="hidden" name="questions[<?= $qi ?>][question_bank_id]" value="<?= (int) $q['question_bank_id'] ?>">
                                        <div class="pro-form-field full">
                                            <p class="pro-form-hint" style="margin:0;">Question importée depuis la banque <strong>#<?= (int) $q['question_bank_id'] ?></strong></p>
                                        </div>
                                    <?php endif; ?>
                                    <div class="pro-form-field full">
                                        <label>Intitulé</label>
                                        <input type="text" name="questions[<?= $qi ?>][question]" class="form-control" maxlength="8000" value="<?= htmlspecialchars($q['question'] ?? '') ?>">
                                    </div>

                                    <?php $opts = $q['options'] ?? ['', '']; foreach ($opts as $oi => $opt): ?>
                                        <div class="pro-form-field">
                                            <label>Option <?= $oi + 1 ?></label>
                                            <input type="text" name="questions[<?= $qi ?>][options][]" class="form-control" maxlength="500" value="<?= htmlspecialchars($opt) ?>">
                                        </div>
                                    <?php endforeach; ?>

                                    <div class="pro-form-field">
                                        <label>Index bonne réponse (0 = 1re option)</label>
                                        <input type="text" name="questions[<?= $qi ?>][correctAnswer]" class="form-control" maxlength="4" value="<?= (int)($q['correctAnswer'] ?? 0) ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    </section>
                    <button type="button" class="btn btn-outline" id="add-q">+ Question</button>
                    <div class="pro-form-actions">
                        <button type="button" class="btn-ai-magic" id="ai-generate">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                            Générer par IA
                        </button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <a class="btn btn-outline" href="<?= APP_ENTRY ?>?url=admin-quiz/quizzes">Annuler</a>
                    </div>
                    <div class="pro-form-hint" style="margin-top: 8px;">
                        Génération IA : sélectionne le chapitre et la difficulté, puis clique sur <strong>Générer par IA</strong>.
                        Le résultat sera injecté dans la section Questions et tu peux modifier avant d'enregistrer.
                        <span style="display:inline-block; margin-left: 10px;">
                            Nb questions:
                            <input id="ai-count" type="text" value="8" maxlength="2" style="width: 54px; margin-left: 6px;" class="form-control">
                        </span>
                    </div>
                </form>
                </div></div>
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
    d.innerHTML = '<p class="q-block-title">Question ' + (idx + 1) + '</p>' +
      '<div class="q-block-grid">' +
      '<div class="pro-form-field full"><label>Intitulé</label><input type="text" name="questions['+idx+'][question]" class="form-control" maxlength="8000"></div>' +
      '<div class="pro-form-field"><label>Option 1</label><input type="text" name="questions['+idx+'][options][]" class="form-control" maxlength="500"></div>' +
      '<div class="pro-form-field"><label>Option 2</label><input type="text" name="questions['+idx+'][options][]" class="form-control" maxlength="500"></div>' +
      '<div class="pro-form-field"><label>Index bonne réponse</label><input type="text" name="questions['+idx+'][correctAnswer]" value="0" class="form-control" maxlength="4"></div>' +
      '</div>';
    wrap.appendChild(d);
    idx++;
  });
})();

(function(){
  var btn = document.getElementById('ai-generate');
  var countInput = document.getElementById('ai-count');
  var form = document.getElementById('quiz-form');
  var wrap = document.getElementById('questions-wrap');
  if(!btn || !countInput || !form || !wrap) return;

  function normInt(v, defV){
    v = String(v || '').trim();
    if(!/^\d+$/.test(v)) return defV;
    var n = parseInt(v, 10);
    if(n < 3) n = 3;
    if(n > 20) n = 20;
    return n;
  }

  function clearQuestions(){
    wrap.innerHTML = '';
  }

  function addQuestionBlock(i, q){
    var d = document.createElement('div');
    d.className = 'q-block';
    var opts = Array.isArray(q.options) ? q.options : [];
    while(opts.length < 2) opts.push('');
    var optHtml = '';
    opts.forEach(function(opt, oi){
      optHtml += '<div class="pro-form-field"><label>Option ' + (oi + 1) + '</label>' +
        '<input type="text" name="questions['+i+'][options][]" class="form-control" maxlength="500" value="' + String(opt||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;') + '"></div>';
    });
    d.innerHTML = '<p class="q-block-title">Question ' + (i + 1) + '</p>' +
      '<div class="q-block-grid">' +
      '<div class="pro-form-field full"><label>Intitulé</label><input type="text" name="questions['+i+'][question]" class="form-control" maxlength="8000" value="' + String(q.question||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;') + '"></div>' +
      optHtml +
      '<div class="pro-form-field"><label>Index bonne réponse (0 = 1re option)</label><input type="text" name="questions['+i+'][correctAnswer]" value="' + String(q.correctAnswer||0).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;') + '" class="form-control" maxlength="4"></div>' +
      '</div>';
    wrap.appendChild(d);
  }

  function setLoading(on){
    btn.disabled = !!on;
    btn.textContent = on ? 'Génération…' : 'Générer par IA';
  }

  btn.addEventListener('click', function(){
    var ch = form.chapter_id ? String(form.chapter_id.value || '') : '';
    if(!ch){
      alert('Choisis un chapitre avant de générer.');
      return;
    }
    var cnt = normInt(countInput.value, 8);
    countInput.value = String(cnt);

    setLoading(true);
    var fd = new FormData();
    fd.append('title', form.title ? (form.title.value || '') : '');
    fd.append('difficulty', form.difficulty ? (form.difficulty.value || 'beginner') : 'beginner');
    fd.append('tags', form.tags ? (form.tags.value || '') : '');
    fd.append('chapter_id', ch);
    fd.append('count', String(cnt));

    fetch('<?= APP_ENTRY ?>?url=admin-quiz/generate-quiz-ai', {
      method: 'POST',
      body: fd,
      credentials: 'same-origin'
    }).then(function(r){ return r.json(); })
      .then(function(data){
        if(!data || !data.ok){
          alert((data && data.error) ? data.error : 'Erreur IA');
          return;
        }
        var qs = Array.isArray(data.questions) ? data.questions : [];
        if(qs.length === 0){
          alert('Aucune question générée.');
          return;
        }
        clearQuestions();
        qs.forEach(function(q, i){ addQuestionBlock(i, q); });
      })
      .catch(function(){ alert('Erreur IA'); })
      .finally(function(){ setLoading(false); });
  });
})();

(function(){
  var btn = document.getElementById('ai-blueprint-btn');
  var obj = document.getElementById('ai-blueprint-objective');
  var form = document.getElementById('quiz-form');
  var autoCount = document.getElementById('auto-count');
  var autoDiff = document.getElementById('auto-diff');
  var autoTags = document.getElementById('auto-tags');
  if(!btn || !obj || !form || !autoCount || !autoDiff || !autoTags) return;

  function setLoading(on){
    btn.disabled = !!on;
    btn.textContent = on ? 'IA…' : 'Blueprint IA';
  }

  btn.addEventListener('click', function(){
    var objective = String(obj.value || '').trim();
    if(!objective){
      alert('Écris un objectif pour le blueprint IA.');
      return;
    }
    var chapterId = form.chapter_id ? String(form.chapter_id.value || '') : '';
    if(!chapterId){
      alert("Choisis un chapitre pour mieux guider l'IA.");
      return;
    }

    setLoading(true);
    var fd = new FormData();
    fd.append('objective', objective);
    fd.append('chapter_id', chapterId);
    fd.append('difficulty', form.difficulty ? (form.difficulty.value || '') : '');
    fd.append('count', String(parseInt(autoCount.value || '10', 10) || 10));

    fetch('<?= APP_ENTRY ?>?url=admin-quiz/generate-blueprint-ai', {
      method: 'POST',
      body: fd,
      credentials: 'same-origin'
    }).then(function(r){ return r.json(); })
      .then(function(data){
        if(!data || !data.ok){
          alert((data && data.error) ? data.error : 'Erreur IA');
          return;
        }
        var bp = data.blueprint || {};
        if(bp.auto_bank_count != null) autoCount.value = String(bp.auto_bank_count);
        if(bp.auto_bank_difficulty != null) autoDiff.value = String(bp.auto_bank_difficulty);
        if(bp.auto_bank_tags != null) autoTags.value = String(bp.auto_bank_tags);
        if(bp.title && form.title) form.title.value = String(bp.title);
        if(bp.tags && form.tags) form.tags.value = String(bp.tags);
      })
      .catch(function(){ alert('Erreur IA'); })
      .finally(function(){ setLoading(false); });
  });
})();
</script>

<style>
.btn-ai-magic {
    background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
    color: white !important;
    border: none;
    font-weight: 700;
    padding: 10px 20px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(168, 85, 247, 0.3);
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.95rem;
    cursor: pointer;
}
.btn-ai-magic:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(168, 85, 247, 0.5);
    background: linear-gradient(135deg, #4f46e5 0%, #9333ea 50%, #db2777 100%);
}
.btn-ai-magic:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}
.btn-ai-magic svg {
    width: 18px;
    height: 18px;
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.15); }
    100% { transform: scale(1); }
}
</style>
