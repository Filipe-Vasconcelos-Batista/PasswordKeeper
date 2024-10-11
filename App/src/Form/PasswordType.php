<?php

namespace App\Form;

use App\Entity\Password;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('password', TextType::class,[
                'label' => false,

                'attr'=>[
                    'class'=>'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
        ])],
            ])
            ->add('securityLevel', ChoiceType::class,[
                'label' => false,
                'choices'=>[
                    'Low'=>'low',
                    'Medium'=>'medium',
                    'High'=>'high',
                ],
                'attr'=>[
                    'class'=>'form-select'
                ]
            ])
            ->add('local',TextType::class,[
                'label' => false,
                'attr'=>[
                    'class'=>'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a Title',
                    ])],
            ])
            ->add('description',TextareaType::class,[
                'label' => false,
                'attr'=>[
                    'class'=>'form-control',
                ]
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
