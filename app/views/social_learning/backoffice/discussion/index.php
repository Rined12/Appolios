<?php require __DIR__ . '/../../layout/header.php'; ?>
<?php $slBase = APP_URL . '/index.php?url=social-learning/'; ?>
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Discussions (Admin)</h1>
  </div>
  <table class="table table-striped">
    <thead><tr><th>ID</th><th>Titre</th><th>Groupe</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($discussions as $d): ?>
      <tr>
        <td><?= $d['id_discussion'] ?></td>
        <td><?= htmlspecialchars($d['titre']) ?></td>
        <td><?= htmlspecialchars($d['nom_groupe'] ?? ('#' . (int)$d['id_groupe'])) ?></td>
        <td>
          <a class="btn btn-sm btn-secondary" href="<?= $slBase ?>admin/discussion/edit/<?= $d['id_discussion'] ?>">Edit</a>
          <a class="btn btn-sm btn-danger" href="<?= $slBase ?>admin/discussion/delete/<?= $d['id_discussion'] ?>" data-confirm="Supprimer cette discussion ?">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/../../layout/footer.php'; ?>
