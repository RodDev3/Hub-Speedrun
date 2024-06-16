<?php

namespace App\Form\Games;

use App\Entity\Games\Games;
use App\Entity\Series\Series;
use App\Entity\Supports\Supports;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GamesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'attr'=> ['class'=>'form-control'],
                'label' => 'Nom *',
                'required' => true,
            ])
            ->add('releaseDate', DateType::class, [
                'attr'=> ['class'=>'form-control'],
                'label' => 'Release date *',
                'widget' => 'single_text',
                'required' => true
            ])
            ->add('discordLink', UrlType::class,[
                'attr'=> ['class'=>'form-control'],
                'required' => false,
            ])
            ->add('image', UrlType::class,[
                'attr'=> ['class'=>'form-control'],
                'label' => 'Image *',
                'required' => true
            ])
            /*->add('rewrite')*/
            ->add('refSupports', EntityType::class, [
                'class' => Supports::class,
                'label' => 'Supports *',
                'choice_label' => 'name',
                'multiple' => true,
                'autocomplete' => true
            ])
            /*->add('refSeries', EntityType::class, [
                'class' => Series::class,
                'choice_label' => 'id',
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Games::class,
        ]);
    }
}
