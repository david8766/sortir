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
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idInscription;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_inscription", type="datetime", nullable=false)
     */
    private $dateInscription;

    /**
     * @ORM\ManyToOne (targetEntity="App\Entity\Sortie")
     *
     */
    private $sortie;

    /**
     * @ORM\ManyToOne (targetEntity="App\Entity\Participant")
     */
    private $participant;

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

    /**
     * @return mixed
     */
    public function getSortie()
    {
        return $this->sortie;
    }

    /**
     * @param mixed $sortie
     */
    public function setSortie($sortie): void
    {
        $this->sortie = $sortie;
    }

    /**
     * @return mixed
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * @param mixed $participant
     */
    public function setParticipant($participant): void
    {
        $this->participant = $participant;
    }




}
