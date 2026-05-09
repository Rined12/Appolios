<?php
$teacherSidebarActive = 'chapitres';
$isEdit = !empty($chapter);
$allCourses = $allCourses ?? [];
$formGlobal = !empty($allCourses) && empty($course);
$action = $isEdit
    ? APP_ENTRY . '?url=teacher/update-chapter/' . (int) $chapter['id']
    : APP_ENTRY . '?url=teacher/chapitres-store';
$backHref = APP_ENTRY . '?url=teacher/chapitres';
?>
<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <h1><?= $isEdit ? 'Modifier le chapitre' : 'Nouveau chapitre' ?></h1>
                <p><a href="<?= $backHref ?>">← Retour aux chapitres</a></p>
                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>
                <div class="table-container" style="max-width:720px;">
                    <form method="post" action="<?= $action ?>" style="padding:24px;">
                        <?php if ($formGlobal): ?>
                            <label>Cours<br>
                                <select name="course_id" class="form-control" style="max-width:100%;" required>
                                    <option value="">— Choisir un cours —</option>
                                    <?php foreach ($allCourses as $c): ?>
                                        <option value="<?= (int) $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <br><br>
                        <?php endif; ?>
                        <label>Titre<br><input type="text" name="title" required class="form-control" style="width:100%;max-width:100%;" value="<?= htmlspecialchars($chapter['title'] ?? '') ?>"></label>
                        <br><br>
                        <label>Contenu<br><textarea name="content" rows="8" class="form-control" style="width:100%;"><?= htmlspecialchars($chapter['content'] ?? '') ?></textarea></label>
                        <br><br>
                        <label>Ordre<br><input type="number" name="sort_order" class="form-control" value="<?= (int) ($chapter['sort_order'] ?? 0) ?>"></label>
                        <br><br>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

