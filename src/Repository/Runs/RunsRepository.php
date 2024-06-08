<?php

namespace App\Repository\Runs;

use App\Entity\Categories\Categories;
use App\Entity\Fields\Fields;
use App\Entity\Runs\Runs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Internal\Hydration\ObjectHydrator;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PDO;

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

    public function getAllAcceptedRuns($categories): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.refCategories = :categories')
            ->setParameter('categories', $categories)
            ->andWhere('r.refStatus = :status')
            ->setParameter('status', 2)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getAllAcceptedAndObsoleteRuns($categories): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.refCategories = :categories')
            ->setParameter('categories', $categories)
            ->andWhere('r.refStatus = :status')
            ->setParameter('status', 4)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getRunsByCategoriesOrderByTimes(Categories $categories, array $filters = []): array
    {

        //TODO A GARDER POUR LE DOSSIER
        //PROBLEME ORDER BY CARCHAR EN INT
        //
        $primaryFields = $categories->getPrimaryComparison();

        $secondaryFields = $categories->getSecondaryComparison();

        $secondary = false;
        if ($secondaryFields instanceof Fields) {
            $secondary = true;
        }

        $sql = "SELECT r.*, fdPrimary.id as primary_id, fdPrimary.ref_runs_id as fd_primary_ref_runs_id, fdPrimary.ref_fields_id as fd_primary_ref_fields_id, fdPrimary.data as fd_primary_data";

        if ($secondary) {
            $sql .= " ,fdSecondary.id as secondary_id, fdSecondary.ref_runs_id as fd_secondary_ref_runs_id, fdSecondary.ref_fields_id as fd_secondary_ref_fields_id, fdSecondary.data as fd_secondary_data";
        }

        $sql .= " FROM Runs as r JOIN field_data as fdPrimary ON r.id = fdPrimary.ref_runs_id";

        if ($secondary){
            $sql .= " JOIN field_data as fdSecondary ON r.id = fdSecondary.ref_runs_id";
        }

        $sql .= " WHERE r.ref_categories_id = :categoriesId AND fdPrimary.ref_fields_id = :primaryId";

        if ($secondary) {
            $sql .= " AND fdSecondary.ref_fields_id = :secondaryId ";
        }

        $sql .= " ORDER BY CAST(fdPrimary.data as UNSIGNED) ASC";

        if ($secondary) {
            $sql .= ", CAST(fdSecondary.data as UNSIGNED) ASC";
        }


        $rsm = new ResultSetMappingBuilder($this->getEntityManager());

        $rsm->addRootEntityFromClassMetadata('\App\Entity\Runs\Runs', 'r');
        $rsm->addJoinedEntityFromClassMetadata('\App\Entity\FieldData\FieldData', 'fdPrimary', 'r', 'refFieldData', ['id' => 'primary_id', 'ref_runs_id' => 'fd_primary_ref_runs_id', 'ref_fields_id' => 'fd_primary_ref_fields_id', 'data' => 'fd_primary_data']);
        $rsm->addJoinedEntityFromClassMetadata('\App\Entity\FieldData\FieldData', 'fdSecondary', 'r', 'refFieldData', ['id' => 'secondary_id', 'ref_runs_id' => 'fd_secondary_ref_runs_id', 'ref_fields_id' => 'fd_secondary_ref_fields_id', 'data' => 'fd_secondary_data']);

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);

        $query->setParameter('categoriesId', $categories->getId());
        $query->setParameter('primaryId', $primaryFields->getId());

        if ($secondary){
            $query->setParameter('secondaryId', $secondaryFields->getId());
        }


        return $query->getResult();
    }
}
