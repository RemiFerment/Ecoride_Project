<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfilePictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('photo', FileType::class, [
            'required' => true,
            'label' => false,
            'attr' => [
                // This restricts the file picker in browsers
                'accept' => 'image/png, image/jpeg, image/webp',
                'id' => 'photoForm',
                'class' => 'd-none'
            ],
            'constraints' => [
                new File([
                    'maxSize' => '16M',
                    'mimeTypes' => [
                        'image/png',
                        'image/jpeg',
                        'image/webp'
                    ],
                    'mimeTypesMessage' => 'Veuillez sélectionner une image (PNG, JPEG, WEBP).',
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
