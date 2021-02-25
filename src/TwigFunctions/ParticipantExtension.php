<?php


namespace App\TwigFunctions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class ParticipantExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('initiales', [$this, 'getInitiales']),
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
}