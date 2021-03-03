<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Form\CsvFileType;
use App\Form\ParticipantType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/creerUnUtilisateur", name="admin.create.user")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function creerUnUtilisateur(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, FileUploader $fileUploader): Response
    {
        $participant = new Participant();
        $participantForm = $this->createForm(ParticipantType::class,$participant);
        $participantForm->handleRequest($request);

        if($participantForm->isSubmitted() && $participantForm->isValid()){
            $hashed = $encoder->encodePassword($participant, $participant->getPassword());
            $participant->setMotDePasse($hashed);
            $participant->setActif(true);
            $participant->setRoles(['ROLE_PARTICIPANT']);

            // Ajout photo de profil
            $imageFile = $participantForm->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $participant->setImageFilename($imageFileName);
            }

            $em->persist($participant);
            $em->flush();
            $this->addFlash('success',"Le nouvel utilisateur est bien enregistrée!");
        }

        return $this->render('admin/creerUnUtilisateur.html.twig',["participantForm" => $participantForm->createView(),"participant" => $participant]);
    }

    /**
     * @Route("/admin/listeDesUtilisateurs", name="admin.users.list")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function usersList(Request $request, PaginatorInterface $paginator): Response
    {
        $participantRepo = $this->getDoctrine()->getRepository(Participant::class);
        $users = $participantRepo->findAll();

        $total = count($users);

        $users = $paginator->paginate(
            $users,
            $request->query->getInt('page',1),
            15
        );
        $current_page = 'admin.users.list';

        return $this->render('admin/listeDesUtilisateurs.html.twig',["users" => $users,'total' => $total,'current_page' => $current_page]);
    }

    /**
     * @Route("/admin/telechargerCSV", name="admin.csv")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function usersCSV(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CsvFileType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $csv = $form->get('csv')->getData();
            $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
            $data = $serializer->decode(file_get_contents($csv), 'csv');
            foreach ($data as $user){
                $participant = new Participant();
                $participant->setPseudo($user['pseudo']);
                $participant->setNom($user['nom']);
                $participant->setPrenom($user['prenom']);
                $participant->setTelephone($user['telephone']);
                $participant->setMail($user['mail']);
                $hashed = $encoder->encodePassword($participant, $user['motdepasse']);
                $participant->setMotDePasse($hashed);
                $participant->setActif($user['actif']);
                $campusRepo = $this->getDoctrine()->getRepository(Campus::class);
                $campus = $campusRepo->find($user['campus']);
                $participant->setCampus($campus);
                $participant->setRoles(['ROLE_PARTICIPANT']);

                $em->persist($participant);
            }
            $em->flush();
            $this->addFlash('success',"Les nouveaux utilisateurs sont bien enregistrés!");
            return $this->redirectToRoute('admin.users.list');
        }
        return $this->render('admin/telechargerCSV.html.twig',['csvForm' => $form->createView()]);
    }

    /**
     * @Route("/admin/supprimerParticipant/{id}", name="delete.user")
     * @param Participant $participant
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function supprimerUnUtilisateur(Participant $participant, EntityManagerInterface $em): Response
    {
        $em->remove($participant);
        $em->flush();
        $this->addFlash('success','Le participant a bien été supprimé.');
        return $this->redirectToRoute('admin.users.list');
    }

    /**
     * @Route("/admin/desactiverParticipant/{id}", name="deactivate.user")
     * @param Participant $participant
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function desactiverUnUtilisateur(Participant $participant, EntityManagerInterface $em): Response
    {
        $participant->setRoles(['']);
        $participant->setActif(false);
        $em->flush();
        $this->addFlash('success','Le participant a bien été désactivé.');
        return $this->redirectToRoute('admin.users.list');
    }

    /**
     * @Route("/admin/activerParticipant/{id}", name="activate.user")
     * @param Participant $participant
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function activerUnUtilisateur(Participant $participant, EntityManagerInterface $em): Response
    {
        $participant->setRoles(['ROLE_PARTICIPANT']);
        $participant->setActif(true);
        $em->flush();
        $this->addFlash('success','Le participant a bien été activé.');
        return $this->redirectToRoute('admin.users.list');
    }

}
