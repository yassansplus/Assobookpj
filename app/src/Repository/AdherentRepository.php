<?php

namespace App\Repository;

use App\Entity\Adherent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Adherent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adherent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adherent[]    findAll()
 * @method Adherent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdherentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adherent::class);
    }

    // /**
    //  * @return Adherent[] Returns an array of Adherent objects
    //  */
    public function searchAdherent($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('lower(a.search) LIKE lower(:val)')
            ->setParameter('val', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Adherent
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
