<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Model/Book.php';
require_once __DIR__ . '/../Model/Category.php';

class BookController extends BaseController
{
    public function index()
    {
        $this->frontList();
    }

    public function frontList(): void
    {
        $rows = $this->findAllBooksWithCategory();
        $books = $this->presentBooks($this->hydrateBooks($rows));
        $this->view('FrontOffice/listBooks', [
            '_layout' => false,
            'books' => $books,
        ]);
    }

    public function frontDetail(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id < 1) {
            http_response_code(400);
            $this->view('FrontOffice/detailBook', [
                '_layout' => false,
                'book' => null,
                'error' => 'ID manquant.',
            ]);
            return;
        }

        $row = $this->findBookByIdWithCategory($id);
        if (!$row) {
            http_response_code(404);
            $this->view('FrontOffice/detailBook', [
                '_layout' => false,
                'book' => null,
                'error' => 'Livre introuvable.',
            ]);
            return;
        }

        $pb = $this->presentBook($this->hydrateBook($row));

        $this->view('FrontOffice/detailBook', [
            '_layout' => false,
            'book' => $pb,
            'error' => null,
        ]);
    }

    public function backList(): void
    {
        $rows = $this->findAllBooksWithCategory();
        $books = $this->hydrateBooks($rows);

        $total = count($books);
        $available = 0;
        $unavailable = 0;
        foreach ($books as $b) {
            if ($b->getStatus()) {
                $available++;
            } else {
                $unavailable++;
            }
        }

        $this->view('BackOffice/showBook', [
            '_layout' => false,
            'books' => $this->presentBooks($books),
            'stats' => [
                'total' => $total,
                'available' => $available,
                'unavailable' => $unavailable,
            ],
        ]);
    }

    public function backAdd(): void
    {
        $categories = $this->getAllCategories();
        $this->view('BackOffice/addBook', [
            '_layout' => false,
            'categories' => $categories,
        ]);
    }

    public function backEdit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id < 1) {
            http_response_code(400);
            $this->view('BackOffice/editBook', [
                '_layout' => false,
                'book' => null,
                'categories' => [],
                'error' => 'ID manquant.',
            ]);
            return;
        }

        $row = $this->findBookByIdWithCategory($id);
        if (!$row) {
            http_response_code(404);
            $this->view('BackOffice/editBook', [
                '_layout' => false,
                'book' => null,
                'categories' => [],
                'error' => 'Livre introuvable.',
            ]);
            return;
        }

        $book = $this->hydrateBook($row);

        $categories = $this->getAllCategories();
        $this->view('BackOffice/editBook', [
            '_layout' => false,
            'book' => $this->presentBook($book),
            'categories' => $categories,
            'error' => null,
        ]);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('book/back-add');
            return;
        }

        $title = trim((string) ($_POST['title'] ?? ''));
        $author = trim((string) ($_POST['author'] ?? ''));
        $publicationDate = trim((string) ($_POST['publication_date'] ?? ''));
        $language = trim((string) ($_POST['language'] ?? ''));
        $status = isset($_POST['status']) && (int) $_POST['status'] === 1;
        $copies = (int) ($_POST['number_of_copies'] ?? 0);
        $categoryId = (int) ($_POST['category_id'] ?? 0);

        if ($title === '' || $author === '' || $publicationDate === '' || $language === '' || $copies < 1 || $categoryId < 1) {
            $this->setFlash('error', 'Données invalides.');
            $this->redirect('book/back-add');
            return;
        }

        $category = $this->findCategoryById($categoryId);
        if (!$category) {
            $this->setFlash('error', 'Categorie invalide.');
            $this->redirect('book/back-add');
            return;
        }

        $book = new Book(null, $title, $author, $publicationDate, $language, $status, $copies, $category);
        $this->createBook($book);
        $this->redirect('book/back-list');
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('book/back-list');
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $title = trim((string) ($_POST['title'] ?? ''));
        $author = trim((string) ($_POST['author'] ?? ''));
        $publicationDate = trim((string) ($_POST['publication_date'] ?? ''));
        $language = trim((string) ($_POST['language'] ?? ''));
        $status = isset($_POST['status']) && (int) $_POST['status'] === 1;
        $copies = (int) ($_POST['number_of_copies'] ?? 0);
        $categoryId = (int) ($_POST['category_id'] ?? 0);

        if ($id < 1 || $title === '' || $author === '' || $publicationDate === '' || $language === '' || $copies < 1 || $categoryId < 1) {
            $this->setFlash('error', 'Données invalides.');
            $this->redirect('book/back-edit?id=' . $id);
            return;
        }

        $category = $this->findCategoryById($categoryId);
        if (!$category) {
            $this->setFlash('error', 'Categorie invalide.');
            $this->redirect('book/back-edit?id=' . $id);
            return;
        }

        $book = new Book($id, $title, $author, $publicationDate, $language, $status, $copies, $category);
        $this->updateBook($book);
        $this->redirect('book/back-list');
    }

    public function delete(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id < 1) {
            $this->setFlash('error', 'ID manquant.');
            $this->redirect('book/back-list');
            return;
        }
        $this->deleteBook($id);
        $this->redirect('book/back-list');
    }

    private function findAllBooksWithCategory(): array
    {
        $db = $this->db();
        $sql = "SELECT b.id, b.title, b.author, b.publication_date, b.language, b.status, b.number_of_copies, b.category_id,
                       c.titre AS category_name
                FROM book b
                INNER JOIN category c ON c.id = b.category_id
                ORDER BY b.id DESC";

        try {
            $stmt = $db->query($sql);
            return $stmt ? $stmt->fetchAll() : [];
        } catch (Throwable $e) {
            $sql = "SELECT b.id, b.title, b.author, b.publication_date, b.language, b.status, b.number_of_copies, b.category_id,
                           c.name AS category_name
                    FROM book b
                    INNER JOIN category c ON c.id = b.category_id
                    ORDER BY b.id DESC";
            $stmt = $db->query($sql);
            return $stmt ? $stmt->fetchAll() : [];
        }
    }

    private function findBookByIdWithCategory(int $id): ?array
    {
        $db = $this->db();
        $sql = "SELECT b.id, b.title, b.author, b.publication_date, b.language, b.status, b.number_of_copies, b.category_id,
                       c.titre AS category_name
                FROM book b
                INNER JOIN category c ON c.id = b.category_id
                WHERE b.id = :id
                LIMIT 1";

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => (int) $id]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (Throwable $e) {
            $sql = "SELECT b.id, b.title, b.author, b.publication_date, b.language, b.status, b.number_of_copies, b.category_id,
                           c.name AS category_name
                    FROM book b
                    INNER JOIN category c ON c.id = b.category_id
                    WHERE b.id = :id
                    LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => (int) $id]);
            $row = $stmt->fetch();
            return $row ?: null;
        }
    }

    private function getAllCategories(): array
    {
        $db = $this->db();
        try {
            $stmt = $db->query("SELECT id, titre AS name FROM category ORDER BY titre ASC");
            return $stmt ? $stmt->fetchAll() : [];
        } catch (Throwable $e) {
            $stmt = $db->query("SELECT id, name AS name FROM category ORDER BY name ASC");
            return $stmt ? $stmt->fetchAll() : [];
        }
    }

    private function findCategoryById(int $id): ?Category
    {
        $db = $this->db();
        try {
            $stmt = $db->prepare("SELECT id, titre AS name FROM category WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => (int) $id]);
            $row = $stmt->fetch();
            return $row ? new Category((int) $row['id'], (string) $row['name']) : null;
        } catch (Throwable $e) {
            $stmt = $db->prepare("SELECT id, name AS name FROM category WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => (int) $id]);
            $row = $stmt->fetch();
            return $row ? new Category((int) $row['id'], (string) $row['name']) : null;
        }
    }

    private function createBook(Book $book): bool
    {
        $db = $this->db();
        $sql = "INSERT INTO book (title, author, publication_date, language, status, number_of_copies, category_id)
                VALUES (:title, :author, :publication_date, :language, :status, :number_of_copies, :category_id)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':title' => $book->getTitle(),
            ':author' => $book->getAuthor(),
            ':publication_date' => $book->getPublicationDate()->format('Y-m-d'),
            ':language' => $book->getLanguage(),
            ':status' => $book->getStatus() ? 1 : 0,
            ':number_of_copies' => $book->getNumberOfCopies(),
            ':category_id' => (int) ($book->getCategory()->getId() ?? 0),
        ]);
    }

    private function updateBook(Book $book): bool
    {
        if ($book->getId() === null) {
            return false;
        }

        $db = $this->db();
        $sql = "UPDATE book
                SET title = :title,
                    author = :author,
                    publication_date = :publication_date,
                    language = :language,
                    status = :status,
                    number_of_copies = :number_of_copies,
                    category_id = :category_id
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':id' => (int) $book->getId(),
            ':title' => $book->getTitle(),
            ':author' => $book->getAuthor(),
            ':publication_date' => $book->getPublicationDate()->format('Y-m-d'),
            ':language' => $book->getLanguage(),
            ':status' => $book->getStatus() ? 1 : 0,
            ':number_of_copies' => $book->getNumberOfCopies(),
            ':category_id' => (int) ($book->getCategory()->getId() ?? 0),
        ]);
    }

    private function deleteBook(int $id): bool
    {
        $db = $this->db();
        $stmt = $db->prepare("DELETE FROM book WHERE id = :id");
        return $stmt->execute([':id' => (int) $id]);
    }

    private function hydrateBooks(array $rows): array
    {
        $out = [];
        foreach ($rows as $r) {
            if (is_array($r)) {
                $out[] = $this->hydrateBook($r);
            }
        }
        return $out;
    }

    private function hydrateBook(array $row): Book
    {
        $category = new Category((int) ($row['category_id'] ?? 0), (string) ($row['category_name'] ?? ''));
        $book = new Book(
            isset($row['id']) ? (int) $row['id'] : null,
            (string) ($row['title'] ?? ''),
            (string) ($row['author'] ?? ''),
            (string) ($row['publication_date'] ?? '1970-01-01'),
            (string) ($row['language'] ?? ''),
            !empty($row['status']),
            (int) ($row['number_of_copies'] ?? 0),
            $category
        );
        $category->addBook($book);
        return $book;
    }

    private function presentBooks(array $books): array
    {
        $out = [];
        foreach ($books as $b) {
            if ($b instanceof Book) {
                $out[] = $this->presentBook($b);
            }
        }
        return $out;
    }

    private function presentBook(Book $book): array
    {
        $catName = $book->getCategory()->getName();
        return [
            'id' => (int) ($book->getId() ?? 0),
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'publication_date' => $book->getPublicationDate()->format('Y-m-d'),
            'language' => $book->getLanguage(),
            'status' => (bool) $book->getStatus(),
            'number_of_copies' => (int) $book->getNumberOfCopies(),
            'category' => [
                'id' => (int) ($book->getCategory()->getId() ?? 0),
                'name' => $catName,
            ],
            'cover_url' => $this->coverUrlForCategory($catName),
        ];
    }

    private function coverUrlForCategory(string $categoryName): string
    {
        $category = strtolower(trim($categoryName));

        $covers = [
            'science' => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?auto=format&fit=crop&w=1100&q=80',
            'technology' => 'https://images.unsplash.com/photo-1516979187457-637abb4f9353?auto=format&fit=crop&w=1100&q=80',
            'literature' => 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?auto=format&fit=crop&w=1100&q=80',
            'history' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=1100&q=80',
            'arts' => 'https://images.unsplash.com/photo-1455885666463-9c87753fc601?auto=format&fit=crop&w=1100&q=80',
        ];

        foreach ($covers as $key => $url) {
            if (str_contains($category, $key)) {
                return $url;
            }
        }

        return 'https://images.unsplash.com/photo-1524578271613-d550eacf6090?auto=format&fit=crop&w=1100&q=80';
    }
}
