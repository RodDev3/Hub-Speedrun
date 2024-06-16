<?php

namespace App\Form\Supports;

use App\Entity\Games\Games;
use App\Entity\Supports\Supports;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function MongoDB\BSON\toRelaxedExtendedJSON;

class SupportsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name *',
                'attr' => ['class' => 'form-control'],
                'required' => true
            ])
            /*->add('refGames', EntityType::class, [
                'class' => Games::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Supports::class,
        ]);
    }
}
