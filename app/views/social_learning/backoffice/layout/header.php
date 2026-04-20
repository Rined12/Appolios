<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin - Social Learning</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.3/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
  </nav>
  <!-- Main Sidebar Container -->
  <?php $slBase = APP_URL . '/index.php?url=social-learning/'; ?>
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link"><span class="brand-text font-weight-light">LearnHub Admin</span></a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item"><a href="<?= $slBase ?>admin/groupe" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Groupes</p></a></li>
          <li class="nav-item"><a href="<?= $slBase ?>admin/discussion" class="nav-link"><i class="nav-icon fas fa-comments"></i><p>Discussions</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>
  <div class="content-wrapper">
    <section class="content p-3">
