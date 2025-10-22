<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Marque;
use App\Validator\CityCheck;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('model', options: [
                'label' => 'Modèle : ',
            ])
            ->add('power_engine', ChoiceType::class, options: [
                'label' => 'Type de moteur : ',
                'choices' => [
                    'Électrique' => 'Electrique',
                    'Hybride' => 'Hybride',
                    'Essence' => 'Essence',
                    'Diesel' => 'Diesel'
                ],
                'placeholder' => 'Choisir un type de moteur',
                'constraints' => [
                    new Choice([
                        'choices' => ['Electrique', 'Hybride', 'Essence', 'Diesel'],
                        'message' => 'Veuillez choisir un type de moteur valide.',
                    ]),
                    new NotBlank([
                        'message' => 'Le type de moteur est obligatoire.',
                    ])
                ]
            ])
            ->add('first_date_registration', null, [
                'widget' => 'single_text',
                'label' => 'Première date d\'immatriculation : ',
                'attr' => [
                    'max' => (new \DateTime())->format('Y-m-d'),
                ],
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => (new \DateTime())->format('Y-m-d'),
                        'message' => 'La date ne peut pas être dans le futur.',
                    ]),
                ],
            ])
            ->add(
                'marque',
                EntityType::class,
                [
                    'class' => Marque::class,
                    'choice_label' => 'name',
                    'placeholder' => 'Choisir une marque',
                    'label' => 'Marque de voiture : '
                ]
            )
            ->add('color', ChoiceType::class, options: [
                'label' => 'Couleur : ',
                'choices' => [
                    'Blanc' => 'Blanc',
                    'Noir' => 'Noir',
                    'Gris' => 'Gris',
                    'Argent' => 'Argent',
                    'Bleu' => 'Bleu',
                    'Rouge' => 'Rouge',
                    'Vert' => 'Vert',
                    'Jaune' => 'Jaune',
                    'Orange' => 'Orange',
                    'Marron' => 'Marron',
                ],
                'placeholder' => 'Choisir une couleur',
                'constraints' => [
                    new Choice([
                        'choices' => ['Blanc', 'Noir', 'Gris', 'Argent', 'Bleu', 'Rouge', 'Vert', 'Jaune', 'Orange', 'Marron'],
                        'message' => 'Veuillez choisir une couleur valide.',
                    ]),
                    new NotBlank([
                        'message' => 'La couleur est obligatoire.',
                    ])
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => "Ajouter la voiture",
                'attr' => ["class" => "btn btn-success"]
            ],)
        ;
        if (!$options['edit']) {
            $builder->add('registration', options: [
                'label' => 'Numéro d\'immatriculation : ',
                'attr' => [
                    'maxlength' => 10,
                    'minlength' => 3,
                    'pattern' => '[A-Z0-9-]+'
                ],
                'constraints' => [

                    new NotBlank([
                        'message' => 'Le numéro d\'immatriculation est obligatoire.',
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 10,
                        'minMessage' => 'Le numéro d\'immatriculation doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le numéro d\'immatriculation ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    new Regex([
                        'pattern' => '/^[A-Z0-9-]+$/',
                        'message' => 'Le numéro d\'immatriculation ne peut contenir que des lettres majuscules, des chiffres et des tirets.',
                    ]),
                ],
            ]);
        } else {
            $builder->add('registration', options: [
                'label' => 'Numéro d\'immatriculation : ',
                'disabled' => true,
            ]);
        }
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
            'edit' => false,
        ]);
    }
}
