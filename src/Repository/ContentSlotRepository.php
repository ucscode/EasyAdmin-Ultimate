<?php

namespace App\Repository;

use App\Entity\ContentSlot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContentSlot>
 *
 * @method ContentSlot|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContentSlot|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContentSlot[]    findAll()
 * @method ContentSlot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentSlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContentSlot::class);
    }

    //    /**
    //     * @return ContentSlot[] Returns an array of ContentSlot objects
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

    //    public function findOneBySomeField($value): ?ContentSlot
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
