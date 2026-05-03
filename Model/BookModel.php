<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/Book.php';
require_once __DIR__ . '/Category.php';

class BookModel extends BaseModel
{
    protected string $table = 'book';
    private string $categoryTable = 'category';
    private ?string $categoryLabelColumn = null;

    public function create(Book $book): bool
    {
        $sql = "INSERT INTO {$this->table} (title, author, publication_date, language, status, number_of_copies, category_id)
                VALUES (:title, :author, :publication_date, :language, :status, :number_of_copies, :category_id)";
        $stmt = $this->db->prepare($sql);

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

    public function update(Book $book): bool
    {
        if ($book->getId() === null) {
            return false;
        }

        $sql = "UPDATE {$this->table}
                SET title = :title,
                    author = :author,
                    publication_date = :publication_date,
                    language = :language,
                    status = :status,
                    number_of_copies = :number_of_copies,
                    category_id = :category_id
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);

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

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => (int) $id]);
    }

    public function findAllWithCategory(): array
    {
        $label = $this->getCategoryLabelColumn();
        $sql = "SELECT b.id, b.title, b.author, b.publication_date, b.language, b.status, b.number_of_copies, b.category_id,
                       c.{$label} AS category_name
                FROM {$this->table} b
                INNER JOIN {$this->categoryTable} c ON c.id = b.category_id
                ORDER BY b.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function findByIdWithCategory(int $id): ?array
    {
        $label = $this->getCategoryLabelColumn();
        $sql = "SELECT b.id, b.title, b.author, b.publication_date, b.language, b.status, b.number_of_copies, b.category_id,
                       c.{$label} AS category_name
                FROM {$this->table} b
                INNER JOIN {$this->categoryTable} c ON c.id = b.category_id
                WHERE b.id = :id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => (int) $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getAllCategories(): array
    {
        $label = $this->getCategoryLabelColumn();
        $stmt = $this->db->query("SELECT id, {$label} AS name FROM {$this->categoryTable} ORDER BY {$label} ASC");
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function findCategoryById(int $id): ?Category
    {
        $label = $this->getCategoryLabelColumn();
        $stmt = $this->db->prepare("SELECT id, {$label} AS name FROM {$this->categoryTable} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => (int) $id]);
        $row = $stmt->fetch();
        return $row ? new Category((int) $row['id'], (string) $row['name']) : null;
    }

    public function hydrateBook(array $row): Book
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

    private function getCategoryLabelColumn(): string
    {
        if ($this->categoryLabelColumn !== null) {
            return $this->categoryLabelColumn;
        }

        $columns = $this->db->query("SHOW COLUMNS FROM {$this->categoryTable}")->fetchAll();
        $availableColumns = array_map(static fn($column) => $column['Field'], $columns);
        $preferredColumns = ['titre', 'name', 'nom', 'category_name', 'libelle', 'description'];

        foreach ($preferredColumns as $column) {
            if (in_array($column, $availableColumns, true)) {
                $this->categoryLabelColumn = $column;
                return $column;
            }
        }

        throw new RuntimeException('Impossible de trouver la colonne du nom de categorie dans la table category.');
    }
}
