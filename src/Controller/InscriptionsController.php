<?php

namespace App\Controller;

use App\Entity\Inscriptions;
use App\Entity\Sortie;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Route("/inscriptions")
 */
class InscriptionsController extends AbstractController
{

    /**
     * @Route("/sinscrire{noSortie}", name="inscription")
     */
    public function sInscrire(Request $request): Response
    {
        $inscription = new Inscriptions();
        $noSortie = $request->get('noSortie', null);
        $sortie = $this->getDoctrine()->getRepository(Sortie::class)->find($noSortie);
        $listeInscriptions = new ArrayCollection();
        $listeInscriptions = $sortie->getInscriptions();
        $inscription->setDateInscription(new DateTime());
        $inscription->setParticipant($this->getUser());
        $inscription->setSortie($sortie);
        $nbParticipants = count($sortie->getInscriptions());
        if($nbParticipants < $sortie->getNbInscriptionsMax()){
            foreach ($listeInscriptions as $i){
                $participant = $i->getParticipant();
                if($participant = $this->getUser()){
                    $this->addFlash('error', 'Vous êtes déjà inscrits pour cette sortie');
                    return $this->redirectToRoute('accueil');
                }
            }
            $em = $this->getDoctrine()->getManager();
            try {
                $em->persist($inscription);
                $em->flush();
                $this->addFlash('success','Votre inscription a bien été prise en compte');
            }catch(Exception $e){
                $this->addFlash('error','Votre inscription a échoué');
            }
        }else{
            $this->addFlash('error','Inscription impossible : la sortie est complète');
        }
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("/{noSortie}", name="seDesister")
     */
    public function seDesister(Request $request): Response
    {
        $noSortie = $request->get('noSortie',null);
        $idInscription = null;
        $sortie = $this->getDoctrine()->getRepository(Sortie::class)->find($noSortie);
        $listeInscriptions = $sortie->getInscriptions();
        foreach ($listeInscriptions as $i){
            $inscrit = $i->getParticipant();
            if($inscrit == $this->getUser()){
                $idInscription = $i->getIdInscription();
            }
        }
        if ($idInscription == null){
            $this->addFlash('error','Vous n\'êtes plus inscrit à cette sortie');
            return $this->redirectToRoute('accueil');
        }
        $inscription = $this->getDoctrine()->getRepository(Inscriptions::class)->find($idInscription);
        $entityManager = $this->getDoctrine()->getManager();
        try {
            $entityManager->remove($inscription);
            $entityManager->flush();
            $this->addFlash('success','Votre désistement a bien été pris en compte !');
        }catch(Exception $e){
            $this->addFlash('error', 'Votre désistement a échoué !');
        }
        return $this->redirectToRoute('accueil');
    }


}
