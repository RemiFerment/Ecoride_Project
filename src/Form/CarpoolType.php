<?php

namespace App\Form;

use App\Entity\Carpooling;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarpoolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start_date', null, [
                'widget' => 'single_text'
            ])
            ->add('start_hour', null, [
                'widget' => 'single_text'
            ])
            ->add('start_place')
            ->add('end_date', null, [
                'widget' => 'single_text'
            ])
            ->add('end_hour', null, [
                'widget' => 'single_text'
            ])
            ->add('end_place')
            ->add('statut')
            ->add('avaible_seat')
            ->add('price_per_person')
            ->add('create_by')
            ->add('car_id')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Carpooling::class,
        ]);
    }
}
