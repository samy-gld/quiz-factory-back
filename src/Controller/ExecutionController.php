<?php


namespace App\Controller;


use App\Entity\Execution;
use App\Form\ExecutionType;
use App\Repository\ExecutionRepository;
use App\Repository\InvitationRepository;
use App\Repository\QuizRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property ObjectManager em
 */
class ExecutionController extends AbstractController
{
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Rest\View(serializerGroups={"execution"})
     * @Rest\Get(path="quiz/{id}/executions")
     * @param Request $request
     * @param QuizRepository $quizRepo
     * @return Execution[]|Collection
     * @throws EntityNotFoundException
     */
    public function getExecutions(Request $request, QuizRepository $quizRepo)
    {
        $quiz = $quizRepo->findOneBy([
            'id' => $request->get('id'),
            'user' => $this->getUser()->getId()
        ]);

        if (!empty($quiz)) return $quiz->getExecutions();
        else return $this->quizNotFound();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"execution"})
     * @Rest\Post(path="/execution")
     * @param Request $request
     * @param InvitationRepository $invitationRepo
     * @return Execution|FormInterface
     */
    public function postExecutionAction(Request $request, InvitationRepository $invitationRepo)
    {
        $execution = new Execution();
        $form = $this->createForm(ExecutionType::class, $execution, ['invitationRepo' => $invitationRepo]);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $invitation = $execution->getInvitation();
            $invitation->setExecution($execution);
            $this->em->persist($execution);
            $this->em->flush();

            return $execution;
        } else {
            return $form;
        }
    }

    private function quizNotFound()
    {
        throw new EntityNotFoundException('Quiz not found');
    }
}
