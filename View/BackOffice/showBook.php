<?php
require_once __DIR__ . "/../../Controller/BookController.php";

$controller = new BookController();
$books = $controller->findAll();

function getBookCoverUrl(Book $book): string
{
    $category = strtolower($book->getCategory()->getName());

    $covers = [
        'science' => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?auto=format&fit=crop&w=900&q=80',
        'technology' => 'https://images.unsplash.com/photo-1516979187457-637abb4f9353?auto=format&fit=crop&w=900&q=80',
        'literature' => 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=900&q=80',
        'history' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=900&q=80',
        'arts' => 'https://images.unsplash.com/photo-1455885666463-9c87753fc601?auto=format&fit=crop&w=900&q=80',
    ];

    foreach ($covers as $key => $url) {
        if (str_contains($category, $key)) {
            return $url;
        }
    }

    return 'https://images.unsplash.com/photo-1524578271613-d550eacf6090?auto=format&fit=crop&w=900&q=80';
}
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
            <a class="navbar-brand fw-semibold" href="showBook.php">EspritBookMVC</a>
            <div class="ms-auto d-flex gap-2">
                <a class="btn btn-sm btn-light" href="addBook.php">Nouveau livre</a>
                <a class="btn btn-sm btn-outline-light" href="../FrontOffice/listBooks.php">Front Office</a>
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
                <a class="btn btn-primary" href="addBook.php">Ajouter un livre</a>
            </div>

            <div class="hero-metrics mt-3">
                <span class="hero-pill">Total: <?php echo count($books); ?></span>
                <span class="hero-pill">Disponibles: <?php echo count(array_filter($books, static fn(Book $item) => $item->getStatus() === true)); ?></span>
                <span class="hero-pill">Indisponibles: <?php echo count(array_filter($books, static fn(Book $item) => $item->getStatus() === false)); ?></span>
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
                                    <td><img class="book-cover-thumb" src="<?php echo htmlspecialchars(getBookCoverUrl($book)); ?>" alt="Cover"></td>
                                    <td><?php echo $book->getId(); ?></td>
                                    <td><?php echo htmlspecialchars($book->getTitle()); ?></td>
                                    <td><?php echo htmlspecialchars($book->getAuthor()); ?></td>
                                    <td><?php echo htmlspecialchars($book->getPublicationDate()->format('Y-m-d')); ?></td>
                                    <td><?php echo htmlspecialchars($book->getLanguage()); ?></td>
                                    <td>
                                        <?php if ($book->getStatus() === true): ?>
                                            <span class="badge text-bg-success badge-status">Disponible</span>
                                        <?php else: ?>
                                            <span class="badge text-bg-secondary badge-status">Indisponible</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $book->getNumberOfCopies(); ?></td>
                                    <td><?php echo htmlspecialchars($book->getCategory()->getName()); ?></td>
                                    <td>
                                        <a class="btn btn-outline-warning btn-sm" href="editBook.php?id=<?php echo $book->getId(); ?>">Modifier</a>
                                        <a class="btn btn-danger btn-sm" href="deleteBook.php?id=<?php echo $book->getId(); ?>" onclick="return confirm('Supprimer ce livre ?');">Supprimer</a>
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
