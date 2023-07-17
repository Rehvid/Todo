<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\Priority;
use App\Enum\UserRoles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'john@gmail.com.'
                ]
            ])
            ->add('password', PasswordType::class, [
                'hash_property_path' => 'password',
                'mapped' => false,
                'required' => $options['is_password_required'],
                'attr' => [
                    'placeholder' => '******'
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => UserRoles::toArray(),
                'multiple' => true,
                'expanded' => true,
                'required' => true,
            ])
            ->add('firstname', TextType::class, [
                'attr' => [
                    'placeholder' => 'John'
                ]
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'placeholder' => 'Lennon'
                ]
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_password_required' => true,
        ]);
    }
}
