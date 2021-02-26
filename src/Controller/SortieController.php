<?php

namespace App\Controller;

use App\Entity\Sortie;
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

        $organisateur = $this->getUser();;
        $sortie->setOrganisateur($organisateur);

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success','La sortie a été enregistrée.');

            return $this->redirectToRoute('sortie_index');
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

        if($sortie->getEtat()->getId()>2) {
            return $this->show($sortie);
        }
         else{
             $form = $this->createForm(SortieType::class, $sortie);
             $form->handleRequest($request);

             if ($form->isSubmitted() && $form->isValid()) {
                 $this->getDoctrine()->getManager()->flush();

                 return $this->redirectToRoute('sortie_index');
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
        $this->ActualiserEtats();

        if ($this->isCsrfTokenValid('delete'.$sortie->getNoSortie(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sortie);
            $entityManager->flush();
            $this->addFlash('success','La sortie a été supprimée.');
        }

        return $this->redirectToRoute('sortie_index');
    }

    private function ActualiserEtats(){
        //Recalcule les états avant affichage
        $sortiesRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortiesRepo->updateEtats();
    }

}