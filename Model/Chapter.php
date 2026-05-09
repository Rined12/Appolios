<?php
/**
 * CHAPTER MODEL
 * Manages course chapters
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class Chapter extends BaseModel {
    protected string $table = 'chapters';
    protected string $primaryKey = 'id';

    private ?int $id;
    private ?int $course_id;
    private ?string $title;
    private ?string $description;
    private ?int $chapter_order;
    private ?string $created_at;

    public function __construct(
        ?int $id = null,
        ?int $course_id = null,
        ?string $title = null,
        ?string $description = null,
        ?int $chapter_order = null,
        ?string $created_at = null
    ) {
        parent::__construct();
        
        $this->id = $id;
        $this->course_id = $course_id;
        $this->title = $title;
        $this->description = $description;
        $this->chapter_order = $chapter_order;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getCourseId(): ?int { return $this->course_id; }
    public function setCourseId(?int $course_id): self { $this->course_id = $course_id; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $title): self { $this->title = $title; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getChapterOrder(): ?int { return $this->chapter_order; }
    public function setChapterOrder(?int $chapter_order): self { $this->chapter_order = $chapter_order; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
}