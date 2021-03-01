<?php

namespace App\Entity;

use DateInterval;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SortieRepository")
 * @ORM\Table(name="sortie")
 */
class Sortie
{
    /**
     * @ORM\Column(name="no_sortie", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $noSortie;

    /**
     * @Assert\NotBlank(message="Veuillez indiquer un nom pour la sortie.")
     * @Assert\Length(max=30, maxMessage="30 caractères maximum.")
     * @ORM\Column(name="nom", type="string", length=30, nullable=false)
     */
    private $nom;

    /**
     * @ORM\Column(name="date_heure_debut", type="datetime", nullable=false)
     */
    private $dateHeureDebut;

    /**
     * @Assert\Positive(message="La valeur doit être supérieure à zéro")
     * @ORM\Column(name="duree", type="integer", nullable=true)
     */
    private $duree;

    /**
     * @ORM\Column(name="date_cloture", type="date", nullable=false)
     */
    private $dateCloture;

    /**
     * @Assert\Positive(message="La valeur doit être supérieure à zéro")
     * @ORM\Column(name="nb_inscriptions_max", type="integer", nullable=false)
     */
    private $nbInscriptionsMax;

    /**
     * @Assert\NotBlank(message="Veuillez décrire la sortie.")
     * @Assert\Length(max=500, maxMessage="500 caractères maximum.")
     * @ORM\Column(name="description", type="string", length=500, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etat")
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Participant")
     */
    private $organisateur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Campus")
     */
    private $campus;

    /**
     * @ORM\OneToMany(targetEntity=Inscriptions::class, mappedBy="sortie", orphanRemoval=true)
     */
    private $inscriptions;

    /**
     * @Assert\Length(max=250, maxMessage="250 caractères maximum.")
     * @ORM\Column(name="motif_annulation", type="string", length=250, nullable=true)
     */
    private $motifAnnulation;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }



    /**
     * @return mixed
     */
    public function getNoSortie()
    {
        return $this->noSortie;
    }

    /**
     * @param mixed $noSortie
     */
    public function setNoSortie($noSortie): void
    {
        $this->noSortie = $noSortie;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getDateHeureDebut()
    {
        return $this->dateHeureDebut;
    }

    /**
     * @param mixed $dateHeureDebut
     */
    public function setDateHeureDebut($dateHeureDebut): void
    {
        $this->dateHeureDebut = $dateHeureDebut;
    }

    /**
     * @return mixed
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * @param mixed $duree
     */
    public function setDuree($duree): void
    {
        $this->duree = $duree;
    }

    /**
     * @return mixed
     */
    public function getDateCloture()
    {
        return $this->dateCloture;
    }

    /**
     * @param mixed $dateCloture
     */
    public function setDateCloture($dateCloture): void
    {
        $this->dateCloture = $dateCloture;
    }

    /**
     * @return mixed
     */
    public function getNbInscriptionsMax()
    {
        return $this->nbInscriptionsMax;
    }

    /**
     * @param mixed $nbInscriptionsMax
     */
    public function setNbInscriptionsMax($nbInscriptionsMax): void
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param mixed $etat
     */
    public function setEtat($etat): void
    {
        $this->etat = $etat;
    }

    /**
     * @return mixed
     */
    public function getOrganisateur()
    {
        return $this->organisateur;
    }

    /**
     * @param mixed $organisateur
     */
    public function setOrganisateur($organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    /**
     * @return mixed
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param mixed $campus
     */
    public function setCampus($campus): void
    {
        $this->campus = $campus;
    }



    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {

        // Vérifie que la date de début n'est pas passée
        $now = new \DateTime();
        if ($this->getDateHeureDebut()<=$now){
            $context->buildViolation("La date est passée.")
                ->atPath('dateHeureDebut')
                ->addViolation();
        }

        // Vérifie que la date de cloture des inscriptions est inférieure à la date de la sortie
        // et supérieure à aujourd'hui
        $now = new \DateTime();
        if ($this->getDateCloture() > $this->getDateHeureDebut()
            || $this->getDateCloture()<$now){
            $context->buildViolation("La date est incorrecte.")
                ->atPath('dateCloture')
                ->addViolation();
        }

    }

    /**
     * @return Collection|Inscriptions[]
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscriptions $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions[] = $inscription;
            $inscription->setSortie($this);
        }

        return $this;
    }

    public function removeInscription(Inscriptions $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getSortie() === $this) {
                $inscription->setSortie(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMotifAnnulation()
    {
        return $this->motifAnnulation;
    }

    /**
     * @param mixed $motifAnnulation
     */
    public function setMotifAnnulation($motifAnnulation): void
    {
        $this->motifAnnulation = $motifAnnulation;
    }

}
