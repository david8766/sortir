<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EtatRepository")
 * @ORM\Table(name="etat")
 */

class Etat
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @Assert\Length(max=30, maxMessage="30 caractères maximum.")
     * @ORM\Column(name="libelle", type="string", length=30, nullable=false)
     */
    private $libelle;

    /**
     * Etat constructor.
     * @param $id
     */
    public function __construct(int $id= 0)
    {
        $this->id = $id;
        $this->libelle = '';
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param mixed $libelle
     */
    public function setLibelle($libelle): void
    {
        $this->libelle = $libelle;
    }

    public function __toString()
    {
        return $this->libelle;
    }

}