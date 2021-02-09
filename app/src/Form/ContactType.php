<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 60
                ]),
                'attr' => [
                    'placeholder' => 'Email',
                    'class' => 'form-control',
                ]
            ])
            ->add('nom', null, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom',
                    'class' => 'form-control',
                    'aria-label' => 'Nom'
                ],
                'constraints' => new Length([
                    'min' => 1
                ]),
            ])
            ->add('prenom', null, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prénom',
                    'class' => 'form-control',
                    'aria-label' => 'Prénom'
                ],
                'constraints' => new Length([
                    'min' => 1
                ]),
            ])
            ->add('message', TextareaType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Bonjour l\'équipe Assobook, j\'aimerai vous dire...',
                    'class' => 'form-control',
                    'aria-label' => 'Message'
                ],
            ]);
    }
}
