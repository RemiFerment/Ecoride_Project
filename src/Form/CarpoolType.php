<?php

namespace App\Form;

use App\Entity\Carpooling;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class CarpoolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start_date', DateTimeType::class, [
                'label' => 'Jour de départ',
                'widget' => 'single_text',
                'with_seconds' => false,
                'attr' => [
                    'min' => (new \DateTime('+2 hours'))->format('Y-m-d\TH:i'),
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => new \DateTime('+2 hours'),
                        'message' => "La date de départ doit être aujourd'hui ou une date future.",
                    ]),
                ],
            ])
            ->add('start_place', options: [
                'label' => 'Ville de départ'
            ])
            ->add('end_place', options: [
                'label' => 'Ville d\'arrivé'
            ])
            ->add('available_seat', options: [
                'label' => 'Nombre de siège',
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\Range([
                        'min' => 1,
                        'max' => 8,
                        'notInRangeMessage' => 'Le nombre de siège doit être entre {{ min }} et {{ max }}.',
                    ]),
                ],
                'attr' => [
                    'min' => 1,
                    'max' => 8,
                ],
            ])
            ->add('price_per_person', options: [
                'label' => 'Prix de la place (en Écopièce)',
                'attr' => [
                    'min' => 0,
                ],
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Le prix doit être supérieur ou égal à {{ compared_value }}.',
                    ]),
                ],
            ])
            ->add('startAdress', options: [
                'label' => 'Adresse de départ'
            ])
            ->add('endAdress', options: [
                'label' => 'Adresse d\'arrivée'
            ])
            ->add('save', SubmitType::class, [
                'label' => "Créer le trajet",
                'attr' => ["class" => "btn btn-success"]
            ],)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Carpooling::class,
        ]);
    }
}
