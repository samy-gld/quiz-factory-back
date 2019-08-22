<?php

namespace App\Repository;

use App\Entity\Question;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function findOneQuestion(int $questionId, int $userId)
    {
        return $this->createQueryBuilder('question')
            ->leftJoin('question.quiz', 'quiz')
            ->andWhere('question.id = :id')
            ->setParameter('id', $questionId)
            ->andWhere('quiz.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findQuestionsForQuiz(int $quizId, int $userId)
    {
        return $this->createQueryBuilder('que')
            ->leftJoin('que.quiz', 'qui')
            ->leftJoin('que.propositions', 'p')
            ->andWhere('qui.id = :id')
            ->setParameter('id', $quizId)
            ->andWhere('qui.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
}
