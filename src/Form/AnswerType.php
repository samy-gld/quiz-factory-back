<?php

namespace App\Form;

use App\Entity\Answer;
use App\Entity\Proposition;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('questionPosition', IntegerType::class)
            ->add('propositions', EntityType::class, [
                'class' => Proposition::class,
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true,
            'propositionRepo' => null
        ]);
    }
}
