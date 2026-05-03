<?php
/**
 * CATEGORY MODEL
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class Category extends BaseModel {
    protected string $table = 'categories';
    protected string $primaryKey = 'id';

    private ?int $id;
    private ?string $name;
    private ?string $description;
    private ?string $icon;
    private ?string $types;
    private ?string $created_at;

    public function __construct(
        ?int $id = null,
        ?string $name = null,
        ?string $description = null,
        ?string $icon = null,
        ?string $types = null,
        ?string $created_at = null
    ) {
        parent::__construct();
        
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->icon = $icon;
        $this->types = $types;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getName(): ?string { return $this->name; }
    public function setName(?string $name): self { $this->name = $name; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getIcon(): ?string { return $this->icon; }
    public function setIcon(?string $icon): self { $this->icon = $icon; return $this; }

    public function getTypes(): ?string { return $this->types; }
    public function setTypes(?string $types): self { $this->types = $types; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
}