<?php


namespace App\Controller;


use App\Entity\Proposition;
use App\Entity\Question;
use App\Form\PropositionType;
use App\Repository\PropositionRepository;
use App\Repository\QuestionRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PropositionController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * PropositionController constructor.
     * @param ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Rest\View(serializerGroups={"proposition"})
     * @Rest\Get(path="/proposition/{id}")
     * @param Request $request
     * @param PropositionRepository $propositionRepo
     * @return View
     */
    public function getPropositionAction(Request $request, PropositionRepository $propositionRepo)
    {
        $proposition = $propositionRepo->find($request->get('id'));
        if (empty($proposition)){
            return $this->questionNotFound();
        }

        return $proposition;
    }

    /**
     * @Rest\View(serializerGroups={"proposition"})
     * @Rest\Get(path="/question/{id}/propositions")
     * @param Request $request
     * @param QuestionRepository $questionRepo
     * @return View
     */
    public function getPropositionsAction(Request $request, questionRepository $questionRepo)
    {
        $question = $questionRepo->find($request->get('id'));

        if (empty($question)){
            return $this->questionNotFound();
        }

        return $question->getPropositions();
    }
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"proposition"})
     * @Rest\Post(path="/question/{id}/proposition")
     * @param Request $request
     * @param QuestionRepository $questionRepo
     * @return View
     */
    public function postPropositionAction(Request $request, QuestionRepository $questionRepo)
    {
        $question = $questionRepo->find($request->get('id'));
        if (empty($question)){
            return $this->questionNotFound();
        }

        $proposition = new Proposition();
        $proposition->setQuestion($question);
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
     */
    public function updatePropositionAction(Request $request)
    {
        return $this->updateProposition($request, true);
    }

    /**
     * @Rest\View(serializerGroups={"proposition"})
     * @Rest\Patch("/proposition/{id}")
     */
    public function patchPropositionAction(Request $request)
    {
        return $this->updateProposition($request, false);
    }

    /**
     * @param Request $request
     * @param $clearMissing
     * @return \Symfony\Component\Form\FormInterface|JsonResponse
     */
    public function updateProposition(Request $request, $clearMissing)
    {
        $propositionRepo = $this->getDoctrine()->getRepository(Proposition::class);
        $proposition = $propositionRepo->find($request->get('id'));

        if (empty($proposition)) {
            return new JsonResponse(['message' => 'Proposition not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(PropositionType::class, $proposition);
        $form->submit($request->request->all(), $clearMissing); // Validation des données

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
     * @param PropositionRepository $propositionRepo
     * @return void
     */
    public function removePropositionAction(Request $request, PropositionRepository $propositionRepo)
    {
        $proposition = $propositionRepo->find($request->get('id'));
        if ($proposition) {
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
