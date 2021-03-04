<?php


namespace App\TwigFunctions;



use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Doctrine\Common\Collections\ArrayCollection;


class ParticipantExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('initiales', [$this, 'getInitiales',]),
            new TwigFunction('isInscrit', [$this, 'isInscrit']),
            new TwigFunction('action', [$this, 'action']),
        ];
    }

   public function getInitiales($nom): string
    {
        $nom_initiale = ''; // déclare le recipient
        $n_mot = explode(" ",$nom);
        foreach($n_mot as $lettre){
            $nom_initiale .= $lettre[0].'.';
        }
        return strtoupper($nom_initiale);
    }


    public function isInscrit($sortie, $user): string
    {
        $listeInscriptions = $sortie->getInscriptions();
        $isInscrit = "non inscrit";
        foreach ($listeInscriptions as $i){
            $participant = $i->getParticipant();
            if($participant == $user){
                $isInscrit = "inscrit";
            }
        }
        return $isInscrit;

    }

    public function action($sortie, $user):int
    {
        //Renvoi un code action en fonction de l'état de l'utilisateur vis à vis de la sortie
        // 0 pour ne rien faire (inscriptions non ouverte), 1 pour "s'inscrire", 2 pour "se désister", 3 pour "Annuler la sortie.
        $sortieEtat = $sortie->getEtat()->getId();
        $listeInscriptions = $sortie->getInscriptions();
        if($sortieEtat == 1){
            $action = 1;
            foreach ($listeInscriptions as $i) {
                $sortieInscrite = $i->getSortie();
                $sortieParticipant = $i->getParticipant();
                $sortieOrganisateur = $sortieInscrite->getOrganisateur();
                if ($sortieOrganisateur == $user) {
                    $action = 3;
                }elseif ($sortie == $sortieInscrite && $sortieParticipant == $user) {
                    $action = 2;
                }

            }
        }else{
                $action = 0;
            }
        return $action;
    }





}