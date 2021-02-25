<?php

namespace App\Repository;

use App\Entity\Sortie;
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

    public function findByAllFilters($campus, $recherche, $dateDebut, $dateFin, $organisateur)
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
            $qb->addOrderBy('s.dateHeureDebut');
        $query = $qb->getQuery();
        return $query->getResult();
    }

}
