<?php

namespace App\Repository\Moderations;

use App\Entity\Moderations\Moderations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Moderations>
 *
 * @method Moderations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Moderations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Moderations[]    findAll()
 * @method Moderations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModerationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Moderations::class);
    }

    //    /**
    //     * @return Moderations[] Returns an array of Moderations objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Moderations
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
