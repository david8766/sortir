<?php

namespace App\Controller;

use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/participant", name="participant")
     */
    public function index(): Response
    {
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('participant/login.html.twig',[
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): Response
    {}

    /**
     * @Route("/monProfil", name="monProfil")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function modifierProfil(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $participant = $this->getUser();
        $participantForm = $this->createForm(ParticipantType::class,$participant);
        dump($participant);
        $participantForm->handleRequest($request);
        if($participantForm->isSubmitted() && $participantForm->isValid()){
            $hashed = $encoder->encodePassword($participant, $participant->getPassword());
            $participant->setMotDePasse($hashed);
            $em->persist($participant);
            $em->flush();
            $this->addFlash('success',"Votre modification est bien enregistrée!");
            $this->redirectToRoute('accueil');
        }

        return $this->render('participant/monProfil.html.twig',[
            "participantForm" => $participantForm->createView()
        ]);
    }


}
