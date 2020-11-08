<?php


namespace App\Controller;


use App\Entity\Answer;
use App\Form\AnswerType;
use App\Repository\ExecutionRepository;
use App\Repository\PropositionRepository;
use App\Services\QuizExecutionService;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property EntityManagerInterface em
 */
class AnswerController extends AbstractController
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Rest\View(serializerGroups={"answer"})
     * @Rest\Get(path="execution/{id}/answers")
     * @param Request $request
     * @return Answer[]|Collection
     * @throws EntityNotFoundException
     */
    public function getAnswers(Request $request)
    {

    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"answer"})
     * @Rest\Post(path="/execution/{id}/answer")
     * @param Request $request
     * @param ExecutionRepository $executionRepo
     * @param PropositionRepository $propositionRepo
     * @param QuizExecutionService $quizExecutionService
     * @return Answer|FormInterface
     * @throws EntityNotFoundException
     */
    public function postAnswerAction(Request $request,
                                     ExecutionRepository $executionRepo,
                                     PropositionRepository $propositionRepo,
                                     QuizExecutionService $quizExecutionService)
    {
        $execution = $executionRepo->findOneBy(['id' => $request->get('id')]);
        if (empty($execution)) return $this->executionNotFound();

        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer, ['propositionRepo' => $propositionRepo]);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            $isSuccess = $quizExecutionService->isSuccessAnswer($answer);
            $answer->setSuccess($isSuccess);
            $answer->setExecution($execution);
            $this->em->persist($answer);
            $this->em->flush();

            return $answer;
        } else {
            return $form;
        }
    }

    private function executionNotFound()
    {
        throw new EntityNotFoundException('Execution not found');
    }
}
