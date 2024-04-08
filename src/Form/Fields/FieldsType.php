<?php

namespace App\Form\Fields;

use App\Entity\Categories\Categories;
use App\Entity\Fields\Fields;
use App\Entity\FieldTypes\FieldTypes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('display')
            ->add('quickFilter')
            ->add('config')
            ->add('refCategories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'id',
            ])
            ->add('refFieldTypes', EntityType::class, [
                'class' => FieldTypes::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fields::class,
        ]);
    }
}
