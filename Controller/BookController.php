<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../Model/Book.php";

class BookController
{
    private PDO $pdo;
    private ?string $categoryLabelColumn = null;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    public function create(Book $book): bool
    {
        $sql = "INSERT INTO book (title, author, publication_date, language, status, number_of_copies, category_id)
                VALUES (:title, :author, :publication_date, :language, :status, :number_of_copies, :category_id)";

        $statement = $this->pdo->prepare($sql);

        $result = $statement->execute([
            ':title' => $book->getTitle(),
            ':author' => $book->getAuthor(),
            ':publication_date' => $book->getPublicationDate()->format('Y-m-d'),
            ':language' => $book->getLanguage(),
            ':status' => $book->getStatus() ? 1 : 0,
            ':number_of_copies' => $book->getNumberOfCopies(),
            ':category_id' => $book->getCategory()->getId(),
        ]);

        if ($result) {
            $book->getCategory()->addBook($book);
        }

        return $result;
    }

    public function update(Book $book): bool
    {
        if ($book->getId() === null) {
            return false;
        }

        $sql = "UPDATE book
                SET title = :title,
                    author = :author,
                    publication_date = :publication_date,
                    language = :language,
                    status = :status,
                    number_of_copies = :number_of_copies,
                    category_id = :category_id
                WHERE id = :id";

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([
            ':id' => $book->getId(),
            ':title' => $book->getTitle(),
            ':author' => $book->getAuthor(),
            ':publication_date' => $book->getPublicationDate()->format('Y-m-d'),
            ':language' => $book->getLanguage(),
            ':status' => $book->getStatus() ? 1 : 0,
            ':number_of_copies' => $book->getNumberOfCopies(),
            ':category_id' => $book->getCategory()->getId(),
        ]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM book WHERE id = :id");
        return $statement->execute([':id' => $id]);
    }

    public function findAll(): array
    {
        $categoryLabelColumn = $this->getCategoryLabelColumn();

        $sql = "SELECT b.id,
                       b.title,
                       b.author,
                       b.publication_date,
                       b.language,
                       b.status,
                       b.number_of_copies,
                       b.category_id,
                       c.{$categoryLabelColumn} AS category_name
                FROM book b
                INNER JOIN category c ON c.id = b.category_id
                ORDER BY b.id DESC";

        $results = $this->pdo->query($sql)->fetchAll();
        $books = [];

        foreach ($results as $row) {
            $category = new Category((int)$row['category_id'], $row['category_name']);
            $book = new Book(
                (int)$row['id'],
                $row['title'],
                $row['author'],
                $row['publication_date'],
                $row['language'],
                (bool)$row['status'],
                (int)$row['number_of_copies'],
                $category
            );
            $category->addBook($book);
            $books[] = $book;
        }

        return $books;
    }

    public function findById(int $id): ?Book
    {
        $categoryLabelColumn = $this->getCategoryLabelColumn();

        $sql = "SELECT b.id,
                       b.title,
                       b.author,
                       b.publication_date,
                       b.language,
                       b.status,
                       b.number_of_copies,
                       b.category_id,
                       c.{$categoryLabelColumn} AS category_name
                FROM book b
                INNER JOIN category c ON c.id = b.category_id
                WHERE b.id = :id";

        $statement = $this->pdo->prepare($sql);
        $statement->execute([':id' => $id]);
        $result = $statement->fetch();

        if ($result) {
            $category = new Category((int)$result['category_id'], $result['category_name']);
            $book = new Book(
                (int)$result['id'],
                $result['title'],
                $result['author'],
                $result['publication_date'],
                $result['language'],
                (bool)$result['status'],
                (int)$result['number_of_copies'],
                $category
            );
            $category->addBook($book);
            return $book;
        }

        return null;
    }

    public function getAllCategories(): array
    {
        $categoryLabelColumn = $this->getCategoryLabelColumn();
        $statement = $this->pdo->query("SELECT id, {$categoryLabelColumn} AS name FROM category ORDER BY {$categoryLabelColumn} ASC");
        return $statement->fetchAll();
    }

    private function getCategoryLabelColumn(): string
    {
        if ($this->categoryLabelColumn !== null) {
            return $this->categoryLabelColumn;
        }

        $columns = $this->pdo->query("SHOW COLUMNS FROM category")->fetchAll();
        $availableColumns = array_map(static fn($column) => $column['Field'], $columns);
        $preferredColumns = ['titre', 'name', 'nom', 'category_name', 'libelle', 'description'];

        foreach ($preferredColumns as $column) {
            if (in_array($column, $availableColumns, true)) {
                $this->categoryLabelColumn = $column;
                return $column;
            }
        }

        throw new RuntimeException("Impossible de trouver la colonne du nom de categorie dans la table category.");
    }

    public function findCategoryById(int $id): ?Category
    {
        $categoryLabelColumn = $this->getCategoryLabelColumn();

        $statement = $this->pdo->prepare("SELECT id, {$categoryLabelColumn} AS name FROM category WHERE id = :id");
        $statement->execute([':id' => $id]);
        $result = $statement->fetch();

        if ($result) {
            return new Category((int)$result['id'], $result['name']);
        }

        return null;
    }
}
?>