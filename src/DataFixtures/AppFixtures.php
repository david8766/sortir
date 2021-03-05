<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

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
        $admin->setActif(true);
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_ORGANISATEUR']);

        $manager->persist($admin);

        // Des participants

        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 20; $i++){
            $participant = new Participant();
            $participant->setCampus($campus[mt_rand(0,3)]);
            $participant->setPseudo('pseudo' . $i);
            $participant->setPrenom($faker->firstName());
            $participant->setNom($faker->lastName);
            $participant->setTelephone($faker->phoneNumber);
            $participant->setMail($faker->email);
            $participant->setMotDePasse(($this->passwordEncoder->encodePassword($admin, 'password')));
            $participant->setActif(true);
            $participant->setRoles(['ROLE_PARTICIPANT']);

            $participants[$i]=$participant;

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

        // Création de villes
        $ville1 = new ville();
        $ville1->setNom('NANTES');
        $ville1->setCodePostal('44000');
        $manager->persist($ville1);

        $ville2 = new ville();
        $ville2->setNom('ANNECY');
        $ville2->setCodePostal('74000');
        $manager->persist($ville2);

        $ville3 = new ville();
        $ville3->setNom('PARIS');
        $ville3->setCodePostal('75000');
        $manager->persist($ville3);

        $ville4 = new ville();
        $ville4->setNom('LACANAU');
        $ville4->setCodePostal('33200');
        $manager->persist($ville4);

        $manager->flush();

        // Création de lieux
        $lieu = array();

        $lieu[0] = new lieu();
        $lieu[0]->setNomLieu("Bar Le Depardieu'");
        $lieu[0]->setRue("Rue des Boit-sans-soif");
        $lieu[0]->setVille($ville1);
        $manager->persist($lieu[0]);

        $lieu[1] = new lieu();
        $lieu[1] ->setNomLieu("Base de loisirs");
        $lieu[1] ->setRue("Quai Alexandre 1er");
        $lieu[1] ->setVille($ville1);
        $manager->persist($lieu[1] );

        $lieu[2]  = new lieu();
        $lieu[2]->setNomLieu("Le Lieu Unique");
        $lieu[2]->setRue("Boulevard Quelquechose");
        $lieu[2]->setVille($ville1);
        $manager->persist($lieu[2]);

        $lieu[3] = new lieu();
        $lieu[3]->setNomLieu("Musée d'Orsay");
        $lieu[3]->setRue("Quai des Orfèvres");
        $lieu[3]->setVille($ville3);
        $manager->persist($lieu[3]);

        $lieu[4] = new lieu();
        $lieu[4]->setNomLieu("Le Louvre");
        $lieu[4]->setRue("Rue de Rivoli");
        $lieu[4]->setVille($ville3);
        $manager->persist($lieu[4]);

        $lieu[5] = new lieu();
        $lieu[5] ->setNomLieu("Bar à vin 'Le Merlot'");
        $lieu[5] ->setRue("Place des Grands Hommes");
        $lieu[5] ->setVille($ville3);
        $manager->persist($lieu[5] );

        $lieu[6]  = new lieu();
        $lieu[6]->setNomLieu("Tour Eiffel");
        $lieu[6]->setRue("Champs de mars");
        $lieu[6]->setVille($ville3);
        $manager->persist($lieu[6]);

        $lieu[7] = new lieu();
        $lieu[7]->setNomLieu('Col du Fémure');
        $lieu[7]->setRue("Route du col");
        $lieu[7]->setVille($ville2);
        $manager->persist($lieu[7]);

        $lieu[8] = new lieu();
        $lieu[8]->setNomLieu('Plage');
        $lieu[8]->setRue("Avenue de l'océan");
        $lieu[8]->setVille($ville4);
        $manager->persist($lieu[8]);

        $lieu[9] = new lieu();
        $lieu[9]->setNomLieu("Restaurant 'Les Dunes'");
        $lieu[9]->setRue("Rue des Embruns");
        $lieu[9]->setVille($ville4);
        $manager->persist($lieu[9]);

        $manager->flush();

        // Création de Sorties fictives.
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 30; $i++){

            switch (mt_rand(1, 7)){
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
            $sortie->setDuree(mt_rand(1, 20) * 30);
            $sortie->setDescription($faker->text(200));

            $debut = new DateTime();

            if(mt_rand(0,3)<2){
                $d =  mt_rand(0,30);
                $di = new DateInterval('P'.$d.'D');
                $di->invert=1;
            }else{
                $d =  mt_rand(0,30);
                $di = new DateInterval('P'.$d.'D');
            }
            $debut->add($di);

            $h =  mt_rand(9,16);
            $m =  mt_rand(0,3) * 15;
            $debut->setTime($h,$m,0);
            $sortie->setDateHeureDebut($debut);

            $cloture = $debut;
            $di = new DateInterval('P3D');
            $di->invert=1;
            $cloture->add($di);
            $sortie->setDateCloture($cloture);

            $sortie->setDuree(rand(1, 10) * 15);

            $p =  mt_rand(1,5) * 15;
            $sortie->setNbInscriptionsMax($p);

            $etat = $manager->getRepository(Etat::class)->find(rand(0,1));
            $sortie->setEtat($etat);

            //$organisateur = $manager->getRepository(Participant::class)->find(rand(0,1));
            $sortie->setOrganisateur($participants[mt_rand(1,20)]);
            $sortie->setCampus($campus[mt_rand(0,3)]);

            $idxLieu=mt_rand(0,9);
            $sortie->setLieu($lieu[$idxLieu]);
            $sortie->setVille($lieu[$idxLieu]->getVille());

            $manager->persist($sortie);

        }

        $manager->flush();

    }
}
