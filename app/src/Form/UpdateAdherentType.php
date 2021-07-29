<?php

namespace App\Form;

use App\Entity\Adherent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdateAdherentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom',
                'required' => false,
                'empty_data' => '',
                'constraints' => [new Length([
                    'min' => 2,
                    'max' => 30
                ]),
                    new NotBlank(),],
                'attr' => [
                    'placeholder' => 'Prénom',
                    'class' => 'form-control',
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
                'required' => false,
                'empty_data' => '',
                'constraints' => [new Length([
                    'min' => 2,
                    'max' => 30
                ]),
                    new NotBlank(),],
                'attr' => [
                    'placeholder' => 'Nom',
                    'class' => 'form-control',
                ]
            ])
            ->add('birthday', DateType::class, [
                'label' => 'Date d\'anniversaire',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control mt-2',
                ],
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Bio',
                'required' => false,
                'constraints' => new Length([
                    'min' => 5,
                    'max' => 140
                ]),
                'attr' => [
                    'placeholder' => 'J\'aime ...',
                    'class' => 'form-control',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier',
                'attr' => [
                    'class' => 'btn btn-block btn-primary mt-2',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Adherent::class,
        ]);
    }
}
