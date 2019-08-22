<?php

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function findOneQuiz(int $quizId, int $userId)
    {
        return $this->createQueryBuilder('qui')
            ->join('qui.questions', 'que')
            ->join('que.propositions', 'p')
            ->andWhere('qui.id = :quizId')
            ->setParameter('quizId', $quizId)
            ->andWhere('qui.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('p.position', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
