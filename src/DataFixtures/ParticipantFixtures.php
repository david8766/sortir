<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ParticipantFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {

        // Création de données fictives pour ajouter des campus en base de données.

        $campus1 = new Campus();
        $campus1->setNom("Campus de Nantes");

        $campus2 = new Campus();
        $campus2->setNom("Campus de Rennes");

        $campus3 = new Campus();
        $campus3->setNom("Campus de Quimper");

        $campus4 = new Campus();
        $campus4->setNom("Campus de Niort");

        // $product = new Product();
        $manager->persist($campus1);
        $manager->persist($campus2);
        $manager->persist($campus3);
        $manager->persist($campus4);

        $manager->flush();

        $campus = [$campus1,$campus2,$campus3,$campus4];

        // Création de données fictives pour ajouter des participants en base de données.

        // Un administrateur
        $admin = new Participant();
        $admin->setCampus($campus1);
        $admin->setPseudo("admin");
        $admin->setNom("Martin");
        $admin->setPrenom("Jean");
        $admin->setTelephone("06-12-34-56-78");
        $admin->setMail("admin@gmail.com");
        $admin->setMotDePasse(($this->passwordEncoder->encodePassword($admin, 'password')));
        $admin->setAdministrateur(true);
        $admin->setActif(true);

        $manager->persist($admin);

        // Des participants

        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 20; $i++){
            $participant = new Participant();
            $participant->setCampus($campus[mt_rand(0,3)]);
            $participant->setPseudo('pseudo' . $i);
            $participant->setNom($faker->firstName());
            $participant->setPrenom($faker->lastName);
            $participant->setTelephone($faker->phoneNumber);
            $participant->setMail($faker->email);
            $participant->setMotDePasse(($this->passwordEncoder->encodePassword($admin, 'password')));
            $participant->setAdministrateur(false);
            $participant->setActif(true);

            $manager->persist($participant);
        }

        $manager->flush();

        // Création les états réels (non modifiables)
        for ($i = 0; $i <= 6; $i++){
            $etat = new Etat();
            $etat->setId($i);
            switch ($i){
                case 0:
                    $etat->setLibelle('Création');
                    break;
                case 1:
                    $etat->setLibelle('Inscription ouverte');
                    break;
                case 2:
                    $etat->setLibelle('Inscription clôturée');
                    break;
                case 3:
                    $etat->setLibelle('En cours');
                    break;
                case 4:
                    $etat->setLibelle('Passée');
                    break;
                case 5:
                    $etat->setLibelle('Annulée');
                    break;
                case 6:
                    $etat->setLibelle('Archivée');
                    break;
            }
            $manager->persist($etat);
        }
        $manager->flush();

        // Création de Sorties fictives.
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 50; $i++){

            switch (rand(1, 7)){
                case 1 :
                    $nom = 'Restaurant gastronomique';
                    break;
                case 2 :
                    $nom = 'Découverte du vignoble nantais';
                    break;
                case 3 :
                    $nom = 'Parapente';
                    break;
                case 4 :
                    $nom = 'Musée du Louvre';
                    break;
                case 5 :
                    $nom = 'Accrobranche';
                    break;
                case 6 :
                    $nom = 'Surf';
                    break;
                case 7 :
                    $nom = 'VTT en bord de Loire';
                    break;
            }

            $sortie = new Sortie();
            $sortie->setNom($nom);
            $sortie->setDuree(rand(1, 20) * 30);
            $sortie->setDescription($faker->text(200));

            $debut = new DateTime();

            if(rand(0,3)<2){
                $d =  rand(0,30);
                $di = new DateInterval('P'.$d.'D');
                $di->invert=1;
            }else{
                $d =  rand(0,30);
                $di = new DateInterval('P'.$d.'D');
            }
            $debut->add($di);

            $h =  rand(9,16);
            $m =  rand(0,3) * 15;
            $debut->setTime($h,$m,0);
            $sortie->setDateHeureDebut($debut);

            $cloture = $debut;
            //$cloture->setTime(0,0,0);
            $di = new DateInterval('P3D');
            $di->invert=1;
            $cloture->add($di);
            $sortie->setDateCloture($cloture);

            $manager->persist($sortie);

            $sortie->setDuree(rand(1, 10) * 15);

            $h =  rand(1,5) * 15;
            $sortie->setNbInscriptionsMax($h);

            $etat = $manager->getRepository(Etat::class)->find(rand(0,1));
            $sortie->setEtat($etat);

            $sortie->setOrganisateur($participant);
            $sortie->setCampus($campus[mt_rand(0,3)]);

            $manager->persist($sortie);

        }
        $manager->flush();

    }
}
