<?php
$teacherSidebarActive = 'questions';
$isEdit = !empty($question);
$action = $isEdit
    ? APP_URL . '/index.php?url=teacher/questions/update/' . (int) $question['id']
    : APP_URL . '/index.php?url=teacher/questions/store';
$opts = $isEdit ? ($question['options'] ?? ['', '']) : ['', ''];
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <h1><?= $isEdit ? 'Modifier la question' : 'Nouvelle question' ?></h1>
                <p><a href="<?= APP_URL ?>/index.php?url=teacher/questions">← Banque</a></p>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
                <form method="post" action="<?= $action ?>" class="table-container" style="padding:24px;max-width:640px;">
                    <label>Titre court (optionnel)<br><input type="text" name="title" class="form-control" style="width:100%;" value="<?= htmlspecialchars($question['title'] ?? '') ?>"></label>
                    <br><br>
                    <label>Question<br><textarea name="question_text" rows="3" required class="form-control" style="width:100%;"><?= htmlspecialchars($question['question_text'] ?? '') ?></textarea></label>
                    <br><br>
                    <?php foreach ($opts as $i => $o): ?>
                        <label>Option <?= $i + 1 ?><br><input type="text" name="options[]" class="form-control" value="<?= htmlspecialchars($o) ?>"></label><br><br>
                    <?php endforeach; ?>
                    <label>Option supplémentaire<br><input type="text" name="options[]" class="form-control" value=""></label><br><br>
                    <label>Bonne réponse (index, 0 = première option)<br>
                        <input type="number" name="correct_answer" min="0" class="form-control" style="max-width:120px;" value="<?= (int)($question['correct_answer'] ?? 0) ?>">
                    </label>
                    <br><br>
                    <label>Tags<br><input type="text" name="tags" class="form-control" value="<?= htmlspecialchars($question['tags'] ?? '') ?>"></label>
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
