<?php

namespace App\Form\Games;

use App\Entity\Games\Games;
use App\Entity\Series\Series;
use App\Entity\Supports\Supports;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GamesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('releaseDate', null, [
                'widget' => 'single_text',
            ])
            ->add('discordLink')
            ->add('image')
            ->add('rewrite')
            ->add('refSupports', EntityType::class, [
                'class' => Supports::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('refSeries', EntityType::class, [
                'class' => Series::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Games::class,
        ]);
    }
}
