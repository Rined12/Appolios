<?php
$adminSidebarActive = 'questions';
$isEdit = !empty($question);
$action = $isEdit
    ? APP_ENTRY . '?url=admin/update-question/' . (int) $question['id']
    : APP_ENTRY . '?url=admin/store-question';
$opts = $isEdit ? ($question['options'] ?? ['', '']) : ['', ''];
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <h1><?= $isEdit ? 'Modifier la question' : 'Nouvelle question' ?></h1>
                <p><a href="<?= APP_ENTRY ?>?url=admin/questions">← Banque</a></p>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
                <form method="post" action="<?= $action ?>" class="table-container" style="padding:24px;max-width:640px;" onsubmit="return appValidateQuestionForm(this);">
                    <label>Titre (optionnel)<br><input type="text" name="title" class="form-control" style="width:100%;" maxlength="255" value="<?= htmlspecialchars($question['title'] ?? '') ?>"></label>
                    <br><br>
                    <label>Question<br><textarea name="question_text" rows="3" class="form-control" style="width:100%;"><?= htmlspecialchars($question['question_text'] ?? '') ?></textarea></label>
                    <br><br>
                    <?php foreach ($opts as $i => $o): ?>
                        <label>Option <?= $i + 1 ?><br><input type="text" name="options[]" class="form-control" maxlength="500" value="<?= htmlspecialchars($o) ?>"></label><br><br>
                    <?php endforeach; ?>
                    <label>Option supplémentaire<br><input type="text" name="options[]" class="form-control" maxlength="500" value=""></label><br><br>
                    <label>Bonne réponse (index)<br>
                        <input type="text" name="correct_answer" class="form-control" style="max-width:120px;" maxlength="4" value="<?= (int)($question['correct_answer'] ?? 0) ?>">
                    </label>
                    <br><br>
                    <label>Tags<br><input type="text" name="tags" class="form-control" maxlength="500" value="<?= htmlspecialchars($question['tags'] ?? '') ?>"></label>
                    <br><br>
                    <label>Difficulté<br>
                        <select name="difficulty" class="form-control">
                            <?php $d = $question['difficulty'] ?? 'beginner'; ?>
                            <option value="beginner" <?= $d === 'beginner' ? 'selected' : '' ?>>Débutant</option>
                            <option value="intermediate" <?= $d === 'intermediate' ? 'selected' : '' ?>>Intermédiaire</option>
                            <option value="advanced" <?= $d === 'advanced' ? 'selected' : '' ?>>Avancé</option>
                        </select>
                    </label>
                    <br><br>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function appValidateQuestionForm(f) {
  var q = f.question_text.value.replace(/^\s+|\s+$/g,'');
  if (!q) { alert('Le texte de la question est obligatoire.'); return false; }
  if (q.length > 8000) { alert('Texte trop long (8000 caractères max).'); return false; }
  var inputs = f.getElementsByTagName('input');
  var filled = 0;
  for (var i = 0; i < inputs.length; i++) {
    if (inputs[i].name === 'options[]' && String(inputs[i].value).replace(/^\s+|\s+$/g,'') !== '') filled++;
  }
  if (filled < 2) { alert('Au moins deux options non vides sont requises.'); return false; }
  var ca = String(f.correct_answer.value || '').replace(/^\s+|\s+$/g,'');
  if (ca === '' || !/^\d+$/.test(ca)) { alert('Index de bonne réponse : nombre entier positif.'); return false; }
  return true;
}
</script>

