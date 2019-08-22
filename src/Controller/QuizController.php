<?php


namespace App\Controller;


use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\FormInterface;
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
     * @var QuizRepository
     */
    private $quizRepo;

    /**
     * quizController constructor.
     * @param ObjectManager $em
     * @param QuizRepository $quizRepo
     */
    public function __construct(ObjectManager $em, QuizRepository $quizRepo)
    {
        $this->em = $em;
        $this->quizRepo = $quizRepo;
    }

    /**
     * @Rest\View(serializerGroups={"quizzes"})
     * @Rest\Get(path="/quiz")
     * @return object|JsonResponse|null
     * @throws EntityNotFoundException
     */
    public function getQuizzesAction()
    {
        $user = $this->getUser();
        $quizzes = $user->getQuizzes();
        if (empty($quizzes)) return $this->quizNotFound();
        return $quizzes;
    }

    /**
     * @Rest\View(serializerGroups={"quiz"})
     * @Rest\Get(path="/quiz/{id}", name="quiz_one")
     * @param Request $request
     * @return object|JsonResponse|null
     * @throws EntityNotFoundException
     */
    public function getQuizAction(Request $request)
    {
        $quiz = $this->quizRepo->findOneBy([
            'id' => $request->get('id'),
            'user' => $this->getUser()->getId()
        ]);

        // $quiz = $this->quizRepo->findOneQuiz($request->get('id'), $this->getUser()->getId());

        if (empty($quiz)) return $this->quizNotFound();

        return $quiz;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"quiz"})
     * @Rest\Post(path="/quiz", options={})
     * @param Request $request
     * @return Quiz|FormInterface
     */
    public function postQuizAction(Request $request)
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->submit($request->request->all()); // Validation des données

        if ($form->isValid()) {
            $quiz->setUser($this->getUser());
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
     * @param Request $request
     * @return FormInterface|JsonResponse|null
     * @throws EntityNotFoundException
     */
    public function updateQuizAction(Request $request)
    {
        return $this->updateQuiz($request, true);
    }

    /**
     * @Rest\View(serializerGroups={"quiz"})
     * @Rest\Patch("/quiz/{id}")
     * @param Request $request
     * @return FormInterface|JsonResponse|null
     * @throws EntityNotFoundException
     */
    public function patchQuizAction(Request $request)
    {
        return $this->updateQuiz($request, false);
    }


    /**
     * @param Request $request
     * @param $clearMissing
     * @return FormInterface|JsonResponse|null
     * @throws EntityNotFoundException
     */
    public function updateQuiz(Request $request, $clearMissing)
    {
        $quiz = $this->quizRepo->findOneBy([
            'id' => $request->get('id'),
            'user' => $this->getUser()->getId()
        ]);

        if (empty($quiz)) {
            return $this->quizNotFound();
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
     * @return View
     * @throws EntityNotFoundException
     */
    public function removeQuizAction(Request $request)
    {
        $quiz = $this->quizRepo->findOneBy([
            'id' => $request->get('id'),
            'user' => $this->getUser()->getId()
        ]);

        if (empty($quiz)) {
            return $this->quizNotFound();
        }

        $this->em->remove($quiz);
        $this->em->flush();
    }

    private function quizNotFound()
    {
        // return View::create(['message' => 'Quiz not found'], Response::HTTP_NOT_FOUND);
        throw new EntityNotFoundException('Quiz not found');
    }
}
