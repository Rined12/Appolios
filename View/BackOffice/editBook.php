<?php
$book = isset($book) && is_array($book) ? $book : null;
$categories = isset($categories) && is_array($categories) ? $categories : [];
$error = isset($error) ? (string) $error : null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier un Livre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/pro-theme.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg app-navbar">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="<?= APP_ENTRY ?>?url=book/back-list">EspritBookMVC</a>
            <div class="ms-auto d-flex gap-2">
                <a class="btn btn-sm btn-light" href="<?= APP_ENTRY ?>?url=book/back-list">Back Office</a>
                <a class="btn btn-sm btn-outline-light" href="<?= APP_ENTRY ?>?url=book/front-list">Front Office</a>
            </div>
        </div>
    </nav>

    <main class="container page-shell">
        <?php if ($error): ?>
            <div class="alert alert-danger" style="margin-top: 16px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <section class="hero-banner p-4 p-md-5 mb-4" style="background-image: url('https://images.unsplash.com/photo-1529148482759-b35b25c5f217?auto=format&fit=crop&w=1600&q=80');">
            <div class="hero-content d-flex flex-wrap justify-content-between align-items-end gap-3 h-100">
                <div>
                    <h1 class="h2 fw-bold mb-2">Modifier le Livre</h1>
                    <p class="lead mb-0">Mettez à jour les informations de ce livre.</p>
                </div>
                <a class="btn btn-light" href="<?= APP_ENTRY ?>?url=book/back-list">Retour à la liste</a>
            </div>
        </section>

        <div class="card soft-card">
            <div class="card-header">Formulaire de modification</div>
            <div class="card-body p-4">
                <?php if ($book): ?>
                <form action="<?= APP_ENTRY ?>?url=book/update" method="POST" class="row g-3">
                    <input type="hidden" name="id" value="<?php echo (int) ($book['id'] ?? 0); ?>">

                    <div class="col-md-6">
                        <label class="form-label">Titre</label>
                        <input class="form-control" type="text" name="title" value="<?php echo htmlspecialchars((string) ($book['title'] ?? '')); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Auteur</label>
                        <input class="form-control" type="text" name="author" value="<?php echo htmlspecialchars((string) ($book['author'] ?? '')); ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Date de publication</label>
                        <input class="form-control" type="date" name="publication_date" value="<?php echo htmlspecialchars((string) ($book['publication_date'] ?? '')); ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Langue</label>
                        <select class="form-select" name="language" required>
                            <?php
                            $languages = ['FR', 'EN', 'AR'];
                            foreach ($languages as $lang) {
                                $selected = ((string) ($book['language'] ?? '') === $lang) ? 'selected' : '';
                                echo "<option value=\"{$lang}\" {$selected}>{$lang}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Statut</label>
                        <select class="form-select" name="status" required>
                            <option value="1" <?php echo !empty($book['status']) ? 'selected' : ''; ?>>Disponible</option>
                            <option value="0" <?php echo empty($book['status']) ? 'selected' : ''; ?>>Indisponible</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nombre d'exemplaires</label>
                        <input class="form-control" type="number" name="number_of_copies" min="1" value="<?php echo (int) ($book['number_of_copies'] ?? 0); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Catégorie</label>
                        <select class="form-select" name="category_id" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo (int)$category['id']; ?>" <?php echo ((int) ($book['category']['id'] ?? 0) === (int)$category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary px-4" type="submit">Mettre à jour</button>
                    </div>
                </form>
                <?php else: ?>
                    <div class="alert alert-warning">Livre indisponible.</div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
