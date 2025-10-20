<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('first_name')
            ->add('last_name')
            ->add('phone_number')
            ->add('postal_adress')
            ->add('birth_date', null, [
                'widget' => 'single_text'
            ])
            ->add('username')
            ->add('roles', ChoiceType::class, options: [
                'choices' => [
                    'Passager' => 'ROLE_PASSAGER',
                    'Chauffeur' => 'ROLE_DRIVER',
                    'Les deux' => 'twice',
                ],
                'mapped' => false,
                'placeholder' => 'Choisir son type de profil',
                'label' => 'Choisir son profil',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
