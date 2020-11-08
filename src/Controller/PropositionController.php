<?php


namespace App\Controller;


use App\Entity\Proposition;
use App\Form\PropositionType;
use App\Repository\PropositionRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PropositionController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var PropositionRepository
     */
    private $propositionRepo;
    /**
     * @var QuestionRepository
     */
    private $questionRepo;

    /**
     * PropositionController constructor.
     * @param EntityManagerInterface $em
     * @param PropositionRepository $propositionRepo
     * @param QuestionRepository $questionRepo
     */
    public function __construct(EntityManagerInterface $em, PropositionRepository $propositionRepo, QuestionRepository $questionRepo)
    {
        $this->em = $em;
        $this->propositionRepo = $propositionRepo;
        $this->questionRepo = $questionRepo;
    }

    /**
     * @Rest\View(serializerGroups={"proposition"})
     * @Rest\Get(path="/proposition/{id}")
     * @param Request $request
     * @return View
     * @throws EntityNotFoundException
     */
    public function getPropositionAction(Request $request)
    {
        $proposition = $this->propositionRepo->find($request->get('id'));

        if (empty($proposition) || $proposition->getQuestion()->getquiz()->getUser() !== $this->getUser()){
            return $this->questionNotFound();
        }

        return $proposition;
    }

    /**
     * @Rest\View(serializerGroups={"proposition"})
     * @Rest\Get(path="/question/{id}/propositions")
     * @param Request $request
     * @return View
     * @throws EntityNotFoundException
     */
    public function getPropositionsAction(Request $request)
    {
        $question = $this->questionRepo->findOneQuestion($request->get('id'), $this->getUser()->getId());

        if (empty($question) || $question[0]->getQuiz()->getUser() !== $this->getUser()){
            return $this->questionNotFound();
        }

        return $question[0]->getPropositions();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"proposition"})
     * @Rest\Post(path="/question/{id}/proposition")
     * @param Request $request
     * @return View
     * @throws EntityNotFoundException
     */
    public function postPropositionAction(Request $request)
    {
        $question = $this->questionRepo->findOneQuestion($request->get('id'), $this->getUser()->getId());
        if (empty($question) || $question[0]->getQuiz()->getUser() !== $this->getUser()){
            return $this->questionNotFound();
        }

        $proposition = new Proposition();
        $proposition->setQuestion($question[0]);
        $form = $this->createForm(PropositionType::class, $proposition);
        $form->submit($request->request->all());
        if ($form->isValid()){
            $this->em->persist($proposition);
            $this->em->flush();
            return $proposition;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerGroups={"proposition"})
     * @Rest\Put("/proposition/{id}")
     * @param Request $request
     * @return FormInterface|JsonResponse
     * @throws EntityNotFoundException
     */
    public function updatePropositionAction(Request $request)
    {
        return $this->updateProposition($request, true);
    }

    /**
     * @Rest\View(serializerGroups={"proposition"})
     * @Rest\Patch("/proposition/{id}")
     * @param Request $request
     * @return FormInterface|JsonResponse
     * @throws EntityNotFoundException
     */
    public function patchPropositionAction(Request $request)
    {
        return $this->updateProposition($request, false);
    }

    /**
     * @param Request $request
     * @param $clearMissing
     * @return FormInterface|JsonResponse
     * @throws EntityNotFoundException
     */
    public function updateProposition(Request $request, $clearMissing)
    {
        $proposition = $this->propositionRepo->find($request->get('id'));

        if (empty($proposition) || $proposition->getQuestion()->getquiz()->getUser() !== $this->getUser()){
            return $this->propositionNotFound();
        }

        $form = $this->createForm(PropositionType::class, $proposition);
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $this->em->merge($proposition);
            $this->em->flush();
            return $proposition;
        } else {
            return $form;
        }
    }
    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete(path="/proposition/{id}")
     * @param Request $request
     * @return void
     */
    public function removePropositionAction(Request $request)
    {
        $proposition = $this->propositionRepo->find($request->get('id'));
        if ($proposition && $proposition->getQuestion()->getquiz()->getUser() === $this->getUser()) {
            $this->em->remove($proposition);
            $this->em->flush();
        }
    }

    private function questionNotFound()
    {
        //return View::create(['message' => 'Question not found'], Response::HTTP_NOT_FOUND);
        throw new EntityNotFoundException('Question not found');
    }

    private function propositionNotFound()
    {
        //return View::create(['message' => 'Proposition not found'], Response::HTTP_NOT_FOUND);
        throw new EntityNotFoundException('Proposition not found');
    }
}
