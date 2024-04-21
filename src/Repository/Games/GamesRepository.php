<?php

namespace App\Repository\Games;

use App\Entity\Games\Games;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Games>
 *
 * @method Games|null find($id, $lockMode = null, $lockVersion = null)
 * @method Games|null findOneBy(array $criteria, array $orderBy = null)
 * @method Games[]    findAll()
 * @method Games[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GamesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Games::class);
    }

    public function getGamesFromResearch(string $research): array
    {
        $query = $this->createQueryBuilder('g');

        //TODO faire en sort que si l'on revienne en arriere cela ne donne pas tous les jeux
        return $query
            ->where($query->expr()->like('g.name', ':val'))
            ->setParameter('val', '%'.$research.'%')
            ->andWhere('g.active = 1')
            ->getQuery()
            ->getResult()
            ;
    }

    /*public function getGameFromRewrite(string $rewrite)
    {
        return $this->createQueryBuilder('g')
            ->where('g.rewrite = :rewrite')
            ->setParameter('rewrite', $rewrite)
            ->getQuery()
            ->getResult()
            ;
    }*/


    //    /**
    //     * @return Games[] Returns an array of Games objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Games
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
