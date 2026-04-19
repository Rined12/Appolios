<?php

class Book
{
    private ?int $id;
    private string $title;
    private string $author;
    private DateTime $publicationDate;
    private string $language;
    private bool $status;
    private int $numberOfCopies;
    private Category $category;

    public function __construct(
        ?int $id,
        string $title,
        string $author,
        string $publicationDate,
        string $language,
        bool $status,
        int $numberOfCopies,
        Category $category
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->publicationDate = new DateTime($publicationDate);
        $this->language = $language;
        $this->status = $status;
        $this->numberOfCopies = $numberOfCopies;
        $this->category = $category;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getPublicationDate(): DateTime
    {
        return $this->publicationDate;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function getNumberOfCopies(): int
    {
        return $this->numberOfCopies;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}

?>