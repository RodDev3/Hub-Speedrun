<?php

namespace App\Form\Categories;

use App\Entity\Categories\Categories;
use App\Entity\Games\Games;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => [
                  'class' => 'form-control',
                'placeholder' => 'Name'
                ],
            ])
            ->add('rules', TextareaType::class,[
                'label' => 'Rules',
            ])
            ->add('players', IntegerType::class, [
                'label' => 'Number of players',
            ])
            /*->add('refGames', EntityType::class, [
                'class' => Games::class,
                'choice_label' => 'name',
            ])*/
            ->add('refCategories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categories::class,
        ]);
    }
}
