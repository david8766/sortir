<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function accueil(Request $request): Response
    {
        $participant = $this->getUser();
        $initiales = $this->initiales($participant->getNom());
        $campusList = $this->getDoctrine()->getRepository(Campus::class)->findAll();
        $sortiesRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sorties = $sortiesRepo->findAll();

        //récupérer les données du formulaire de tri s'il est envoyé
        if($request->getMethod() ==='POST'){
            $campus = $request->get('campus');
            $dateDebut = $request->get('dateDebut');
            $dateFin = $request->get('dateFin');
            $mesSortiesOrganisees = $request->get('mesSortiesOrganisees');
            $mesInscriptions = $request->get('mesInscriptions');
            $sortiesNonInscrit = $request->get('sortiesNonInscrit');
            $sortiesPassees = $request->get('sortiesPassees');

            //Effectuer les tris en fonction des paramètres de tri
            //Tri en fonction du campus et des dates
            $sorties = $sortiesRepo->findByCampusAndDates($campus, $dateDebut, $dateFin);



        }

        return $this->render('home/accueil.html.twig',[
            "initiales"=>$initiales,
            "campusList"=>$campusList,
            "sorties"=>$sorties,
        ]);
    }


    function initiales($nom): string
    {

        $nom_initiale = ''; // déclare le recipient
        $n_mot = explode(" ",$nom);
        foreach($n_mot as $lettre){
            $nom_initiale .= $lettre[0].'.';
        }
        return strtoupper($nom_initiale);
    }

}
