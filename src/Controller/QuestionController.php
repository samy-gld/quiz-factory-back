<?php


namespace App\Controller;


use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class QuestionController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var QuestionRepository
     */
    private $questionRepo;

    /**
     * QuestionController constructor.
     * @param EntityManagerInterface $em
     * @param QuestionRepository $questionRepo
     */
    public function __construct(EntityManagerInterface $em, QuestionRepository $questionRepo)
    {
        $this->em = $em;
        $this->questionRepo = $questionRepo;
    }

    /**
     * @Rest\View(serializerGroups={"question"})
     * @Rest\Get(path="/question/{id}")
     * @param Request $request
     * @return View
     * @throws EntityNotFoundException
     */
    public function getQuestionAction(Request $request)
    {
        $question = $this->questionRepo->findOneQuestion($request->get('id'), $this->getUser()->getId());
        if (empty($question)){
            return $this->questionNotFound();
        }

        return $question;
    }

    /**
     * @Rest\View(serializerGroups={"question"})
     * @Rest\Get(path="/quiz/{id}/questions")
     * @param Request $request
     * @return View
     * @throws EntityNotFoundException
     */
    public function getQuestionsAction(Request $request)
    {
        $questions = $this->questionRepo->findQuestionsForQuiz($request->get('id'), $this->getUser()->getId());
        if (empty($questions)){
            return $this->questionNotFound();
        }

        return $questions;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"question"})
     * @Rest\Post(path="quiz/{id}/question")
     * @param Request $request
     * @param QuizRepository $quizRepo
     * @return View|FormInterface
     * @throws EntityNotFoundException
     */
    public function postQuestionAction(Request $request, QuizRepository $quizRepo)
    {
        $quiz = $quizRepo->findOneBy([
            'id' => $request->get('id'),
            'user' => $this->getUser()->getId()
        ]);
        if (empty($quiz)){
            return $this->quizNotFound();
        }

        $question = new Question();
        $question->setquiz($quiz);
        $form = $this->createForm(QuestionType::class, $question);
        $form->submit($request->request->all());

        if ($form->isValid()){
            $this->em->persist($question);
            $this->em->flush();
            return $question;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerGroups={"question"})
     * @Rest\Put("/question/{id}")
     * @param Request $request
     * @return FormInterface|JsonResponse
     */
    public function updateQuestionAction(Request $request)
    {
        return $this->updateQuestion($request, true);
    }

    /**
     * @Rest\View(serializerGroups={"question"})
     * @Rest\Patch("/question/{id}")
     * @param Request $request
     * @return FormInterface|JsonResponse
     */
    public function patchQuestionAction(Request $request)
    {
        return $this->updateQuestion($request, false);
    }

    /**
     * @param Request $request
     * @param $clearMissing
     * @return FormInterface|JsonResponse
     */
    public function updateQuestion(Request $request, $clearMissing)
    {
        $question = $this->questionRepo->findOneQuestion($request->get('id'), $this->getUser()->getId());

        if (empty($question)) {
            return new JsonResponse(['message' => 'Question not found'], Response::HTTP_NOT_FOUND);
        }

        $question = $question[0];
        $form = $this->createForm(QuestionType::class, $question);
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {

            $this->em->merge($question);
            $this->em->flush();
            return $question;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete(path="/question/{id}")
     * @param Request $request
     * @param QuestionRepository $questionRepo
     * @return View
     */
    public function removeQuestionAction(Request $request, QuestionRepository $questionRepo)
    {
        $question = $questionRepo->findOneQuestion($request->get('id'), $this->getUser()->getId());
        if ($question) {
            $this->em->remove($question[0]);
            $this->em->flush();
        }
    }

    private function quizNotFound()
    {
        //return View::create(['message' => 'Quiz not found'], Response::HTTP_NOT_FOUND);
        throw new EntityNotFoundException('Quiz not found');
    }

    private function questionNotFound()
    {
        //return View::create(['message' => 'Question not found'], Response::HTTP_NOT_FOUND);
        throw new EntityNotFoundException('Question not found');
    }
}
