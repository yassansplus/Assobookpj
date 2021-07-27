<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Event;
use App\Entity\Publication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
            ])
            ->add('endingDate', DateTimeType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('Publication', PublicationType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
