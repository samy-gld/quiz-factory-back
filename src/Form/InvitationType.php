<?php

namespace App\Form;

use App\Entity\Invitation;
use App\Entity\Quiz;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', TextType::class)
            ->add('quiz', IntegerType::class)
            /* DataTransformer */
            ->get('quiz')
            ->addModelTransformer(
                new CallbackTransformer(
                    function ($quiz) {
                        return $quiz;
                    },
                    function ($quiz) use ($options) {
                        return $options['quizRepo']->findOneQuiz($quiz, $options['userId']);
                    }
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Invitation::class,
            'csrf_protection' => false,
            'userId' => null,
            'quizRepo' => null
        ]);
    }
}
