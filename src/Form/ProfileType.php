<?php

namespace App\Form;

use App\Entity\User;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Choice as ChoiceConstraint;
use Symfony\Component\Validator\Constraints\LessThan;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'label' => 'Adresse e-mail',
                'attr' => [
                    'placeholder' => 'exemple@mail.com',
                    'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$',
                ],
                'constraints' => [
                    new Email([
                        'message' => 'L\'adresse e-mail "{{ value }}" n\'est pas valide.',
                    ]),
                ],
            ])
            ->add('first_name', null, [
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
            ->add('last_name', null, [
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
            ->add('phone_number', null, [
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
            ->add('postal_adress', null, [
                'label' => 'Adresse postale',
                'attr' => [
                    'placeholder' => '123 Rue Exemple, 75000 Paris',
                    'minlength' => 5,
                    'maxlength' => 100,
                ],
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'max' => 100,
                        'minMessage' => 'L\'adresse postale doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'L\'adresse postale ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('birth_date', null, [
                'widget' => 'single_text',
                'label' => 'Date de naissance',
                'attr' => [
                    'max' => (new DateTime())->format('Y-m-d'),
                ],
                'constraints' => [
                    new LessThan([
                        'value' => (new DateTime())->format('Y-m-d'),
                        'message' => 'La date de naissance doit être dans le passé.',
                    ]),
                ],
            ])
            ->add('username', null, [
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
                'required' => false,
                'constraints' => [
                    new ChoiceConstraint([
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
