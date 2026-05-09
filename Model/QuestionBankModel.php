<?php

declare(strict_types=1);

class QuestionBankModel
{
    private ?int $id;
    private ?string $title;
    private ?string $question_text;
    private ?string $options_json;
    private ?int $correct_answer;
    private ?string $tags;
    private ?string $difficulty;
    private ?int $created_by;
    private ?string $created_at;
    private ?string $updated_at;

    public function __construct(
        ?int $id = null,
        ?string $title = null,
        ?string $question_text = null,
        ?string $options_json = null,
        ?int $correct_answer = null,
        ?string $tags = null,
        ?string $difficulty = null,
        ?int $created_by = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->question_text = $question_text;
        $this->options_json = $options_json;
        $this->correct_answer = $correct_answer;
        $this->tags = $tags;
        $this->difficulty = $difficulty;
        $this->created_by = $created_by;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $title): self { $this->title = $title; return $this; }

    public function getQuestionText(): ?string { return $this->question_text; }
    public function setQuestionText(?string $question_text): self { $this->question_text = $question_text; return $this; }

    public function getOptionsJson(): ?string { return $this->options_json; }
    public function setOptionsJson(?string $options_json): self { $this->options_json = $options_json; return $this; }

    public function getCorrectAnswer(): ?int { return $this->correct_answer; }
    public function setCorrectAnswer(?int $correct_answer): self { $this->correct_answer = $correct_answer; return $this; }

    public function getTags(): ?string { return $this->tags; }
    public function setTags(?string $tags): self { $this->tags = $tags; return $this; }

    public function getDifficulty(): ?string { return $this->difficulty; }
    public function setDifficulty(?string $difficulty): self { $this->difficulty = $difficulty; return $this; }

    public function getCreatedBy(): ?int { return $this->created_by; }
    public function setCreatedBy(?int $created_by): self { $this->created_by = $created_by; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }

    public function getUpdatedAt(): ?string { return $this->updated_at; }
    public function setUpdatedAt(?string $updated_at): self { $this->updated_at = $updated_at; return $this; }
}
