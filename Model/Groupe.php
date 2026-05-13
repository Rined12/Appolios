<?php

class Groupe
{
    private ?int $id_groupe;
    private ?string $nom_groupe;
    private ?string $description;
    private ?string $date_creation;
    private ?int $id_createur;
    private ?string $statut;
    private ?string $approval_statut;
    private ?string $image_url;

    public function __construct(
        ?int $id_groupe = null,
        ?string $nom_groupe = null,
        ?string $description = null,
        ?string $date_creation = null,
        ?int $id_createur = null,
        ?string $statut = null,
        ?string $approval_statut = null,
        ?string $image_url = null
    ) {
        $this->id_groupe = $id_groupe;
        $this->nom_groupe = $nom_groupe;
        $this->description = $description;
        $this->date_creation = $date_creation;
        $this->id_createur = $id_createur;
        $this->statut = $statut;
        $this->approval_statut = $approval_statut;
        $this->image_url = $image_url;
    }

    public function getIdGroupe(): ?int { return $this->id_groupe; }
    public function setIdGroupe(?int $id_groupe): self { $this->id_groupe = $id_groupe; return $this; }

    public function getNomGroupe(): ?string { return $this->nom_groupe; }
    public function setNomGroupe(?string $nom_groupe): self { $this->nom_groupe = $nom_groupe; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getDateCreation(): ?string { return $this->date_creation; }
    public function setDateCreation(?string $date_creation): self { $this->date_creation = $date_creation; return $this; }

    public function getIdCreateur(): ?int { return $this->id_createur; }
    public function setIdCreateur(?int $id_createur): self { $this->id_createur = $id_createur; return $this; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(?string $statut): self { $this->statut = $statut; return $this; }

    public function getApprovalStatut(): ?string { return $this->approval_statut; }
    public function setApprovalStatut(?string $approval_statut): self { $this->approval_statut = $approval_statut; return $this; }

    public function getImageUrl(): ?string { return $this->image_url; }
    public function setImageUrl(?string $image_url): self { $this->image_url = $image_url; return $this; }
}

class GroupPost
{
    private ?int $id;
    private ?int $group_id;
    private ?int $user_id;
    private ?string $post_type;
    private ?string $content;
    private ?string $media_url;
    private ?string $media_kind;
    private ?string $created_at;

    public function __construct(
        ?int $id = null,
        ?int $group_id = null,
        ?int $user_id = null,
        ?string $post_type = null,
        ?string $content = null,
        ?string $media_url = null,
        ?string $media_kind = null,
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->group_id = $group_id;
        $this->user_id = $user_id;
        $this->post_type = $post_type;
        $this->content = $content;
        $this->media_url = $media_url;
        $this->media_kind = $media_kind;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getGroupId(): ?int { return $this->group_id; }
    public function setGroupId(?int $group_id): self { $this->group_id = $group_id; return $this; }

    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $user_id): self { $this->user_id = $user_id; return $this; }

    public function getPostType(): ?string { return $this->post_type; }
    public function setPostType(?string $post_type): self { $this->post_type = $post_type; return $this; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(?string $content): self { $this->content = $content; return $this; }

    public function getMediaUrl(): ?string { return $this->media_url; }
    public function setMediaUrl(?string $media_url): self { $this->media_url = $media_url; return $this; }

    public function getMediaKind(): ?string { return $this->media_kind; }
    public function setMediaKind(?string $media_kind): self { $this->media_kind = $media_kind; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
}

class GroupPostComment
{
    private ?int $id;
    private ?int $post_id;
    private ?int $user_id;
    private ?string $content;
    private ?string $created_at;

    public function __construct(
        ?int $id = null,
        ?int $post_id = null,
        ?int $user_id = null,
        ?string $content = null,
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->post_id = $post_id;
        $this->user_id = $user_id;
        $this->content = $content;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getPostId(): ?int { return $this->post_id; }
    public function setPostId(?int $post_id): self { $this->post_id = $post_id; return $this; }

    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $user_id): self { $this->user_id = $user_id; return $this; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(?string $content): self { $this->content = $content; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
}

class GroupPostReaction
{
    private ?int $id;
    private ?int $post_id;
    private ?int $user_id;
    private ?string $reaction;
    private ?string $created_at;

    public function __construct(
        ?int $id = null,
        ?int $post_id = null,
        ?int $user_id = null,
        ?string $reaction = null,
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->post_id = $post_id;
        $this->user_id = $user_id;
        $this->reaction = $reaction;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getPostId(): ?int { return $this->post_id; }
    public function setPostId(?int $post_id): self { $this->post_id = $post_id; return $this; }

    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $user_id): self { $this->user_id = $user_id; return $this; }

    public function getReaction(): ?string { return $this->reaction; }
    public function setReaction(?string $reaction): self { $this->reaction = $reaction; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
}
