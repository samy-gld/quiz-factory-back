<?php


namespace App\Controller;


use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class QuizController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * quizController constructor.
     * @param ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Rest\View(serializerGroups={"quizzes"})
     * @Rest\Get(path="/quiz")
     * @param Request $request
     * @param QuizRepository $quizRepo
     * @return object|JsonResponse|null
     */
    public function getQuizzesAction(QuizRepository $quizRepo)
    {
        $quizzes = $quizRepo->findAll();
        if (empty($quizzes)) return new JsonResponse(['message' => 'Quiz not found'], Response::HTTP_NOT_FOUND);
        return $quizzes;
    }

    /**
     * @Rest\View(serializerGroups={"quiz"})
     * @Rest\Get(path="/quiz/{id}", name="quiz_one")
     * @param Request $request
     * @param QuizRepository $quizRepo
     * @return object|JsonResponse|null
     */
    public function getQuizAction(Request $request, QuizRepository $quizRepo)
    {
        $quiz = $quizRepo->find($request->get('id'));

        if (empty($quiz)) return new JsonResponse(['message' => 'Quiz not found'], Response::HTTP_NOT_FOUND);

        return $quiz;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"quiz"})
     * @Rest\Post(path="/quiz", options={})
     * @param Request $request
     * @return Quiz|\Symfony\Component\Form\FormInterface
     */
    public function postQuizAction(Request $request)
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->submit($request->request->all()); // Validation des données

        if ($form->isValid()) {
            $this->em->persist($quiz);
            $this->em->flush();
            return $quiz;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerGroups={"quiz"})
     * @Rest\Put("/quiz/{id}")
     */
    public function updateQuizAction(Request $request)
    {
        return $this->updateQuiz($request, true);
    }

    /**
     * @Rest\View(serializerGroups={"quiz"})
     * @Rest\Patch("/quiz/{id}")
     */
    public function patchQuizAction(Request $request)
    {
        return $this->updateQuiz($request, false);
    }


    /**
     * @param Request $request
     * @param $clearMissing
     * @return \Symfony\Component\Form\FormInterface|JsonResponse|null
     */
    public function updateQuiz(Request $request, $clearMissing)
    {
        $quizRepo = $this->getDoctrine()->getRepository(Quiz::class);
        $quiz = $quizRepo->find($request->get('id'));

        if (empty($quiz)) {
            return new JsonResponse(['message' => 'Quiz not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(QuizType::class, $quiz);
        $form->submit($request->request->all(), $clearMissing); // Validation des données

        if ($form->isValid()) {
            $this->em->merge($quiz);
            $this->em->flush();
            return $quiz;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete(path="/quiz/{id}")
     * @param Request $request
     * @param QuizRepository $quizRepo
     */
    public function removequizAction(Request $request, QuizRepository $quizRepo)
    {
        $quiz = $quizRepo->find($request->get('id'));

        if ($quiz) {
            $this->em->remove($quiz);
            $this->em->flush();
        }
    }
}
