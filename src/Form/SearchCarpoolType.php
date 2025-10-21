<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class SearchCarpoolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startPlace', TextType::class, options: [
                'label' => false,
                'attr' => [
                    'class' => 'text-center',
                    'placeholder' => 'Départ',
                ]
            ])
            ->add('endPlace', TextType::class, options: [
                'label' => false,
                'attr' => [
                    'class' => 'text-center',
                    'placeholder' => 'Destination',
                ]
            ])
            ->add('startDateTime', DateTimeType::class, options: [
                'widget' => 'single_text',
                'label' => false,
                'data' => new \DateTimeImmutable('now'),
                'html5' => true,
                'attr' => [
                    'min' => (new \DateTimeImmutable('now'))->format('Y-m-d\TH:i'),
                    'class' => 'text-center',
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date doit être postérieure à aujourd\'hui.',
                    ]),
                ],
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
