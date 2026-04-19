<?php
require_once __DIR__ . "/../../Controller/BookController.php";

if (!isset($_GET['id'])) {
    die('ID manquant.');
}

$id = (int)$_GET['id'];
$controller = new BookController();
$book = $controller->findById($id);

if (!$book) {
    die('Livre introuvable.');
}

function getBookCoverUrl(Book $book): string
{
    $category = strtolower($book->getCategory()->getName());

    $covers = [
        'science' => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?auto=format&fit=crop&w=1400&q=80',
        'technology' => 'https://images.unsplash.com/photo-1516979187457-637abb4f9353?auto=format&fit=crop&w=1400&q=80',
        'literature' => 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?auto=format&fit=crop&w=1400&q=80',
        'history' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=1400&q=80',
        'arts' => 'https://images.unsplash.com/photo-1455885666463-9c87753fc601?auto=format&fit=crop&w=1400&q=80',
    ];

    foreach ($covers as $key => $url) {
        if (str_contains($category, $key)) {
            return $url;
        }
    }

    return 'https://images.unsplash.com/photo-1524578271613-d550eacf6090?auto=format&fit=crop&w=1400&q=80';
}
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
            <a class="navbar-brand fw-semibold" href="listBooks.php">EspritBookMVC</a>
            <div class="ms-auto d-flex gap-2">
                <a class="btn btn-sm btn-light" href="listBooks.php">Retour catalogue</a>
                <a class="btn btn-sm btn-outline-light" href="../BackOffice/showBook.php">Back Office</a>
            </div>
        </div>
    </nav>

    <main class="container page-shell">
        <section class="hero-banner p-4 p-md-5 mb-4" style="background-image: url('<?php echo htmlspecialchars(getBookCoverUrl($book)); ?>');">
            <div class="hero-content d-flex justify-content-between align-items-end gap-3 h-100 flex-wrap">
                <div>
                    <h1 class="h2 fw-bold mb-2"><?php echo htmlspecialchars($book->getTitle()); ?></h1>
                    <p class="lead mb-0"><?php echo htmlspecialchars($book->getAuthor()); ?></p>
                </div>
                <span class="hero-pill <?php echo ($book->getStatus() === true) ? 'border-success' : 'border-light'; ?>">
                    <?php echo ($book->getStatus() === true) ? 'Disponible' : 'Indisponible'; ?>
                </span>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-lg-4">
                <img class="book-cover-large" src="<?php echo htmlspecialchars(getBookCoverUrl($book)); ?>" alt="Couverture du livre">
            </div>

            <div class="col-lg-8">
                <div class="card soft-card h-100">
                    <div class="card-header">Informations du livre</div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="metric-chip h-100">
                                    <div class="label">ID</div>
                                    <div class="value"><?php echo $book->getId(); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="metric-chip h-100">
                                    <div class="label">Catégorie</div>
                                    <div class="value"><?php echo htmlspecialchars($book->getCategory()->getName()); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="metric-chip h-100">
                                    <div class="label">Date de publication</div>
                                    <div class="value"><?php echo htmlspecialchars($book->getPublicationDate()->format('Y-m-d')); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="metric-chip h-100">
                                    <div class="label">Langue</div>
                                    <div class="value"><?php echo htmlspecialchars($book->getLanguage()); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="metric-chip h-100">
                                    <div class="label">Nombre d'exemplaires</div>
                                    <div class="value"><?php echo $book->getNumberOfCopies(); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="metric-chip h-100">
                                    <div class="label">Statut</div>
                                    <div class="value"><?php echo ($book->getStatus() === true) ? 'Disponible' : 'Indisponible'; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
