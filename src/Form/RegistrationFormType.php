<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', options: [
                'label' => 'Adresse E-mail'
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions d\'utilisation',
                    ]),
                ],
                'label' => 'Accepter les conditions d\'utilisation'
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'insérer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'label' => "Mot de passe",
            ])
            ->add('first_name', options: [
                'label' => 'Prénom'
            ])
            ->add('last_name', options: [
                'label' => 'Nom'
            ])
            ->add('phone_number', options: [
                'label' => 'Numéro de téléphone'
            ])
            ->add('postal_adress', options: [
                'label' => 'Adresse postale'
            ])
            ->add('birth_date', options: [
                'label' => 'Date de naissance'
            ])
            ->add('username', options: [
                'label' => 'Nom d\'utilisateur'
            ])
            ->add('roles', ChoiceType::class, options: [
                'choices' => [
                    'Passager' => 'ROLE_PASSAGER',
                    'Chauffeur' => 'ROLE_DRIVER',
                    'Les deux' => 'twice',
                ],
                'mapped' => false,
                'placeholder' => 'Choisir son type de profil',
                'label' => 'Choisir son profil',
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
