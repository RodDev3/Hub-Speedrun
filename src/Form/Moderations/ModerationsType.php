<?php

namespace App\Form\Moderations;

use App\Entity\Games\Games;
use App\Entity\Moderations\Moderations;
use App\Entity\Roles\Roles;
use App\Entity\Users\Users;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModerationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('refUsers', EntityType::class, [
                'attr' => ['class' => 'form-control'],
                'class' => Users::class,
                'placeholder' => 'Select a user',
                'choice_label' => 'username',
                'label' => 'User'
            ])
            ->add('refRoles', EntityType::class, [
                'attr' => ['class' => 'form-control'],
                'class' => Roles::class,
                'placeholder' => '- - -',
                'choice_label' => 'name',
                'label' => 'Role',
                'autocomplete' => true,
            ])
            ->add('refGames', HiddenType::class, [
                'data' => $options['game']->getUuid(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Moderations::class,
            'game' => Games::class,
        ]);
        $resolver->setRequired('game');
    }
}
