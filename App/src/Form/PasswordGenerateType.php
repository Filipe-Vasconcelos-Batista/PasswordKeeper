<?php

namespace App\Form;

use App\Entity\Password;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordGenerateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('securityLevel', ChoiceType::class,[
                'choices'=>[
                    'Low'=>'low',
                    'Medium'=>'medium',
                    'High'=>'high',
                ],
            ])
            ->add('local')
            ->add('description')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Password::class,
        ]);
    }
}
