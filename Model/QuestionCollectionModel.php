<?php

declare(strict_types=1);

class QuestionCollectionModel
{
    private ?int $id;
    private ?string $title;
    private ?int $created_by;
    private ?string $created_at;

    public function __construct(
        ?int $id = null,
        ?string $title = null,
        ?int $created_by = null,
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->created_by = $created_by;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $title): self { $this->title = $title; return $this; }

    public function getCreatedBy(): ?int { return $this->created_by; }
    public function setCreatedBy(?int $created_by): self { $this->created_by = $created_by; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
}
