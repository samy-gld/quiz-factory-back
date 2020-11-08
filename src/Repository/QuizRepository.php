<?php

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
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

    public function isFinalized(int $id)
    {
        $quiz = $this->createQueryBuilder('q')
            ->select('q.status')
            ->andWhere('q.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        return isset($quiz) && $quiz["status"] === 'finalized';
    }
}
