<?php

class Discussion
{
    private ?int $id_discussion;
    private ?string $titre;
    private ?string $contenu;
    private ?string $date_creation;
    private ?int $id_groupe;
    private ?int $id_auteur;
    private ?string $approval_statut;

    public function __construct(
        ?int $id_discussion = null,
        ?string $titre = null,
        ?string $contenu = null,
        ?string $date_creation = null,
        ?int $id_groupe = null,
        ?int $id_auteur = null,
        ?string $approval_statut = null
    ) {
        $this->id_discussion = $id_discussion;
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->date_creation = $date_creation;
        $this->id_groupe = $id_groupe;
        $this->id_auteur = $id_auteur;
        $this->approval_statut = $approval_statut;
    }

    public function getIdDiscussion(): ?int { return $this->id_discussion; }
    public function setIdDiscussion(?int $id_discussion): self { $this->id_discussion = $id_discussion; return $this; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(?string $titre): self { $this->titre = $titre; return $this; }

    public function getContenu(): ?string { return $this->contenu; }
    public function setContenu(?string $contenu): self { $this->contenu = $contenu; return $this; }

    public function getDateCreation(): ?string { return $this->date_creation; }
    public function setDateCreation(?string $date_creation): self { $this->date_creation = $date_creation; return $this; }

    public function getIdGroupe(): ?int { return $this->id_groupe; }
    public function setIdGroupe(?int $id_groupe): self { $this->id_groupe = $id_groupe; return $this; }

    public function getIdAuteur(): ?int { return $this->id_auteur; }
    public function setIdAuteur(?int $id_auteur): self { $this->id_auteur = $id_auteur; return $this; }

    public function getApprovalStatut(): ?string { return $this->approval_statut; }
    public function setApprovalStatut(?string $approval_statut): self { $this->approval_statut = $approval_statut; return $this; }
}
