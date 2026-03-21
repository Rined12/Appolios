<?php

class Book
{
    private ?int $id;
    private string $title;
    private string $author;
    private string $publicationDate;
    private string $language;
    private bool $status;
    private int $numberOfCopies;
    private int $categoryId;

    public function __construct(
        ?int $id,
        string $title,
        string $author,
        string $publicationDate,
        string $language,
        bool $status,
        int $numberOfCopies,
        int $categoryId
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->publicationDate = $publicationDate;
        $this->language = $language;
        $this->status = $status;
        $this->numberOfCopies = $numberOfCopies;
        $this->categoryId = $categoryId;
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

    public function getPublicationDate(): string
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

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }
}

?>