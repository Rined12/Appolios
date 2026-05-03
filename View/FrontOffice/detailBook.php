<?php
$book = isset($book) && is_array($book) ? $book : null;
$error = isset($error) ? (string) $error : null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Détail du Livre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/pro-theme.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg app-navbar">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="<?= APP_ENTRY ?>?url=book/front-list">EspritBookMVC</a>
            <div class="ms-auto d-flex gap-2">
                <a class="btn btn-sm btn-light" href="<?= APP_ENTRY ?>?url=book/front-list">Retour catalogue</a>
                <a class="btn btn-sm btn-outline-light" href="<?= APP_ENTRY ?>?url=book/back-list">Back Office</a>
            </div>
        </div>
    </nav>

    <main class="container page-shell">
        <?php if ($error): ?>
            <div class="alert alert-danger" style="margin-top: 16px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($book): ?>
        <section class="hero-banner p-4 p-md-5 mb-4" style="background-image: url('<?php echo htmlspecialchars((string) ($book['cover_url'] ?? '')); ?>');">
            <div class="hero-content d-flex justify-content-between align-items-end gap-3 h-100 flex-wrap">
                <div>
                    <h1 class="h2 fw-bold mb-2"><?php echo htmlspecialchars((string) ($book['title'] ?? '')); ?></h1>
                    <p class="lead mb-0"><?php echo htmlspecialchars((string) ($book['author'] ?? '')); ?></p>
                </div>
                <span class="hero-pill <?php echo !empty($book['status']) ? 'border-success' : 'border-light'; ?>">
                    <?php echo !empty($book['status']) ? 'Disponible' : 'Indisponible'; ?>
                </span>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-lg-4">
                <img class="book-cover-large" src="<?php echo htmlspecialchars((string) ($book['cover_url'] ?? '')); ?>" alt="Couverture du livre">
            </div>

            <div class="col-lg-8">
                <div class="card soft-card h-100">
                    <div class="card-header">Informations du livre</div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="metric-chip h-100">
                                    <div class="label">ID</div>
                                    <div class="value"><?php echo (int) ($book['id'] ?? 0); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="metric-chip h-100">
                                    <div class="label">Catégorie</div>
                                    <div class="value"><?php echo htmlspecialchars((string) (($book['category']['name'] ?? '') ?: '')); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="metric-chip h-100">
                                    <div class="label">Date de publication</div>
                                    <div class="value"><?php echo htmlspecialchars((string) ($book['publication_date'] ?? '')); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="metric-chip h-100">
                                    <div class="label">Langue</div>
                                    <div class="value"><?php echo htmlspecialchars((string) ($book['language'] ?? '')); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="metric-chip h-100">
                                    <div class="label">Nombre d'exemplaires</div>
                                    <div class="value"><?php echo (int) ($book['number_of_copies'] ?? 0); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="metric-chip h-100">
                                    <div class="label">Statut</div>
                                    <div class="value"><?php echo !empty($book['status']) ? 'Disponible' : 'Indisponible'; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
