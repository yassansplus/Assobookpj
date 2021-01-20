<?php

namespace App\Form;

use App\Entity\Adherent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class AdherentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => false,
                'required' => true,
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 30
                ]),
                'attr' => [
                    'placeholder' => 'Prénom',
                    'class' => 'form-control',
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => false,
                'required' => true,
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 60
                ]),
                'attr' => [
                    'placeholder' => 'Nom',
                    'class' => 'form-control',
                ]
            ])
            ->add('gender',ChoiceType::class, [
                'label' => false,
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Masculin' => true,
                    'Feminin' => false,
                ],
                'attr'=> [
                    'class' => 'formGender'
                ]
            ])
            ->add('birthday',DateType::class,[
                'label' => false,
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider mon profil',
                'attr' => [
                    'class' => 'btn btn-block btn-primary',
                    'style' => 'margin-top:10px;'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Adherent::class,
        ]);
    }
}
