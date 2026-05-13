<?php
$books = isset($books) && is_array($books) ? $books : [];
$stats = isset($stats) && is_array($stats) ? $stats : ['total' => 0, 'available' => 0, 'unavailable' => 0];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Back Office - Livres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/pro-theme.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg app-navbar">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="<?= APP_ENTRY ?>?url=book/back-list">EspritBookMVC</a>
            <div class="ms-auto d-flex gap-2">
                <a class="btn btn-sm btn-light" href="<?= APP_ENTRY ?>?url=book/back-add">Nouveau livre</a>
                <a class="btn btn-sm btn-outline-light" href="<?= APP_ENTRY ?>?url=book/front-list">Front Office</a>
            </div>
        </div>
    </nav>

    <main class="container page-shell">
        <section class="hero-banner p-4 p-md-5 mb-4" style="background-image: url('https://images.unsplash.com/photo-1526243741027-444d633d7365?auto=format&fit=crop&w=1600&q=80');">
            <div class="hero-content d-flex flex-wrap justify-content-between align-items-end gap-3 h-100">
                <div>
                    <h1 class="h2 fw-bold mb-2">Back Office - Catalogue des Livres</h1>
                    <p class="lead mb-0">Gérez votre bibliothèque avec une interface claire, visuelle et professionnelle.</p>
                </div>
                <a class="btn btn-primary" href="<?= APP_ENTRY ?>?url=book/back-add">Ajouter un livre</a>
            </div>

            <div class="hero-metrics mt-3">
                <span class="hero-pill">Total: <?php echo (int) ($stats['total'] ?? 0); ?></span>
                <span class="hero-pill">Disponibles: <?php echo (int) ($stats['available'] ?? 0); ?></span>
                <span class="hero-pill">Indisponibles: <?php echo (int) ($stats['unavailable'] ?? 0); ?></span>
            </div>
        </section>

        <div class="card soft-card">
            <div class="card-body p-0">
                <?php if (empty($books)): ?>
                    <div class="empty-state">Aucun livre trouve. Commencez par ajouter un nouveau livre.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Cover</th>
                                    <th>ID</th>
                                    <th>Titre</th>
                                    <th>Auteur</th>
                                    <th>Date</th>
                                    <th>Langue</th>
                                    <th>Statut</th>
                                    <th>Copies</th>
                                    <th>Categorie</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><img class="book-cover-thumb" src="<?php echo htmlspecialchars((string) ($book['cover_url'] ?? '')); ?>" alt="Cover"></td>
                                    <td><?php echo (int) ($book['id'] ?? 0); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($book['title'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($book['author'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($book['publication_date'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($book['language'] ?? '')); ?></td>
                                    <td>
                                        <?php if (!empty($book['status'])): ?>
                                            <span class="badge text-bg-success badge-status">Disponible</span>
                                        <?php else: ?>
                                            <span class="badge text-bg-secondary badge-status">Indisponible</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo (int) ($book['number_of_copies'] ?? 0); ?></td>
                                    <td><?php echo htmlspecialchars((string) (($book['category']['name'] ?? '') ?: '')); ?></td>
                                    <td>
                                        <a class="btn btn-outline-warning btn-sm" href="<?= APP_ENTRY ?>?url=book/back-edit&id=<?php echo (int) ($book['id'] ?? 0); ?>">Modifier</a>
                                        <a class="btn btn-danger btn-sm" href="<?= APP_ENTRY ?>?url=book/delete&id=<?php echo (int) ($book['id'] ?? 0); ?>" onclick="return confirm('Supprimer ce livre ?');">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
