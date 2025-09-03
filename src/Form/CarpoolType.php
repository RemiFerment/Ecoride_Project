<?php

namespace App\Form;

use App\Entity\Carpooling;
use App\Validator\CityCheck;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarpoolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start_date', null, [
                'widget' => 'single_text',
                'label' => 'Jour de départ'
            ],)
            ->add('start_hour', null, [
                'widget' => 'single_text',
                'label' => 'Heure de départ',
                'html5' => true,
            ])
            ->add('start_place', options: [
                'label' => 'Lieu de départ'
            ])
            ->add('end_place', options: [
                'label' => 'Destination'
            ])
            ->add('avaible_seat', options: [
                'label' => 'Nombre de siège'
            ])
            ->add('price_per_person', options: [
                'label' => 'Prix de la place (en Écopièce)'
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
