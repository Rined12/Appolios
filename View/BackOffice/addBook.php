<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter un Livre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/pro-theme.css" rel="stylesheet">
</head>
<body class="add-book-page">
    <?php
    require_once __DIR__ . "/../../Controller/BookController.php";
    $controller = new BookController();
    $categories = $controller->getAllCategories();
    ?>

    <nav class="navbar navbar-expand-lg app-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="showBook.php">EspritBookMVC</a>
            <div class="ms-auto d-flex gap-2">
                <a class="btn btn-sm btn-outline-light" href="showBook.php">Back Office</a>
                <a class="btn btn-sm btn-light" href="../FrontOffice/listBooks.php">Front Office</a>
            </div>
        </div>
    </nav>

    <main class="container py-4 add-book-bootstrap-shell">
        <section class="hero-banner add-hero-pro p-4 p-md-5 mb-4" style="background-image: url('https://images.unsplash.com/photo-1526243741027-444d633d7365?auto=format&fit=crop&w=1600&q=80');">
            <div class="hero-content d-flex flex-wrap justify-content-between align-items-end gap-3 h-100">
                <div>
                    <h1 class="h2 fw-bold mb-2">Ajouter un Livre</h1>
                    <p class="lead mb-0">Renseignez les informations du livre et publiez une fiche propre dans votre catalogue.</p>
                </div>
                <a class="btn btn-light" href="showBook.php">Voir la liste</a>
            </div>
        </section>

        <section class="row g-4 add-layout align-items-stretch">
            <div class="col-lg-4">
                <aside class="add-side-panel">
                    <img class="add-side-photo" src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&w=900&q=80" alt="Bibliotheque">
                    <h2 class="add-side-title">Conseils de saisie</h2>
                    <p class="add-side-text">Un formulaire bien rempli facilite la recherche des livres dans le Back Office et dans le Front Office.</p>
                    <ul class="add-tip-list">
                        <li>Utilisez un titre précis et lisible.</li>
                        <li>Indiquez la langue réelle du livre.</li>
                        <li>Vérifiez la catégorie pour un classement correct.</li>
                        <li>Mettez à jour le statut selon la disponibilité.</li>
                    </ul>
                </aside>
            </div>

            <div class="col-lg-8 d-flex">
                <section class="card soft-card add-form-card-bootstrap mx-auto">
                    <div class="card-header py-3 px-4">
                        <div class="add-form-header-row">
                            <div>
                                <p class="add-form-title">Formulaire d'ajout</p>
                                <p class="add-form-subtitle">Tous les champs sont obligatoires.</p>
                            </div>
                            <span class="add-form-badge">Nouveau livre</span>
                        </div>
                    </div>
                    <div class="card-body p-3 p-lg-4">
                        <form action="Verification.php" method="POST" class="row g-2 add-book-form">
                            <div class="col-12">
                                <label class="form-label" for="title">Titre</label>
                                <input class="form-control" id="title" type="text" name="title" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="author">Auteur</label>
                                <input class="form-control" id="author" type="text" name="author" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="publication_date">Date de publication</label>
                                <input class="form-control" id="publication_date" type="date" name="publication_date" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="language">Langue</label>
                                <select class="form-select" id="language" name="language" required>
                                    <option value="FR">FR</option>
                                    <option value="EN">EN</option>
                                    <option value="AR">AR</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="status">Statut</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="1">Disponible</option>
                                    <option value="0">Indisponible</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="number_of_copies">Nombre d'exemplaires</label>
                                <input class="form-control" id="number_of_copies" type="number" name="number_of_copies" min="1" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="category_id">Catégorie</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo (int)$category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12 d-grid mt-3">
                                <button class="btn btn-primary add-submit-btn" type="submit">Ajouter le livre</button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>