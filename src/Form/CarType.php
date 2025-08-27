<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Marque;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('model', options: [
                'label' => 'Modèle : '
            ])
            ->add('registration', options: [
                'label' => 'Numéro d\'immatriculation : '
            ])
            ->add('power_engine', options: [
                'label' => 'Moteur : '
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
                    'placeholder' => 'Insérer une marque',
                    'label' => 'Marque de voiture : '
                ]
            )
            ->add('color', options: [
                'label' => 'Couleur : '
            ])
            ->add('save', SubmitType::class, [
                'label' => "Ajouter la voiture",
            ],)
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
}
