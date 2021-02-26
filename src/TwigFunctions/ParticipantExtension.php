<?php


namespace App\TwigFunctions;


use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


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

    public function isInscrit($listeInscriptions, $sortie): string
    {
        $isInscrit = "non inscrit";
        foreach ($listeInscriptions as $i){
            $sortieInscrite = $i->getSortie();
            if($sortie == $sortieInscrite){
                $isInscrit = "inscrit";
            }
        }
        return $isInscrit;
    }

    public function action($listeInscriptions, $sortie, $user):string
    {
        //Renvoi un code action en fonction de l'état de l'utilisateur vis à vis de la sortie
        // 0 pour ne rien faire (inscriptions non ouverte), 1 pour "s'inscrire", 2 pour "se désister", 3 pour "Annuler la sortie.
        $action = "0";
        foreach ($listeInscriptions as $i) {
            $sortieInscrite = $i->getSortie();
            $sortieOrganisateur = $sortieInscrite->getOrganisateur();
            $sortieEtat = $i->getEtat();
            if($sortieEtat == 1){
                $action = "1";
                if ($sortie == $sortieInscrite) {
                    $action = "2";
                }
                if ($sortieOrganisateur == $user){
                    $action = "3";
                }
            }
        }
        return $action;
    }


}