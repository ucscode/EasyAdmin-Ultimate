<?php

namespace App\Repository;

use App\Entity\UserProperty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserProperty>
 *
 * @method UserProperty|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserProperty|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserProperty[]    findAll()
 * @method UserProperty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProperty::class);
    }

    //    /**
    //     * @return UserProperty[] Returns an array of UserProperty objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserProperty
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
