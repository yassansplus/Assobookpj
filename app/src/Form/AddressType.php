<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Paris',
                    'class' => 'form-control',
                ]
            ])
            ->add('postalCode', IntegerType::class, [
                'label' => 'Code postal',
                'attr' => [
                    'placeholder' => '75002',
                    'class' => 'form-control',
                    'min' => 0
                ]
            ])
            ->add('street',TextType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => '10 rue de paris',
                    'class' => 'form-control',
                ]
            ])
            ->add('region',TextType::class, [
                'label' => 'Région',
                'attr' => [
                    'placeholder' => 'Île-De-France',
                    'class' => 'form-control',
                ]
            ])
            ->add('country',TextType::class, [
                'label' => 'Pays',
                'attr' => [
                    'placeholder' => 'France',
                    'class' => 'form-control',
                ]
            ])
            ->add('latitude',TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => '48.11125775',
                    'class' => 'form-control',
                    'hidden' => true,
                ]
            ])
            ->add('longitude',TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => '2.1111478',
                    'class' => 'form-control',
                    'hidden' => true,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
