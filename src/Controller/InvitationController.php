<?php


namespace App\Controller;


use App\Entity\Invitation;
use App\Form\InvitationType;
use App\Repository\InvitationRepository;
use App\Repository\QuizRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

/**
 * @property ObjectManager em
 */
class InvitationController extends AbstractController
{
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Rest\View(serializerGroups={"invitation"})
     * @Rest\Get(path="quiz/{id}/invitations")
     * @param Request $request
     * @param QuizRepository $quizRepo
     * @return Invitation[]|Collection
     * @throws EntityNotFoundException
     */
    public function getInvitations(Request $request, QuizRepository $quizRepo)
    {
        $quiz = $quizRepo->findOneBy([
            'id' => $request->get('id'),
            'user' => $this->getUser()->getId()
        ]);

        if (empty($quiz)) return $this->quizNotFound();

        return $quiz->getInvitations();


    }

    /**
     * @Rest\View(serializerGroups={"invitation"})
     * @Rest\Get(path="invitation/{token}")
     * @param Request $request
     * @param InvitationRepository $invitationRepo
     * @return Invitation|void|null
     * @throws EntityNotFoundException
     */
    public function getInvitationAction(Request $request, InvitationRepository $invitationRepo)
    {
        $invitation = $invitationRepo->findOneBy(['token' => $request->get('token')]);
        if (empty($invitation)) return $this->invitationNotFound();

        return $invitation;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"invitation"})
     * @Rest\Post(path="/invitation")
     * @param Request $request
     * @param QuizRepository $quizRepo
     * @param MailerInterface $mailer
     * @return Invitation|FormInterface
     * @throws TransportExceptionInterface
     * @throws EntityNotFoundException
     */
    public function postInvitationAction(Request $request, QuizRepository $quizRepo, MailerInterface $mailer)
    {
        dump($_ENV['URL_BACKEND']);
        $invitation = new Invitation();
        $user = $this->getUser();
        $form = $this->createForm(InvitationType::class, $invitation, [
            'userId' => $user->getId(),
            'quizRepo' => $quizRepo
        ]);

        $form->submit($request->request->all());

        if ($invitation->getQuiz() === null) return $this->quizNotFound();

        if ($form->isValid()) {
            $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
            $invitation->setToken($token);
            $this->em->persist($invitation);
            $this->em->flush();

            $email = (new TemplatedEmail())
                ->from('invitation@quiz.factory.com')
                ->to($invitation->getEmail())
                ->subject('Invitation à participer à un Quiz')
                ->htmlTemplate('emails/invitation.html.twig')
                ->context([
                    'url' => $_ENV['URL_BACKEND'].'/execute/'.$invitation->getToken(),
                    'firstname' => $invitation->getFirstname(),
                    'lastname' => $invitation->getLastname(),
                    'quiz' =>  $invitation->getQuiz()->getName(),
                    'user' => $user->getUsername()
                ])
            ;
            $mailer->send($email);

            return $invitation;
        } else {
            return $form;
        }
    }

    private function quizNotFound()
    {
        throw new EntityNotFoundException('Quiz not found');
    }

    private function invitationNotFound()
    {
        throw new EntityNotFoundException('Invitation not found');
    }
}
