<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inscriptions
 *
 * @ORM\Table(name="inscriptions")
 * @ORM\Entity(repositoryClass="App\Repository\InscriptionsRepository")
 *
 */
class Inscriptions
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_inscription", type="integer", nullable=false)
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idInscription;

    /**
     * @var \DateTime
     * @ORM\Id
     * @ORM\Column(name="date_inscription", type="datetime", nullable=false)
     */
    private $dateInscription;

    /**
     * @ORM\ManyToOne(targetEntity=Sortie::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false, name="sortie_id", referencedColumnName="no_sortie")
     */
    private $sortie;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false, name="particpant_id", referencedColumnName="id")
     */
    private $participant;

    public function getSortie(): ?Sortie
    {
        return $this->sortie;
    }

    public function setSortie(?Sortie $sortie): self
    {
        $this->sortie = $sortie;

        return $this;
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function setParticipant(?Participant $participant): self
    {
        $this->participant = $participant;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdInscription(): int
    {
        return $this->idInscription;
    }

    /**
     * @param int $idInscription
     */
    public function setIdInscription(int $idInscription): void
    {
        $this->idInscription = $idInscription;
    }

    /**
     * @return \DateTime
     */
    public function getDateInscription(): \DateTime
    {
        return $this->dateInscription;
    }

    /**
     * @param \DateTime $dateInscription
     */
    public function setDateInscription(\DateTime $dateInscription): void
    {
        $this->dateInscription = $dateInscription;
    }



    
}
