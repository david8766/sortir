<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Inscriptions;
use App\Entity\Sortie;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;


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
        //Mise à jour de l'état des sorties et appel aux repositories
        $this->actualiserEtats();
        $campusList = $this->getDoctrine()->getRepository(Campus::class)->findAll();
        $sortiesRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $inscriptionsRepo = $this->getDoctrine()->getRepository(Inscriptions::class);

        //Récupération de la session pour garder les données de formulaires
        $request->getSession();
        $campusActif = $this->session->get('campusActif', null);
        $dateDebut = $this->session->get('dateDebut', null);
        $dateFin = $this->session->get('dateFin', null);
        $recherche = $this->session->get('recherche',null);

        $organisateur = $this->session->get('organisateur', null);
        $sortiesCommeInscrit = $this->session->get('sortiesCommeInscrit', null);
        $sortiesNonInscrit = $this->session->get('sortiesNonInscrit', null);
        $etatSortiesPassees = $this->session->get('etatSortiesPassees', null);

        $sorties = $this->session->get('sorties', null);

        if($sorties == null){
            $sorties = $sortiesRepo->findAll();
        }

        //récupérer les données du formulaire de tri s'il est envoyé
        if($request->getMethod() ==='POST'){

            //Campus
            $campus = $request->get('campus');
            $campusActif = $this->getDoctrine()->getRepository(Campus::class)->find($campus);
            $this->session->set('campusActif',$campusActif);

            //Recherche par mot-clé
            $recherche = $request->get('rechercheParNom');
            $this->session->set('rechercheParNom',$recherche);

            //Dates
            $dateDebut = $request->get('dateDebut');
            $this->session->set('dateDebut',$dateDebut);
            $dateFin = $request->get('dateFin');
            $this->session->set('dateFin',$dateFin);

            //Recherche en fonction de l'état de la sortie
            $organisateur = $request->get('mesSortiesOrganisees');
            $this->session->set('organisateur', $organisateur);
            $etatSortiesPassees = $request->get('etatSortiesPassees');
            $this->session->set('etatSortiesPassees', $etatSortiesPassees);

            //Recherche en fonction des inscriptions
            $sortiesCommeInscrit = $request->get('sortiesCommeInscrit');
            $this->session->set('sortiesCommeInscrit', $sortiesCommeInscrit);

            $sortiesNonInscrit = $request->get('sortiesNonInscrit');
            $this->session->set('sortiesNonInscrit', $sortiesNonInscrit);

            //Effectuer les tris en fonction des paramètres de tri
            $sorties = $sortiesRepo->findByAllFilters(
                $campusActif,
                $recherche,
                $dateDebut,
                $dateFin,
                $organisateur,
                $etatSortiesPassees,
                $sortiesCommeInscrit,
                $sortiesNonInscrit);

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
            "sortiesCommeInscrit"=>$sortiesCommeInscrit,
            "sortiesNonInscrit"=>$sortiesNonInscrit,
        ]);
    }

    private function actualiserEtats(){
        //Recalcule les états avant affichage
        $sortiesRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortiesRepo->updateEtats();
    }

}
