<?php

namespace App\Entity;

use App\Repository\MaladieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaladieRepository::class)]
class Maladie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $NomMaladie = null;

    #[ORM\Column(length: 255)]
    private ?string $TypeMaladie = null;

    #[ORM\Column(length: 255)]
    private ?string $Symptoms = null;

    #[ORM\OneToMany(mappedBy: 'maladie', targetEntity: Traitement::class)]
    private Collection $traitements;

    public function __construct()
    {
        $this->traitements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomMaladie(): ?string
    {
        return $this->NomMaladie;
    }

    public function setNomMaladie(string $NomMaladie): self
    {
        $this->NomMaladie = $NomMaladie;

        return $this;
    }

    public function getTypeMaladie(): ?string
    {
        return $this->TypeMaladie;
    }

    public function setTypeMaladie(string $TypeMaladie): self
    {
        $this->TypeMaladie = $TypeMaladie;

        return $this;
    }

    public function getSymptoms(): ?string
    {
        return $this->Symptoms;
    }

    public function setSymptoms(string $Symptoms): self
    {
        $this->Symptoms = $Symptoms;

        return $this;
    }

    /**
     * @return Collection<int, Traitement>
     */
    public function getTraitements(): Collection
    {
        return $this->traitements;
    }

    public function addTraitement(Traitement $traitement): self
    {
        if (!$this->traitements->contains($traitement)) {
            $this->traitements->add($traitement);
            $traitement->setMaladie($this);
        }

        return $this;
    }

    public function removeTraitement(Traitement $traitement): self
    {
        if ($this->traitements->removeElement($traitement)) {
            // set the owning side to null (unless already changed)
            if ($traitement->getMaladie() === $this) {
                $traitement->setMaladie(null);
            }
        }

        return $this;
    }
}
