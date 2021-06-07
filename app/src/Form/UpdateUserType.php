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
use Vich\UploaderBundle\Form\Type\VichImageType;

class UpdateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('photoFile',VichImageType::class,[
                'label' => 'Photo de profil',
                'required' => false,
                'allow_delete' => true,
                'image_uri' => true,
                'download_uri' => false,
                'attr' => [
                    "class" => 'form-control mb-2'
                ]
            ])
            ->add('couvertureFile',VichImageType::class,[
                'label' => 'Photo de couverture',
                'required' => false,
                'allow_delete' => true,
                'image_uri' => true,
                'download_uri' => false,
                'attr' => [
                    "class" => 'form-control mb-2'
                ]
            ])
            ->add('old_password', PasswordType::class,[
                'invalid_message' => 'Le mot de passe ne correspond pas à l\'ancien',
                'mapped' => false,
                'label' => 'Votre ancien mot de passe',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ancien mot de passe',
                    'class' => 'form-control mb-2'
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identique.',
                'required' => false,
                'constraints' => new Length([
                    'min' => 8,
                    'max' => 16,
                ]),
                'first_options' => [
                    'label' => 'Votre nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Nouveau mot de passe',
                        'class' => 'form-control mb-2'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe',
                    'attr' => [
                        'placeholder' => 'Confirmez votre mot de passe',
                        'class' => 'form-control mb-2'
                    ]
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier',
                'attr' => [
                    'class' => 'btn btn-primary btn-block mb-5',
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
