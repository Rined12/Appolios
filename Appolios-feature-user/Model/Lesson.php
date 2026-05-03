<?php
/**
 * LESSON MODEL
 * Manages course lessons (videos, text, PDFs)
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class Lesson extends BaseModel {
    protected string $table = 'lessons';
    protected string $primaryKey = 'id';

    private ?int $id;
    private ?int $chapter_id;
    private ?string $title;
    private ?string $lesson_type;
    private ?string $video_url;
    private ?string $content;
    private ?string $pdf_path;
    private ?int $duration;
    private ?int $lesson_order;
    private ?string $created_at;

    public function __construct(
        ?int $id = null,
        ?int $chapter_id = null,
        ?string $title = null,
        ?string $lesson_type = null,
        ?string $video_url = null,
        ?string $content = null,
        ?string $pdf_path = null,
        ?int $duration = null,
        ?int $lesson_order = null,
        ?string $created_at = null
    ) {
        parent::__construct();
        
        $this->id = $id;
        $this->chapter_id = $chapter_id;
        $this->title = $title;
        $this->lesson_type = $lesson_type;
        $this->video_url = $video_url;
        $this->content = $content;
        $this->pdf_path = $pdf_path;
        $this->duration = $duration;
        $this->lesson_order = $lesson_order;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getChapterId(): ?int { return $this->chapter_id; }
    public function setChapterId(?int $chapter_id): self { $this->chapter_id = $chapter_id; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $title): self { $this->title = $title; return $this; }

    public function getLessonType(): ?string { return $this->lesson_type; }
    public function setLessonType(?string $lesson_type): self { $this->lesson_type = $lesson_type; return $this; }

    public function getVideoUrl(): ?string { return $this->video_url; }
    public function setVideoUrl(?string $video_url): self { $this->video_url = $video_url; return $this; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(?string $content): self { $this->content = $content; return $this; }

    public function getPdfPath(): ?string { return $this->pdf_path; }
    public function setPdfPath(?string $pdf_path): self { $this->pdf_path = $pdf_path; return $this; }

    public function getDuration(): ?int { return $this->duration; }
    public function setDuration(?int $duration): self { $this->duration = $duration; return $this; }

    public function getLessonOrder(): ?int { return $this->lesson_order; }
    public function setLessonOrder(?int $lesson_order): self { $this->lesson_order = $lesson_order; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
}