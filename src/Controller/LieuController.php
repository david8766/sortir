<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lieu")
 */
class LieuController extends AbstractController
{
    /**
     * @Route("/", name="lieu_index", methods={"GET"})
     */
    public function index(LieuRepository $lieuRepository): Response
    {
        return $this->render('lieu/index.html.twig', [
            'lieus' => $lieuRepository->findBy([],['nomLieu'=>'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="lieu_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {

        $this->denyAccessUnlessGranted("ROLE_ORGANISATEUR");

        $lieu = new Lieu();

        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success','Le lieu a été enregistré.');

            return $this->redirectToRoute('lieu_index');
        }

        return $this->render('lieu/new.html.twig', [
            'lieu' => $lieu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lieu_show", methods={"GET"})
     */
    public function show(Lieu $lieu): Response
    {
        return $this->render('lieu/show.html.twig', [
            'lieu' => $lieu,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lieu_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Lieu $lieu): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ORGANISATEUR");

        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success','Le lieu a été enregistré.');

            return $this->redirectToRoute('lieu_index');
        }

        return $this->render('lieu/edit.html.twig', [
            'lieu' => $lieu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lieu_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Lieu $lieu): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lieu->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($lieu);
            $entityManager->flush();

            $this->addFlash('success','Le lieu a été supprimé.');
        }

        return $this->redirectToRoute('lieu_index');
    }

    /**
     * @Route("/ajax/adresse", name="ajax_adresse")
     */
    public function ajaxGetAdresse(Request $request)
    {
        /*
        $lieux = $this->getDoctrine()
            ->getRepository(Lieu::class)
            ->findAll();
        */
        //if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
        if ($request->isXmlHttpRequest()){
            return new JsonResponse(array('data' => 'this is a json response'));
            /*
            $jsonData = array();
            $idx = 0;
            foreach ($lieux as $lieu) {
                $temp = array(
                    'nom' => $lieu->getNomLieu(),
                    'rue' => $lieu->getRue(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
            */
        } else {
            return new Response('Erreur Ajax !', 400);
        }
    }

}
