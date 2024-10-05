<?php

namespace App\Form;

use App\Entity\Password;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

class PasswordGenerateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('length', NumberType::class,[
                'label'=>false,
                'constraints'=> [
                    new GreaterThan([
                        'value'=>0,
                        'message'=> 'The length must greater than 0'
                    ])
                ]
            ])
            ->add('numbers', CheckboxType::class,[
                'label'=> false,
                'required'=> false,
                'value'=>true,
            ])
            ->add('specialk', CheckboxType::class,[
                'label'=> false,
                'required'=> false,
                'value'=>true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {

    }
}
