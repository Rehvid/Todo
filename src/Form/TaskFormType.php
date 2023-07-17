<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\User;
use App\Enum\Priority;
use App\Enum\Status;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter title...'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter description...'
                ]
            ])
            ->add('deadline', DateTimeType::class)
            ->add('priority', EnumType::class, [
                'class' => Priority::class,
                'choice_label' => fn ($choice) => strtolower($choice->name)
            ])
            ->add('status', EnumType::class, [
                'class' => Status::class,
                'choice_label' => fn ($choice) => strtolower($choice->name)
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => fn (User $user) => 'ID: '. $user->getId() . ' '. $user->getFullName()
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'users' => null,
        ]);
    }
}
