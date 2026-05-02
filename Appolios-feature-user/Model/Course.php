<?php
/**
 * APPOLIOS Course Model
 * Entity class - handles course data
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class Course extends BaseModel {
    protected string $table = 'courses';

    // ==========================================
    // PROPERTIES
    // ==========================================
    private ?int $id;
    private ?string $title;
    private ?string $description;
    private ?string $video_url;
    private ?string $image;
    private ?float $price;
    private ?string $status;
    private ?string $admin_message;
    private ?int $created_by;
    private ?string $created_at;

    // ==========================================
    // CONSTRUCTOR
    // ==========================================
    public function __construct(
        ?int $id = null,
        ?string $title = null,
        ?string $description = null,
        ?string $video_url = null,
        ?string $image = null,
        ?float $price = null,
        ?string $status = null,
        ?string $admin_message = null,
        ?int $created_by = null,
        ?string $created_at = null
    ) {
        parent::__construct();
        
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->video_url = $video_url;
        $this->image = $image;
        $this->price = $price;
        $this->status = $status;
        $this->admin_message = $admin_message;
        $this->created_by = $created_by;
        $this->created_at = $created_at;
    }

    // ==========================================
    // GETTERS & SETTERS
    // ==========================================
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $title): self { $this->title = $title; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getVideoUrl(): ?string { return $this->video_url; }
    public function setVideoUrl(?string $video_url): self { $this->video_url = $video_url; return $this; }

    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): self { $this->image = $image; return $this; }

    public function getPrice(): ?float { return $this->price; }
    public function setPrice(?float $price): self { $this->price = $price; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(?string $status): self { $this->status = $status; return $this; }

    public function getAdminMessage(): ?string { return $this->admin_message; }
    public function setAdminMessage(?string $admin_message): self { $this->admin_message = $admin_message; return $this; }

    public function getCreatedBy(): ?int { return $this->created_by; }
    public function setCreatedBy(?int $created_by): self { $this->created_by = $created_by; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
    
    // Delegate methods for backward compatibility
    public function getAllWithCreator() {
        require_once __DIR__ . '/../Controller/CourseController.php';
        $ctrl = new CourseController();
        return $ctrl->getAllWithCreator();
    }
    
    public function getWithChapters($id) {
        require_once __DIR__ . '/../Controller/CourseController.php';
        $ctrl = new CourseController();
        return $ctrl->getWithChapters($id);
    }
    
    public function findById($id) {
        require_once __DIR__ . '/../Controller/CourseController.php';
        $ctrl = new CourseController();
        return $ctrl->findById($id);
    }
}