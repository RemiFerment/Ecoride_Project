<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Choice;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', options: [
                'label' => 'Adresse E-mail',
                'attr' => [
                    'placeholder' => 'exemple@mail.com',
                    'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$',
                    'minlength' => 5,
                    'maxlength' => 180,
                ],
                'constraints' => [
                    new Email([
                        'message' => 'L\'adresse e-mail "{{ value }}" n\'est pas valide.',
                    ]),
                ],
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
                'attr' => [
                    'autocomplete' => 'new-password',
                    //pattern have to : at least 8 characters, one uppercase letter, one lowercase letter, one digit and one special character
                    'pattern' => '(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'insérer un mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$/',
                        'message' => 'Le mot de passe doit contenir au moins 8 caractères, une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.',
                    ]),
                ],
                'label' => "Mot de passe",
            ])
            ->add('first_name', options: [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Votre prénom',
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
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Votre nom',
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
            ->add('phone_number', options: [
                'label' => 'Numéro de téléphone',
                'attr' => [
                    'placeholder' => '06 12 34 56 78',
                    'pattern' => '^(0|\+33)[1-9]( *[0-9]{2}){4}$',
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(0|\+33)[1-9]( *[0-9]{2}){4}$/',
                        'message' => 'Le numéro de téléphone "{{ value }}" n\'est pas valide.',
                    ]),
                ],
            ])
            ->add('postal_adress', options: [
                'label' => 'Adresse postale',
                'attr' => [
                    'placeholder' => '123 Rue Exemple, 75000 Paris',
                    'minlength' => 5,
                    'maxlength' => 255,
                ],
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'max' => 255,
                        'minMessage' => 'L\'adresse postale doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'L\'adresse postale ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('birth_date', options: [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'attr' => [
                    'max' => (new \DateTime())->format('Y-m-d'),
                ],
                'constraints' => [
                    new LessThan([
                        'value' => (new \DateTime())->format('Y-m-d'),
                        'message' => 'La date de naissance doit être antérieure à aujourd\'hui.',
                    ]),
                ],
            ])
            ->add('username', options: [
                'label' => 'Nom d\'utilisateur',
                'attr' => [
                    'placeholder' => 'Votre nom d\'utilisateur',
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
            ->add('roles', ChoiceType::class, options: [
                'choices' => [
                    'Passager' => 'ROLE_PASSAGER',
                    'Chauffeur' => 'ROLE_DRIVER',
                    'Les deux' => 'twice',
                ],
                'mapped' => false,
                'placeholder' => 'Choisir son type de profil',
                'label' => 'Choisir son profil',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir un type de profil.'
                    ]),
                    new Choice([
                        'choices' => ['ROLE_PASSAGER', 'ROLE_DRIVER', 'twice'],
                        'message' => 'Veuillez choisir un type de profil valide.',
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
