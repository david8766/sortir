<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\AnnulationType;
use App\Form\SortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/", name="sortie_index", methods={"GET"})
     */
    public function index(): Response
    {
        $this->ActualiserEtats();

        $sorties = $this->getDoctrine()
            ->getRepository(Sortie::class)
            ->findAll();

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
        ]);
    }

    /**
     * @Route("/new", name="sortie_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {

        $sortie = new Sortie();

        $etat = $this->getDoctrine()
            ->getRepository(Etat::class)
            ->find(0);
        $sortie->setEtat($etat);

        $organisateur = $this->getUser();
        $sortie->setOrganisateur($organisateur);

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success','La sortie a été enregistrée.');

            return $this->render('sortie/edit.html.twig', [
                'sortie' => $sortie,
                'form' => $form->createView(),
            ]);

        }

        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    private function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/{noSortie}/edit", name="sortie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sortie $sortie): Response
    {
        $this->ActualiserEtats();

        $roles=$this->getUser()->getRoles();

        if($this->getUser() !== $sortie->getOrganisateur()
            || !in_array('ROLE_ADMIN',$roles)
            || $sortie->getEtat()->getId()>2) {
                return $this->show($sortie);
        }
         else{

             $form = $this->createForm(SortieType::class, $sortie);
             $form->handleRequest($request);

             if ($form->isSubmitted() && $form->isValid()) {
                 $this->getDoctrine()->getManager()->flush();

                 $this->addFlash('success','La sortie a été enregistrée.');
                 return $this->redirectToRoute('accueil');
             }

             return $this->render('sortie/edit.html.twig', [
                 'sortie' => $sortie,
                 'form' => $form->createView(),
             ]);
         }
    }

    /**
     * @Route("/{noSortie}", name="sortie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Sortie $sortie): Response
    {

        if($this->getUser() !== $sortie->getOrganisateur() ){
            $this->addFlash('error',"Vous n'êtes pas autorisé à supprimer cette sortie.");
            return $this->show($sortie);
        }

        $this->ActualiserEtats();

        if($sortie->getEtat()->getId() != 0) {
            $this->addFlash('error','Vous ne pouvez pas supprimer cette sortie.');
            return $this->show($sortie);
        }

        if ($this->isCsrfTokenValid('delete'.$sortie->getNoSortie(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sortie);
            $entityManager->flush();
            $this->addFlash('success','La sortie a été supprimée.');
        }

        return $this->redirectToRoute('accueil');
    }

    private function ActualiserEtats(){
        //Recalcule les états avant affichage
        $sortiesRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortiesRepo->updateEtats();
    }

    /**
     * @Route("/{noSortie}/publier", name="sortie_publier", methods={"PUBLIER"})
     */
    public function publier(Request $request, Sortie $sortie): Response
    {
        if($this->getUser() !== $sortie->getOrganisateur() ){
            $this->addFlash('error',"Vous n'êtes pas autorisé à publier cette sortie.");
            return $this->show($sortie);
        }

        if($sortie->getEtat()->getId() != 0) {
            $this->addFlash('error','Vous ne pouvez pas publier cette sortie.');
            return $this->show($sortie);
        }

        if ($this->isCsrfTokenValid('publier'.$sortie->getNoSortie(), $request->request->get('_token'))) {

            $etat = $this->getDoctrine()
                ->getRepository(Etat::class)
                ->find(1);
            $sortie->setEtat($etat);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success','La sortie a été publiée.');
        }

        return $this->redirectToRoute('accueil');

    }

    /**
     * @Route("/{noSortie}/annuler", name="sortie_annuler", methods={"ANNULER"})
     */
    public function annuler(Request $request, Sortie $sortie): Response
    {
        if($this->getUser() !== $sortie->getOrganisateur() ){
            $this->addFlash('error',"Vous n'êtes pas autorisé à annuler cette sortie.");
            return $this->show($sortie);
        }

        if($sortie->getEtat()->getId() != 1) {
            $this->addFlash('error','Vous ne pouvez pas annuler cette sortie.');
            return $this->show($sortie);
        }

        if ($this->isCsrfTokenValid('annuler'.$sortie->getNoSortie(), $request->request->get('_token'))) {

            // Enregistre les éventuelles modifications de la sortie
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            // Appelle la forme de confirmation (saisie du motif d'annulation)
            return $this->redirectToRoute('sortie_confirmer_annulation', array('noSortie'=>$sortie->getNoSortie()) );
        }

        return $this->edit($request,$sortie);

    }

    /**
     * @Route("/{noSortie}/confirmer_annulation", name="sortie_confirmer_annulation", methods={"GET","POST"})
     */
    public function annulerConfirmer(Request $request, Sortie $sortie): Response
    {
        if($this->getUser() !== $sortie->getOrganisateur() ){
            $this->addFlash('error',"Vous n'êtes pas autorisé à annuler cette sortie.");
            return $this->show($sortie);
        }

        if($sortie->getEtat()->getId() != 1) {
            $this->addFlash('error','Vous ne pouvez pas annuler cette sortie.');
            return $this->show($sortie);
        }

        //Définit l'état à "annulé"
        $etat = $this->getDoctrine()
            ->getRepository(Etat::class)
            ->find(5);
        $sortie->setEtat($etat);

        //Appelle la forme
        $form = $this->createForm(AnnulationType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success','La sortie a été annulée.');
            return $this->show($sortie);
        }

        return $this->render('sortie/annulation.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);

    }

}
