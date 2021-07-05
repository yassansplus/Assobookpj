<?php

namespace App\Form;

use App\Entity\Association;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UpdateAssocType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
                'label' => 'Le nom de votre association',
                'required' => false,
                'constraints' => new Length([
                    'min' => 3,
                    'max' => 60
                ]),
                'attr' => [
                    'placeholder' => 'My Assoc',
                    'class' => 'form-control mb-2',
                ]
            ])
            ->add('description',TextareaType::class,[
                'label'=> 'Description de votre association',
                'required' => false,
                'constraints' => new Length([
                    'min' => 10,
                    'max' => 250,
                ]),
                'attr' => [
                    'placeholder' => 'Décrivez votre association...',
                    'class' => 'form-control mb-2',
                ]
            ])
            ->add('website',TextType::class,[
                'label' => 'Votre site internet (facultatif)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'http://...',
                    'class' => 'form-control mb-2',
                ]
            ])
            ->add('haveCagnotte',CheckboxType::class,[
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'custom-control-input',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier',
                'attr' => [
                    'class' => 'btn btn-primary btn-block',
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