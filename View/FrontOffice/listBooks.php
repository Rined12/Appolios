<?php
$books = isset($books) && is_array($books) ? $books : [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Front Office - Livres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/pro-theme.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg app-navbar">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="<?= APP_ENTRY ?>?url=book/front-list">EspritBookMVC</a>
            <div class="ms-auto d-flex gap-2">
                <a class="btn btn-sm btn-light" href="<?= APP_ENTRY ?>?url=book/back-list">Back Office</a>
            </div>
        </div>
    </nav>

    <main class="container page-shell">
        <section class="hero-banner p-4 p-md-5 mb-4" style="background-image: url('https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=1600&q=80');">
            <div class="hero-content d-flex flex-wrap justify-content-between align-items-end gap-3 h-100">
                <div>
                    <h1 class="h2 fw-bold mb-2">Front Office - Bibliotheque</h1>
                    <p class="lead mb-0">Explorez les livres disponibles et accedez a leurs details.</p>
                </div>
                <span class="hero-pill"><?php echo count($books); ?> livre(s)</span>
            </div>
        </section>

        <?php if (empty($books)): ?>
            <div class="card soft-card">
                <div class="empty-state">Aucun livre n'est disponible pour le moment.</div>
            </div>
        <?php else: ?>
            <section class="book-grid">
                <?php foreach ($books as $book): ?>
                    <article class="book-card">
                        <img class="book-card-media" src="<?php echo htmlspecialchars((string) ($book['cover_url'] ?? '')); ?>" alt="Photo livre">
                        <div class="book-card-body">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                <h2 class="h6 mb-0 fw-bold"><?php echo htmlspecialchars((string) ($book['title'] ?? '')); ?></h2>
                                <span class="badge badge-category"><?php echo htmlspecialchars((string) (($book['category']['name'] ?? '') ?: '')); ?></span>
                            </div>
                            <p class="book-meta mb-2">Auteur: <?php echo htmlspecialchars((string) ($book['author'] ?? '')); ?></p>
                            <p class="book-meta mb-3">Langue: <?php echo htmlspecialchars((string) ($book['language'] ?? '')); ?></p>
                            <a class="btn btn-sm btn-outline-primary w-100" href="<?= APP_ENTRY ?>?url=book/front-detail&id=<?php echo (int) ($book['id'] ?? 0); ?>">Voir detail</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
