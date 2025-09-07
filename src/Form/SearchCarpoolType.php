<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchCarpoolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startPlace', options: [
                'label' => false,
                'attr' => [
                    'class' => 'text-center',
                    'placeholder' => 'Départ',
                ]
            ])
            ->add('endPlace', options: [
                'label' => false,
                'attr' => [
                    'class' => 'text-center',
                    'placeholder' => 'Destination',
                ]
            ])
            ->add('startDateTime', DateTimeType::class, options: [
                'widget' => 'single_text',
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
