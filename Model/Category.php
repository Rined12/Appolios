<?php

class Category
{
    private ?int $id;
    private string $name;
    private array $books = [];

    public function __construct(?int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBooks(): array
    {
        return $this->books;
    }

    public function addBook(Book $book): void
    {
        if (!in_array($book, $this->books, true)) {
            $this->books[] = $book;
        }
    }

    public function removeBook(Book $book): void
    {
        $key = array_search($book, $this->books, true);
        if ($key !== false) {
            unset($this->books[$key]);
        }
    }
}

?>
