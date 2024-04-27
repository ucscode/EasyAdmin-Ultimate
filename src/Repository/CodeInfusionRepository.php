<?php

namespace App\Repository;

use App\Entity\CodeInfusion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CodeInfusion>
 *
 * @method CodeInfusion|null find($id, $lockMode = null, $lockVersion = null)
 * @method CodeInfusion|null findOneBy(array $criteria, array $orderBy = null)
 * @method CodeInfusion[]    findAll()
 * @method CodeInfusion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodeInfusionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CodeInfusion::class);
    }

    //    /**
    //     * @return CodeInfusion[] Returns an array of CodeInfusion objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CodeInfusion
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
