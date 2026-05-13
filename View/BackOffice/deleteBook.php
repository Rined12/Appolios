<?php
$id = (int) ($_GET['id'] ?? 0);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Suppression livre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/pro-theme.css" rel="stylesheet">
</head>
<body>
    <div class="container page-shell" style="padding-top: 24px;">
        <div class="alert alert-warning">
            Cette page n'exécute plus d'action. Utilise la route MVC.
        </div>
        <a class="btn btn-danger" href="<?= APP_ENTRY ?>?url=book/delete&id=<?= (int) $id ?>">Supprimer</a>
        <a class="btn btn-outline-secondary" href="<?= APP_ENTRY ?>?url=book/back-list">Annuler</a>
    </div>
</body>
</html>
