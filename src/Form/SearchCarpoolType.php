<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Choice;
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
            ->add('filter_grade', IntegerType::class, options: [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'text-center',
                    'placeholder' => 'Note minimale',
                    'min' => 0,
                    'max' => 5,
                ]
            ])
            ->add('filter_price', IntegerType::class, options: [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'text-center',
                    'placeholder' => 'Prix maximum',
                    'min' => 1,
                ]
            ])
            ->add('filter_preferences', ChoiceType::class, options: [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices'  => [
                    'Animaux acceptés' => 'animals_allowed',
                    'Fumeur accepté' => 'smoker_allowed',
                ],
                'attr' => [
                    'class' => 'd-flex flex-column justify-content-center gap-3',
                ]
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
