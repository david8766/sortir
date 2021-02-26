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
        $inscription->setDateInscription(new DateTime());
        $inscription->setParticipant($this->getUser());
        $inscription->setSortie($sortie);

        $em = $this->getDoctrine()->getManager();
        try {
            $em->persist($inscription);
            $em->flush();
            $this->addFlash('success','Votre inscription a bien été prise en compte');
        }catch(Exception $e){
            $this->addFlash('error','Votre inscription a échoué');
        }
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("/{idInscription}", name="seDesister")
     */
    public function seDesister(Request $request): Response
    {
        $id = $request->get('idInscription',null);
        $inscription = $this->getDoctrine()->getRepository(Inscriptions::class)->find($id);
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
