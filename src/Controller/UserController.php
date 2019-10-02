<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class UserController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * UserController constructor.
     * @param ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"user"})
     * @Rest\Post(path="/register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param TokenGeneratorInterface $tokenGenerator
     * @param MailerInterface $mailer
     * @return FormInterface
     * @throws TransportExceptionInterface
     */
    public function postUserAction(Request $request,
                                   UserPasswordEncoderInterface $encoder,
                                   TokenGeneratorInterface $tokenGenerator,
                                   MailerInterface $mailer)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all());

        if($form->isValid()) {
            $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encoded);
            $user->setEnabled(false);
            $token = $tokenGenerator->generateToken();
            $user->setConfirmationToken($token);

            $this->em->persist($user);
            $this->em->flush();

            $email = (new TemplatedEmail())
                ->from('inscription@quiz.factory.com')
                ->to($user->getEmail())
                ->subject('Confirmation d\'inscription à Quiz Factory')
                ->htmlTemplate('emails/registration.html.twig')
                    ->context([
                        'url' => $_ENV['URL_FRONTEND'].'/confirm/'.$token,
                        'username' => $user->getUsername(),
                    ])
                ;
            $mailer->send($email);

            return $user;
        } else {
            $errorUsername = $form['username']->getErrors(true, false);
            $errorEmail = $form['email']->getErrors(true, false);
            $message = '';

            if (count($errorUsername) > 0) {
                if ($errorUsername[0]->getMessage() === 'Username already used') {
                    $message = $errorUsername[0]->getMessage();
                }
            }

            if (count($errorEmail) > 0) {
                if ($errorEmail[0]->getMessage() === 'Email already used') {
                    $message = $errorEmail[0]->getMessage();
                }
            }

            if ($message !== '') {
                throw new HttpException(409, $message);
            }

            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"user"})
     * @Rest\Post(path="/confirm/{token}")
     * @param $token
     * @param Request $request
     * @param UserRepository $userRepo
     * @return RedirectResponse|Response|null
     */
    public function confirmAction($token, UserRepository $userRepo)
    {
        $user = $userRepo->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException('No user with this confirmation token');
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);
        $this->em->merge($user);
        $this->em->flush();

        return $user;
    }
}
