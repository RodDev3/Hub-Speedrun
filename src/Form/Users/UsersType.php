<?php

namespace App\Form\Users;

use App\Entity\Runs\Runs;
use App\Entity\Users\Users;
use SebastianBergmann\CodeCoverage\Report\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class,[
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Username *'
            ])
            ->add('email', EmailType::class,[
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Email *'
            ])
            ->add('password', PasswordType::class,[
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Password *'
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
