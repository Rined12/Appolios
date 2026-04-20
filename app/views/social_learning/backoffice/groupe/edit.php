<?php require __DIR__ . '/../../layout/header.php'; ?>
<?php $slBase = APP_URL . '/index.php?url=social-learning/'; ?>
<div class="container-fluid">
  <h1>Modifier le groupe</h1>
  <?php if (!empty($errors)): ?><div class="alert alert-danger"><ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <form method="post" action="<?= $slBase ?>admin/groupe/update/<?= $groupe['id_groupe'] ?>">
    <div class="mb-3">
      <label>Nom</label>
      <input type="text" name="nom_groupe" class="form-control" value="<?= htmlspecialchars($groupe['nom_groupe']) ?>">
    </div>
    <div class="mb-3">
      <label>Description</label>
      <textarea name="description" class="form-control"><?= htmlspecialchars($groupe['description']) ?></textarea>
    </div>
    <div class="mb-3">
      <label>Statut</label>
      <select name="statut" class="form-control">
        <option value="actif" <?= $groupe['statut']==='actif' ? 'selected' : '' ?>>actif</option>
        <option value="archivé" <?= $groupe['statut']==='archivé' ? 'selected' : '' ?>>archivé</option>
      </select>
    </div>
    <button class="btn btn-primary">Enregistrer</button>
  </form>
</div>
<?php require __DIR__ . '/../../layout/footer.php'; ?>
