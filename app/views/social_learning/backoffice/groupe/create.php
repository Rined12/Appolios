<?php require __DIR__ . '/../../layout/header.php'; ?>
<?php $slBase = APP_URL . '/index.php?url=social-learning/'; ?>
<div class="container-fluid">
  <h1>Créer un groupe</h1>
  <?php if (!empty($errors)): ?><div class="alert alert-danger"><ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <form method="post" action="<?= $slBase ?>admin/groupe/store" novalidate>
    <div class="mb-3">
      <label>Nom</label>
      <input type="text" name="nom_groupe" class="form-control" value="<?= $old['nom_groupe'] ?? '' ?>">
    </div>
    <div class="mb-3">
      <label>Description</label>
      <textarea name="description" class="form-control"><?= $old['description'] ?? '' ?></textarea>
    </div>
    <button class="btn btn-primary">Créer</button>
  </form>
</div>
<?php require __DIR__ . '/../../layout/footer.php'; ?>
