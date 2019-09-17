<?php

namespace App\Repository;

use App\Entity\Execution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Execution|null find($id, $lockMode = null, $lockVersion = null)
 * @method Execution|null findOneBy(array $criteria, array $orderBy = null)
 * @method Execution[]    findAll()
 * @method Execution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExecutionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Execution::class);
    }

    // /**
    //  * @return Execution[] Returns an array of Execution objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Execution
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
