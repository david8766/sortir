<?php

namespace App\Controller;

use App\Entity\Inscriptions;
use App\Form\Inscriptions2Type;
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
     * @Route("/", name="inscriptions_index", methods={"GET"})
     */
    public function index(): Response
    {
        $inscriptions = $this->getDoctrine()
            ->getRepository(Inscriptions::class)
            ->findAll();

        return $this->render('inscriptions/index.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }

    /**
     * @Route("/new", name="inscriptions_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $inscription = new Inscriptions();
        $form = $this->createForm(Inscriptions2Type::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($inscription);
            $entityManager->flush();

            return $this->redirectToRoute('inscriptions_index');
        }

        return $this->render('inscriptions/new.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{dateInscription}", name="inscriptions_show", methods={"GET"})
     */
    public function show(Inscriptions $inscription): Response
    {
        return $this->render('inscriptions/show.html.twig', [
            'inscription' => $inscription,
        ]);
    }

    /**
     * @Route("/{dateInscription}/edit", name="inscriptions_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Inscriptions $inscription): Response
    {
        $form = $this->createForm(Inscriptions2Type::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('inscriptions_index');
        }

        return $this->render('inscriptions/edit.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{dateInscription}", name="inscriptions_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Inscriptions $inscription): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inscription->getDateInscription(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($inscription);
            $entityManager->flush();
        }

        return $this->redirectToRoute('inscriptions_index');
    }
}
