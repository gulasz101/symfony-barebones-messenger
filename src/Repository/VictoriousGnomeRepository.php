<?php

namespace App\Repository;

use App\Entity\VictoriousGnome;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VictoriousGnome|null find($id, $lockMode = null, $lockVersion = null)
 * @method VictoriousGnome|null findOneBy(array $criteria, array $orderBy = null)
 * @method VictoriousGnome[]    findAll()
 * @method VictoriousGnome[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VictoriousGnomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VictoriousGnome::class);
    }

    // /**
    //  * @return VictoriousGnome[] Returns an array of VictoriousGnome objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VictoriousGnome
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
