<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participants;
use App\Entity\Sorties;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {

        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/accueil", name="accueil")
     */
    public function accueil(): Response
    {
       /* $participant = $this->getUser();
        $initiales = $this->initiales($participant->getNom());*/
        $campus = $this->getDoctrine()->getRepository(Campus::class)->findAll();
        $sortiesRepo = $this->getDoctrine()->getRepository(Sorties::class);
        $sorties = $sortiesRepo->findAll();

        return $this->render('home/accueil.html.twig',[
            //"initiales"=>$initiales,
            "campus"=>$campus,
            "sorties"=>$sorties,
        ]);
    }


    function initiales($nom): string
    {
        $nom_initiale = ''; // déclare le recipient
        $n_mot = explode(" ",$nom);
        foreach($n_mot as $lettre){
            $nom_initiale .= $lettre{0}.'.';
        }
        return strtoupper($nom_initiale);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout (): Response
    {

    }
}
