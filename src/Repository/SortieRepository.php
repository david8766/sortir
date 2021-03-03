<?php

namespace App\Repository;

use App\Entity\Sortie;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findByAllFilters(
        $campus,
        $recherche,
        $dateDebut,
        $dateFin,
        $organisateur,
        $etatSortiesPassees)
    {

        $qb = $this->createQueryBuilder('s');
        $qb ->join('s.campus', 'c')
            ->addSelect('c')
            ->join('s.organisateur', 'p')
            ->addSelect('p');

        if($campus != null){
            $qb ->andWhere('c.id = :campus')
                ->setParameter('campus', $campus);
        }
        if($recherche != null){
            $qb ->andWhere('s.nom LIKE :recherche')
                ->setParameter('recherche', '%'.$recherche.'%');
        }
        if($dateDebut != null){
            $qb ->andWhere('s.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $dateDebut);
        }
        if($dateFin != null){
            $qb ->andWhere('s.dateCloture <= :dateFin')
                ->setParameter('dateFin', $dateFin);
        }
        if($organisateur != null){
            $qb ->andWhere('p.id = :organisateur')
                ->setParameter('organisateur', $organisateur);
        }
        if($etatSortiesPassees != null){
            $qb ->andWhere('s.etat = :etatSortiesPassees')
                ->setParameter('etatSortiesPassees',$etatSortiesPassees);
        }
            $qb->addOrderBy('s.dateHeureDebut', 'DESC');
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findByMesInscriptionsEnCours($user){
        $qb = $this->createQueryBuilder('s');
        $qb ->join('s.inscriptions', 'i')
            ->addSelect('i')
            ->andWhere('i.participant = :user')
            ->setParameter('user', $user);
        $qb->addOrderBy('s.dateHeureDebut', 'DESC');
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findByMesInscriptionsPossiblesAlternate($user){
        $qb = $this->createQueryBuilder('s');

        $ssReq = $qb ->select('i.sortie')
                     ->from('App\Entity\Inscriptions', 'i')
                     ->where('i.participant = :user')
                     ->setParameter('user', $user);

        $MainReq = $qb  ->andWhere('s.etat = 1')
                        ->andWhere($qb->expr()->notIn('s.inscriptions', $ssReq->getDQL()))
                        ->addOrderBy('s.dateHeureDebut', 'DESC');

        $query = $MainReq->getQuery();
        return $query->getResult();
    }

    public function findByMesInscriptionsPossibles($user){
        $em = $this->getEntityManager();
        $dql = "SELECT s FROM App\Entity\Sortie s                
                WHERE s.etat = 1 
                AND s NOT IN (SELECT i.sortie FROM App\Entity\Inscriptions i
                WHERE i.participant = :user)
                ORDER BY s.dateHeureDebut DESC";
            $em->createQuery($dql)
                ->setParameter('user', $user)
                ->execute();

    }
//$qb ->andWhere('s.etat = 1')
//->andWhere($qb->expr()->notIn());

    public function updateEtats(){

        // inscriptions closes
        $now = new DateTime();

        $em = $this->getEntityManager();
        $dql = "UPDATE App\Entity\Sortie s 
                SET s.etat=2 
                WHERE s.etat=1 and s.dateCloture<:now";
        $em->createQuery($dql)
            ->setParameter('now', $now)
            ->execute();

        // sorties en cours
        $em = $this->getEntityManager();
        $dql = "UPDATE App\Entity\Sortie s 
                SET s.etat=3 
                WHERE s.etat>0 and s.etat<3 and s.dateHeureDebut<:now";
        $em->createQuery($dql)
            ->setParameter('now', $now)
            ->execute();

        // sorties passées
        $now = new DateTime();

        $em = $this->getEntityManager();
        $dql = "UPDATE App\Entity\Sortie s 
                SET s.etat=4 
                WHERE s.etat>0 and s.etat<4 and DATE_ADD(s.dateHeureDebut, s.duree, 'minute')<:now";
        $em->createQuery($dql)
            ->setParameter('now', $now)
            ->execute();

        // sorties archivées apres 30 jours
        $di = new DateInterval('P30D');
        $di->invert=1;
        $dateArchive = new DateTime('midnight');
        $dateArchive->add($di);

        $em = $this->getEntityManager();
        $dql = "UPDATE App\Entity\Sortie s 
                SET s.etat=6 
                WHERE s.etat<>5 and s.dateHeureDebut<:dateArchive";
        $em->createQuery($dql)
                    ->setParameter('dateArchive', $dateArchive)
                    ->execute();

    }
}
