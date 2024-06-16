<?php

namespace App\Form\Categories;

use App\Entity\Categories\Categories;
use App\Entity\Games\Games;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UuidType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Uid\Uuid;

class CategoriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name', TextType::class, [
                'label' => 'Name *',
                'attr' => [
                  'class' => 'form-control',
                ],
                'required' => true
            ])
            ->add('rules', TextareaType::class,[
                'attr' => ['class' => 'form-control'],
                'label' => 'Rules',
                'required' => true
            ])
            ->add('players', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Number of players *',
                'required' => true,
                'help' => 'Number of players cannot be modified',
            ])

            ->add('refGames', HiddenType::class, [
                'data' => $options['game']->getUuid(),
                'mapped' => false
            ])

            ->add('videoMandatory', CheckboxType::class, [
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label'],
                'label' => 'Video Mandatory ? *',
                'required' => false
            ]);
            /*->add('refCategories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'id',
            ])*/

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categories::class,
            'allow_extra_fields' => true,
            'game' => null,
        ]);
    }
}
