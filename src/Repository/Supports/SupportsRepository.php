<?php

namespace App\Repository\Supports;

use App\Entity\Supports\Supports;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Supports>
 *
 * @method Supports|null find($id, $lockMode = null, $lockVersion = null)
 * @method Supports|null findOneBy(array $criteria, array $orderBy = null)
 * @method Supports[]    findAll()
 * @method Supports[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupportsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Supports::class);
    }

    //    /**
    //     * @return Supports[] Returns an array of Supports objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Supports
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
