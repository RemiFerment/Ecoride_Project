<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfilePictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('photo', FileType::class, [
            'required' => true,
            'constraints' => [
                new File([
                    'maxSize' => '16M',
                    'mimeTypes' => ['image/png', 'image/jpeg', 'image/webp', 'image/gif'],
                ])
            ],
        ])
            ->add('save', SubmitType::class, options: [
                'label' => 'Ajouter la photo'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
