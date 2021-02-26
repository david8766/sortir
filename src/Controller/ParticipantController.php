<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\NewPasswordType;
use App\Form\ParticipantType;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/afficherProfil/{id}", name="afficherProfil")
     * @param $id
     * @return Response
     */
    public function afficherProfil($id): Response
    {
        $participantRepo = $this->getDoctrine()->getRepository(Participant::class);
        $participant = $participantRepo->find($id);
        return $this->render('participant/afficherProfil.html.twig',compact('participant'));
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


    /**
     * @Route("/motDePasseOublie", name="motDePasseOublie")
     * @param Request $request
     * @param Swift_Mailer $mailer
     * @param TokenGeneratorInterface $tokenGenerator
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function motDePasseOublie(Request $request, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(ResetPasswordType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $data = $form->getData();
            $participantRepo = $this->getDoctrine()->getRepository(Participant::class);
            $participant = $participantRepo->findOneBy(['mail' => $data['email']]);
            dump($participant);
            if($participant === null){
                $this->addFlash('danger',"Votre adresse email n'existe pas dans notre base de données!");
            } else {
                $token = $tokenGenerator->generateToken();
                $participant->setResetToken($token);
                $em->persist($participant);
                $em->flush();

                // Création de l'URL de réinitialisation de mot de passe
                $url = $this->generateUrl('reinitialisationMotDePasse', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                // Création de l'e-mail
                $message = (new \Swift_Message('Sortir.com - Mot de passe oublié'))
                    ->setFrom('reset_password@sortir.com')
                    ->setTo($participant->getMail())
                    ->setBody(
                        "<h1>Bonjour!</h1>
                        <p>Une demande de réinitialisation de mot de passe a été effectuée pour le site Sortir.com.</p>
                        <p>Veuillez cliquer sur le lien suivant : " . $url . '</p>',
                        'text/html'
                    )
                ;
                // Envoi de l'email
                $mailer->send($message);
                // Message
                $this->addFlash('success', 'Un email pour la réinitialisation de votre mot de passe vous a été envoyé !');
                // Et redirection vers la page de connexion
                return $this->redirectToRoute('login');
            }

        }

        return $this->render('participant/motDePasseOublie.html.twig',['emailForm' => $form->createView()]);
    }

    /**
     * @Route("/reinitialisationMotDePasse/{token}", name="reinitialisationMotDePasse")
     * @param Request $request
     * @param $token
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function reinitialisationMotDePasse($token,Request $request,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em) :Response
    {

        $participantRepo = $this->getDoctrine()->getRepository(Participant::class);
        $participant = $participantRepo->findOneBy(['reset_token' => $token]);
        //dump($participant);
        if ($participant === null) {

            $this->addFlash('danger', 'Vous n\'êtes pas reconnu.');
            return $this->redirectToRoute('login');

        } else {

            $form = $this->createForm(NewPasswordType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $newPassword = $request->request->get('new_password');
                $hashed = $encoder->encodePassword($participant, $newPassword["password"]["first"]);
                $participant->setMotDePasse($hashed);
                $participant->setResetToken(null);

                $em->persist($participant);
                $em->flush();

                $this->addFlash('success',"Votre mot de passe est bien enregistrée!");
                return $this->redirectToRoute('login');
            }

        }

        return $this->render('participant/reinitialisationMotDePasse.html.twig',['passwordForm' => $form->createView(),'token' => $token]);
    }


}
