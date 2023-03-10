<?php

namespace App\Entity;

use App\Repository\TraitementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TraitementRepository::class)]
class Traitement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $NomTraitement = null;

    #[ORM\Column(length: 255)]
    private ?string $TypeTraitement = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateDebutTraitement = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateFinTraitement = null;

    #[ORM\Column(length: 255)]
    private ?string $Description = null;

    #[ORM\ManyToOne(inversedBy: 'traitements')]
    private ?Maladie $maladie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomTraitement(): ?string
    {
        return $this->NomTraitement;
    }

    public function setNomTraitement(string $NomTraitement): self
    {
        $this->NomTraitement = $NomTraitement;

        return $this;
    }

    public function getTypeTraitement(): ?string
    {
        return $this->TypeTraitement;
    }

    public function setTypeTraitement(string $TypeTraitement): self
    {
        $this->TypeTraitement = $TypeTraitement;

        return $this;
    }

    public function getDateDebutTraitement(): ?\DateTimeInterface
    {
        return $this->DateDebutTraitement;
    }

    public function setDateDebutTraitement(\DateTimeInterface $DateDebutTraitement): self
    {
        $this->DateDebutTraitement = $DateDebutTraitement;

        return $this;
    }

    public function getDateFinTraitement(): ?\DateTimeInterface
    {
        return $this->DateFinTraitement;
    }

    public function setDateFinTraitement(\DateTimeInterface $DateFinTraitement): self
    {
        $this->DateFinTraitement = $DateFinTraitement;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getMaladie(): ?Maladie
    {
        return $this->maladie;
    }

    public function setMaladie(?Maladie $maladie): self
    {
        $this->maladie = $maladie;

        return $this;
    }
}
