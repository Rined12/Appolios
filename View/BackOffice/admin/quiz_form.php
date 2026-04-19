<?php
$adminSidebarActive = 'quiz';
$isEdit = !empty($quiz);
$action = $isEdit
    ? APP_ENTRY . '?url=admin/update-quiz/' . (int) $quiz['id']
    : APP_ENTRY . '?url=admin/store-quiz';
$questions = $isEdit && !empty($quiz['questions']) ? $quiz['questions'] : [['question' => '', 'options' => ['', ''], 'correctAnswer' => 0]];
$questionBank = $questionBank ?? [];
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <h1><?= $isEdit ? 'Modifier le quiz' : 'Nouveau quiz' ?></h1>
                <p><a href="<?= APP_ENTRY ?>?url=admin/quizzes">← Liste</a></p>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
                <form method="post" action="<?= $action ?>" id="quiz-form" class="table-container" style="padding:24px;" onsubmit="return appValidateQuizForm(this);">
                    <label>Titre<br><input type="text" name="title" class="form-control" style="width:100%;max-width:480px;" maxlength="255" value="<?= htmlspecialchars($quiz['title'] ?? '') ?>"></label>
                    <br><br>
                    <label>Chapitre<br>
                        <select name="chapter_id" class="form-control" style="max-width:520px;">
                            <option value="">— Choisir —</option>
                            <?php foreach (($chapters ?? []) as $ch): ?>
                                <option value="<?= (int) $ch['id'] ?>" <?= ($isEdit && (int)($quiz['chapter_id'] ?? 0) === (int)$ch['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars(($ch['course_title'] ?? '') . ' — ' . ($ch['title'] ?? '')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <br><br>
                    <label>Difficulté<br>
                        <select name="difficulty" class="form-control" style="max-width:240px;">
                            <?php $d = $quiz['difficulty'] ?? 'beginner'; ?>
                            <option value="beginner" <?= $d === 'beginner' ? 'selected' : '' ?>>Débutant</option>
                            <option value="intermediate" <?= $d === 'intermediate' ? 'selected' : '' ?>>Intermédiaire</option>
                            <option value="advanced" <?= $d === 'advanced' ? 'selected' : '' ?>>Avancé</option>
                        </select>
                    </label>
                    <br><br>
                    <label>Tags<br><input type="text" name="tags" class="form-control" style="max-width:480px;" maxlength="500" value="<?= htmlspecialchars($quiz['tags'] ?? '') ?>"></label>
                    <br><br>
                    <label>Temps limite (sec.)<br><input type="text" name="time_limit_sec" class="form-control" style="max-width:200px;" maxlength="6" value="<?= htmlspecialchars((string)($quiz['time_limit_sec'] ?? '')) ?>"></label>
                    <hr style="margin:24px 0;">
                    <h3>Importer depuis la banque de questions</h3>
                    <p style="font-size:0.9rem;color:var(--gray-dark);max-width:640px;">Cochez des questions déjà créées dans <a href="<?= APP_ENTRY ?>?url=admin/questions">Question</a> : elles seront ajoutées au quiz après les questions saisies ci-dessous.</p>
                    <?php if (!empty($questionBank)): ?>
                        <div style="max-height:240px;overflow:auto;border:1px solid var(--gray);border-radius:8px;padding:12px;margin-bottom:20px;background:var(--card-bg, #fff);">
                            <?php foreach ($questionBank as $bq): ?>
                                <label style="display:flex;gap:10px;align-items:flex-start;margin:10px 0;cursor:pointer;">
                                    <input type="checkbox" name="bank_question_ids[]" value="<?= (int) $bq['id'] ?>" style="margin-top:4px;">
                                    <span><strong>#<?= (int) $bq['id'] ?></strong> — <?= htmlspecialchars(substr((string) ($bq['question_text'] ?? ''), 0, 120)) ?><?= strlen((string) ($bq['question_text'] ?? '')) > 120 ? '…' : '' ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="font-size:0.9rem;margin-bottom:16px;">La banque est vide — ajoutez des questions ou saisissez-les manuellement.</p>
                    <?php endif; ?>
                    <h3>Questions (saisie manuelle)</h3>
                    <div id="questions-wrap">
                        <?php foreach ($questions as $qi => $q): ?>
                            <div class="q-block" style="border:1px solid var(--gray);border-radius:8px;padding:16px;margin-bottom:16px;">
                                <label>Intitulé<br><input type="text" name="questions[<?= $qi ?>][question]" class="form-control" style="width:100%;" maxlength="8000" value="<?= htmlspecialchars($q['question'] ?? '') ?>"></label>
                                <?php $opts = $q['options'] ?? ['', '']; foreach ($opts as $oi => $opt): ?>
                                    <div style="margin-top:8px;">
                                        <label>Option <?= $oi + 1 ?><br><input type="text" name="questions[<?= $qi ?>][options][]" class="form-control" maxlength="500" value="<?= htmlspecialchars($opt) ?>"></label>
                                    </div>
                                <?php endforeach; ?>
                                <div style="margin-top:8px;">
                                    <label>Index bonne réponse<br>
                                        <input type="text" name="questions[<?= $qi ?>][correctAnswer]" class="form-control" style="max-width:120px;" maxlength="4" value="<?= (int)($q['correctAnswer'] ?? 0) ?>">
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-outline" id="add-q">+ Question</button>
                    <br><br>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function appValidateQuizForm(f) {
  var t = f.title.value.replace(/^\s+|\s+$/g,'');
  if (!t) { alert('Titre obligatoire.'); return false; }
  if (t.length > 255) { alert('Titre trop long (255 caractères max).'); return false; }
  var ch = f.chapter_id;
  if (!ch || ch.selectedIndex === 0 || ch.value === '') { alert('Choisissez un chapitre.'); return false; }
  var tl = String(f.time_limit_sec.value || '').replace(/^\s+|\s+$/g,'');
  if (tl !== '') {
    if (!/^\d+$/.test(tl)) { alert('Temps limite : nombre entier positif ou champ vide.'); return false; }
    var n = parseInt(tl, 10);
    if (n < 0 || n > 86400) { alert('Temps limite invalide (0 à 86400 secondes).'); return false; }
  }
  return true;
}
(function(){
  var wrap = document.getElementById('questions-wrap');
  var idx = <?= count($questions) ?>;
  document.getElementById('add-q').addEventListener('click', function(){
    var d = document.createElement('div');
    d.className = 'q-block';
    d.style.cssText = 'border:1px solid var(--gray);border-radius:8px;padding:16px;margin-bottom:16px;';
    d.innerHTML = '<label>Intitulé<br><input type="text" name="questions['+idx+'][question]" class="form-control" style="width:100%;" maxlength="8000"></label>'+
      '<div style="margin-top:8px;"><label>Option 1<br><input type="text" name="questions['+idx+'][options][]" class="form-control" maxlength="500"></label></div>'+
      '<div style="margin-top:8px;"><label>Option 2<br><input type="text" name="questions['+idx+'][options][]" class="form-control" maxlength="500"></label></div>'+
      '<div style="margin-top:8px;"><label>Index bonne réponse<br><input type="text" name="questions['+idx+'][correctAnswer]" value="0" class="form-control" style="max-width:120px;" maxlength="4"></label></div>';
    wrap.appendChild(d);
    idx++;
  });
})();
</script>

