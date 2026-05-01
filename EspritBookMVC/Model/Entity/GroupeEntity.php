<?php

declare(strict_types=1);

/** Domain model for a group. Persistence: GroupeRepository. */
final class GroupeEntity
{
    private ?int $idGroupe;
    private string $nomGroupe;
    private string $description;
    private int $idCreateur;
    private string $approvalStatut;
    private string $statut;

    public function __construct(
        ?int $idGroupe,
        string $nomGroupe,
        string $description,
        int $idCreateur,
        string $approvalStatut = 'en_cours',
        string $statut = 'actif'
    ) {
        $this->idGroupe = $idGroupe;
        $this->nomGroupe = $nomGroupe;
        $this->description = $description;
        $this->idCreateur = $idCreateur;
        $this->approvalStatut = $approvalStatut;
        $this->statut = $statut;
    }

    public function getIdGroupe(): ?int
    {
        return $this->idGroupe;
    }

    public function setIdGroupe(?int $id): void
    {
        $this->idGroupe = $id;
    }

    public function getNomGroupe(): string
    {
        return $this->nomGroupe;
    }

    public function setNomGroupe(string $v): void
    {
        $this->nomGroupe = $v;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $v): void
    {
        $this->description = $v;
    }

    public function getIdCreateur(): int
    {
        return $this->idCreateur;
    }

    public function setIdCreateur(int $v): void
    {
        $this->idCreateur = $v;
    }

    public function getApprovalStatut(): string
    {
        return $this->approvalStatut;
    }

    public function setApprovalStatut(string $v): void
    {
        $this->approvalStatut = $v;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $v): void
    {
        $this->statut = $v;
    }
}
