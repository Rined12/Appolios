<?php
class Lesson {
    private ?int $id;
    private ?int $chapter_id;
    private ?string $title;
    private ?string $content;
    private ?string $video_url;
    private ?string $pdf_path;
    private ?string $lesson_type;
    private ?int $lesson_order;
    private ?int $sort_order;

    public function __construct(
        ?int $id = null,
        ?int $chapter_id = null,
        ?string $title = null,
        ?string $content = null,
        ?string $video_url = null,
        ?string $pdf_path = null,
        ?string $lesson_type = null,
        ?int $lesson_order = null,
        ?int $sort_order = null
    ) {
        $this->id = $id;
        $this->chapter_id = $chapter_id;
        $this->title = $title;
        $this->content = $content;
        $this->video_url = $video_url;
        $this->pdf_path = $pdf_path;
        $this->lesson_type = $lesson_type;
        $this->lesson_order = $lesson_order;
        $this->sort_order = $sort_order;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getChapterId(): ?int { return $this->chapter_id; }
    public function setChapterId(?int $chapter_id): self { $this->chapter_id = $chapter_id; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $title): self { $this->title = $title; return $this; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(?string $content): self { $this->content = $content; return $this; }

    public function getVideoUrl(): ?string { return $this->video_url; }
    public function setVideoUrl(?string $video_url): self { $this->video_url = $video_url; return $this; }

    public function getPdfPath(): ?string { return $this->pdf_path; }
    public function setPdfPath(?string $pdf_path): self { $this->pdf_path = $pdf_path; return $this; }

    public function getLessonType(): ?string { return $this->lesson_type; }
    public function setLessonType(?string $lesson_type): self { $this->lesson_type = $lesson_type; return $this; }

    public function getLessonOrder(): ?int { return $this->lesson_order; }
    public function setLessonOrder(?int $lesson_order): self { $this->lesson_order = $lesson_order; return $this; }

    public function getSortOrder(): ?int { return $this->sort_order; }
    public function setSortOrder(?int $sort_order): self { $this->sort_order = $sort_order; return $this; }
}
