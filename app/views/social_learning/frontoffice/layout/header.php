<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Social Learning - LearnHub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>.error-text{color:#c82333;font-size:0.9rem}</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <?php $slBase = APP_URL . '/index.php?url=social-learning/'; ?>
    <a class="navbar-brand" href="<?= $slBase ?>groupe">Social Learning</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="<?= $slBase ?>groupe">Groupes</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $slBase ?>discussion">Discussions</a></li>
      </ul>
    </div>
  </div>
</nav>
<main class="container mt-4">
