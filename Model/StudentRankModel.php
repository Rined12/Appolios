<?php

declare(strict_types=1);

class StudentRankModel
{
    private ?int $id;
    private ?int $user_id;
    private ?int $rating;
    private ?string $league;
    private ?string $division;
    private ?string $last_updated_at;

    public function __construct(
        ?int $id = null,
        ?int $user_id = null,
        ?int $rating = null,
        ?string $league = null,
        ?string $division = null,
        ?string $last_updated_at = null
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->rating = $rating;
        $this->league = $league;
        $this->division = $division;
        $this->last_updated_at = $last_updated_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $user_id): self { $this->user_id = $user_id; return $this; }

    public function getRating(): ?int { return $this->rating; }
    public function setRating(?int $rating): self { $this->rating = $rating; return $this; }

    public function getLeague(): ?string { return $this->league; }
    public function setLeague(?string $league): self { $this->league = $league; return $this; }

    public function getDivision(): ?string { return $this->division; }
    public function setDivision(?string $division): self { $this->division = $division; return $this; }

    public function getLastUpdatedAt(): ?string { return $this->last_updated_at; }
    public function setLastUpdatedAt(?string $last_updated_at): self { $this->last_updated_at = $last_updated_at; return $this; }
}
