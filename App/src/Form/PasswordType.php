<?php

namespace App\Form;

use App\Entity\Password;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('local')
            ->add('description')
            ->add('password')
            ->add('securityLevel', ChoiceType::class,[
                'choices'=>[
                    'Low'=>'low',
                    'Medium'=>'medium',
                    'High'=>'high',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Password::class,
        ]);
    }
}
