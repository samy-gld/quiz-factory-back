<?php


namespace App\Controller;


use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class QuestionController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * QuestionController constructor.
     * @param ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Rest\View(serializerGroups={"question"})
     * @Rest\Get(path="/question/{id}")
     * @param Request $request
     * @param QuestionRepository $questionRepo
     * @return View
     */
    public function getQuestionAction(Request $request, QuestionRepository $questionRepo)
    {
        $question = $questionRepo->find($request->get('id'));
        if (empty($question)){
            return $this->questionNotFound();
        }

        return $question;
    }

    /**
     * @Rest\View(serializerGroups={"question"})
     * @Rest\Get(path="/quiz/{id}/questions")
     * @param Request $request
     * @param QuizRepository $quizRepo
     * @return View
     */
    public function getQuestionsAction(Request $request, QuizRepository $quizRepo)
    {
        $quiz = $quizRepo->find($request->get('id'));

        if (empty($quiz)){
            return $this->quizNotFound();
        }

        return $quiz->getQuestions();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"question"})
     * @Rest\Post(path="quiz/{id}/question")
     * @param Request $request
     * @param QuizRepository $quizRepo
     * @return View|\Symfony\Component\Form\FormInterface
     */
    public function postQuestionAction(Request $request, QuizRepository $quizRepo)
    {
        $quiz = $quizRepo->find($request->get('id'));
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
     */
    public function updateQuestionAction(Request $request)
    {
        return $this->updateQuestion($request, true);
    }

    /**
     * @Rest\View(serializerGroups={"question"})
     * @Rest\Patch("/question/{id}")
     */
    public function patchQuestionAction(Request $request)
    {
        return $this->updateQuestion($request, false);
    }

    /**
     * @param Request $request
     * @param $clearMissing
     * @return \Symfony\Component\Form\FormInterface|JsonResponse
     */
    public function updateQuestion(Request $request, $clearMissing)
    {
        $questionRepo = $this->getDoctrine()->getRepository(Question::class);
        $question = $questionRepo->find($request->get('id'));

        if (empty($question)) {
            return new JsonResponse(['message' => 'Question not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(QuestionType::class, $question);
        $form->submit($request->request->all(), $clearMissing); // Validation des données

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
        $question = $questionRepo->find($request->get('id'));
        if ($question) {
            $this->em->remove($question);
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
