<?php

namespace App\Entity;

use App\Repository\PaticipationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaticipationRepository::class)]
class Paticipation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    private ?Evenement $id_event = null;

    #[ORM\Column(length: 255)]
    private ?string $nomuser = null;

    /**
     * @return string|null
     */
    public function getNomuser(): ?string
    {
        return $this->nomuser;
    }

    /**
     * @param string|null $nomuser
     */
    public function setNomuser(?string $nomuser): void
    {
        $this->nomuser = $nomuser;
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdEvent(): ?Evenement
    {
        return $this->id_event;
    }

    public function setIdEvent(Evenement $id_event): self
    {
        $this->id_event = $id_event;

        return $this;
    }
}
