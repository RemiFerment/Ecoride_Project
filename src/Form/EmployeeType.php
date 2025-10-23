<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;


class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', options: [
                'label' => 'Login',
                'attr' => [
                    'placeholder' => 'Modo1',
                    'minlength' => 5,
                    'maxlength' => 180,
                ],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 180,
                        'minMessage' => 'L\'adresse e-mail doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'L\'adresse e-mail ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => [],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'insérer un mot de passe',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ],
                'label' => "Mot de passe",
            ])
            ->add('first_name', options: [
                'label' => 'Prénom du modérateur',
                'attr' => [
                    'placeholder' => 'Le prénom du modérateur',
                    'minlength' => 2,
                    'maxlength' => 50,
                ],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le prénom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('last_name', options: [
                'label' => 'Nom du modérateur',
                'attr' => [
                    'placeholder' => 'Le nom du modérateur',
                    'minlength' => 2,
                    'maxlength' => 50,
                ],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('username', options: [
                'label' => 'Nom d\'utilisateur du modérateur',
                'attr' => [
                    'placeholder' => 'Le nom d\'utilisateur du modérateur',
                    'minlength' => 3,
                    'maxlength' => 50,
                ],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 50,
                        'minMessage' => 'Le nom d\'utilisateur doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom d\'utilisateur ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
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
