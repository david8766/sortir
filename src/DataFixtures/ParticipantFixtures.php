<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Participant;
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
    }
}
