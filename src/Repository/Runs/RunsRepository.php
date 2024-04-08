<?php

namespace App\Repository\Runs;

use App\Entity\Runs\Runs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Runs>
 *
 * @method Runs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Runs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Runs[]    findAll()
 * @method Runs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RunsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Runs::class);
    }

    //    /**
    //     * @return Runs[] Returns an array of Runs objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Runs
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
