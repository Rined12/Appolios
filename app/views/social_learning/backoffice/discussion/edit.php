<?php require __DIR__ . '/../../layout/header.php'; ?>
<?php $slBase = APP_URL . '/index.php?url=social-learning/'; ?>
<div class="container-fluid">
  <h1>Modifier la discussion</h1>
  <?php if (!empty($errors)): ?><div class="alert alert-danger"><ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <form method="post" action="<?= $slBase ?>admin/discussion/update/<?= $discussion['id_discussion'] ?>">
    <div class="mb-3">
      <label>Titre</label>
      <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($discussion['titre']) ?>">
    </div>
    <div class="mb-3">
      <label>Contenu</label>
      <textarea name="contenu" class="form-control"><?= htmlspecialchars($discussion['contenu']) ?></textarea>
    </div>
    <button class="btn btn-primary">Enregistrer</button>
  </form>
</div>
<?php require __DIR__ . '/../../layout/footer.php'; ?>
