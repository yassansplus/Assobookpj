<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UpdatePwdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('old_password', PasswordType::class,[
                'invalid_message' => 'Le mot de passe ne correspond pas à l\'ancien',
                'mapped' => false,
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ancien mot de passe',
                    'class' => 'form-control'
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identique.',
                'required' => true,
                'constraints' => new Length([
                    'min' => 8,
                    'max' => 16,
                ]),
                'first_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Nouveau mot de passe',
                        'class' => 'form-control'
                    ]
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Confirmez votre mot de passe',
                        'class' => 'form-control'
                    ]
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Modfier',
                'attr' => [
                    'class' => 'btn btn-primary btn-block',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
