<?php

namespace App\Form;

use App\Entity\Games\Games;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*->add('search', EntityType::class, [
                'class' => Games::class,
                'placeholder' => 'Search',
                'choice_label' => 'name',
                'autocomplete' => true,
            ])*/
            ->add('search', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Search'],
                'label' => 'Search',

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
