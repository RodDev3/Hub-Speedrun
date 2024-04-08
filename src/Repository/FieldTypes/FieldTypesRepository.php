<?php

namespace App\Repository\FieldTypes;

use App\Entity\FieldTypes\FieldTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FieldTypes>
 *
 * @method FieldTypes|null find($id, $lockMode = null, $lockVersion = null)
 * @method FieldTypes|null findOneBy(array $criteria, array $orderBy = null)
 * @method FieldTypes[]    findAll()
 * @method FieldTypes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FieldTypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FieldTypes::class);
    }

    //    /**
    //     * @return FieldTypes[] Returns an array of FieldTypes objects
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

    //    public function findOneBySomeField($value): ?FieldTypes
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
