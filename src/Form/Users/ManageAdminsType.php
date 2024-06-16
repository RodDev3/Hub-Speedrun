<?php

namespace App\Form\Users;

use App\Entity\Users\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ManageAdminsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', EntityType::class,[
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Username',
                'class' => Users::class,
                'choice_label' => 'username',
                'autocomplete' => true,
            ])

            ->add('roles', ChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                'choices'=> [
                    '- - -' => null,
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER'
                ],
                'label' => 'Role',
                'autocomplete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}