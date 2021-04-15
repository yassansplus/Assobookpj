<?php

namespace App\Form;

use App\Entity\Association;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class AssociationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
                'label' => 'Le nom de votre association',
                'required' => true,
                'constraints' => new Length([
                    'min' => 3,
                    'max' => 60
                ]),
                'attr' => [
                    'placeholder' => 'My Assoc',
                    'class' => 'form-control',
                ]
            ])
            ->add('description',TextareaType::class,[
                'label'=> 'Description de votre association',
                'required' => true,
                'constraints' => new Length([
                    'min' => 10,
                    'max' => 250,
                ]),
                'attr' => [
                    'placeholder' => 'Décrivez votre association...',
                    'class' => 'form-control',
                ]
            ])
            ->add('adress',AddressType::class,[
                'mapped' => false,
                'label' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider mon association',
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
            'data_class' => Association::class,
        ]);
    }
}
