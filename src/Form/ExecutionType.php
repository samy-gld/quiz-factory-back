<?php

namespace App\Form;

use App\Entity\Execution;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExecutionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('started', CheckboxType::class)
            ->add('finished', CheckboxType::class)
            ->add('currentPosition', IntegerType::class)
            ->add('invitation')
            /* DataTransformer */
            ->get('invitation')
            /* token is send as invitation => we find the corresponding invitation */
            ->addModelTransformer(
                new CallbackTransformer(
                    function ($invitation) {
                        return $invitation;
                    },
                    function ($invitation) use ($options) {
                        $findInvitation = $options['invitationRepo']->findOneBy(['token' => $invitation]);
                        if (empty($findInvitation)) throw new EntityNotFoundException('Invitation not found');
                        if ($findInvitation->getExecution() !== null) throw new HttpException(409, 'An execution already exists for this invitation');
                        return $findInvitation;
                    }
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Execution::class,
            'csrf_protection' => false,
            'invitationRepo' => null
        ]);
    }
}
