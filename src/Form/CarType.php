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

            ])
            ->add('first_date_registration', null, [
                'widget' => 'single_text',
                'label' => 'Première date d\'immatriculation : '
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
                'placeholder' => 'Choisir une couleur'
            ])
            ->add('save', SubmitType::class, [
                'label' => "Ajouter la voiture",
                'attr' => ["class" => "btn btn-success"]
            ],)
        ;
        if (!$options['edit']) {
            $builder->add('registration', options: [
                'label' => 'Numéro d\'immatriculation : '
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
