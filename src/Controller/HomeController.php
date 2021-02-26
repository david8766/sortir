<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Sortie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/", name="accueil")
     */
    public function accueil(Request $request): Response
    {
        //Récupération de la session pour garder les données de formulaires
        $request->getSession();
        $campusActif = $this->session->get('campusActif', null);
        $dateDebut = $this->session->get('dateDebut', null);
        $dateFin = $this->session->get('dateFin', null);
        $recherche = $this->session->get('recherche',null);
        $sorties = $this->session->get('sorties', null);
        $organisateur = $this->session->get('organisateur', null);
        $etatSortiesPassees = $this->session->get('etatSortiesPassees', null);

        //Mise à jour de l'état des sorties et appel aux repositories
        $this->actualiserEtats();
        $campusList = $this->getDoctrine()->getRepository(Campus::class)->findAll();
        $sortiesRepo = $this->getDoctrine()->getRepository(Sortie::class);

        if($sorties == null){
            $sorties = $sortiesRepo->findAll();
        }
        //$insciptionRepo = $this->getDoctrine()->getRepository(Inscriptions::class);
        //$mesInscriptionsAuxSorties = $insciptionRepo->findBy(['participant' => $user]);

        //récupérer les données du formulaire de tri s'il est envoyé
        if($request->getMethod() ==='POST'){

            //Campus
            $campus = $request->get('campus');
            $campusActif = $this->getDoctrine()->getRepository(Campus::class)->find($campus);
            $this->session->set('campusActif',$campusActif);

            //Dates
            $dateDebut = $request->get('dateDebut');
            $this->session->set('dateDebut',$dateDebut);
            $dateFin = $request->get('dateFin');
            $this->session->set('dateFin',$dateFin);

            //Recherche par mot-clé
            $recherche = $request->get('rechercheParNom');
            $this->session->set('rechercheParNom',$recherche);

            //Recherche en fonction de l'état de la sortie
            $organisateur = $request->get('mesSortiesOrganisees');
            $this->session->set('organisateur', $organisateur);
            $etatSortiesPassees = $request->get('etatSortiesPassees');
            $this->session->set('etatSortiesPassees', $etatSortiesPassees);

            //Recherche en fonction des inscriptions
            $mesInscriptions = $request->get('mesInscriptions');
            $sortiesNonInscrit = $request->get('sortiesNonInscrit');


            //Effectuer les tris en fonction des paramètres de tri
            $sorties = $sortiesRepo->findByAllFilters($campus, $recherche, $dateDebut, $dateFin, $organisateur, $etatSortiesPassees);
            if ($sorties == null){
                $this->addFlash('noResults', 'Aucune sortie ne correspond à vos critères');
            }
            $this->session->set('sorties', $sorties);
        }

        return $this->render('home/accueil.html.twig',[
            "campusList"=>$campusList,
            "sorties"=>$sorties,
            "campusActif"=>$campusActif,
            "dateDebut"=>$dateDebut,
            "dateFin"=>$dateFin,
            "recherche"=>$recherche,
            "organisateur"=>$organisateur,
            "etatSortiesPassees"=>$etatSortiesPassees,
        ]);
    }

    private function actualiserEtats(){
        //Recalcule les états avant affichage
        $sortiesRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortiesRepo->updateEtats();
    }

}
