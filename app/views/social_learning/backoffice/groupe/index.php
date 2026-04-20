<?php require __DIR__ . '/../../layout/header.php'; ?>
<?php $slBase = APP_URL . '/index.php?url=social-learning/'; ?>
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Groupes (Admin)</h1>
    <a class="btn btn-primary" href="<?= $slBase ?>admin/groupe/create">Créer</a>
  </div>
  <table class="table table-striped">
    <thead><tr><th>ID</th><th>Nom</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($groupes as $g): ?>
      <tr>
        <td><?= $g['id_groupe'] ?></td>
        <td><?= htmlspecialchars($g['nom_groupe']) ?></td>
        <td><?= $g['statut'] ?></td>
        <td>
          <a class="btn btn-sm btn-secondary" href="<?= $slBase ?>admin/groupe/edit/<?= $g['id_groupe'] ?>">Edit</a>
          <a class="btn btn-sm btn-danger" href="<?= $slBase ?>admin/groupe/delete/<?= $g['id_groupe'] ?>" data-confirm="Supprimer ce groupe ?">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/../../layout/footer.php'; ?>
