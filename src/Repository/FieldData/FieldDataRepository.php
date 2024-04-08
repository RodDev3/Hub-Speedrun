<?php

namespace App\Repository\FieldData;

use App\Entity\FieldData\FieldData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FieldData>
 *
 * @method FieldData|null find($id, $lockMode = null, $lockVersion = null)
 * @method FieldData|null findOneBy(array $criteria, array $orderBy = null)
 * @method FieldData[]    findAll()
 * @method FieldData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FieldDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FieldData::class);
    }

    //    /**
    //     * @return FieldData[] Returns an array of FieldData objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?FieldData
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
