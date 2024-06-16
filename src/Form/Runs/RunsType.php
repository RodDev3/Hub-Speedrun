<?php

namespace App\Form\Runs;

use App\Entity\Categories\Categories;
use App\Entity\Games\Games;
use App\Entity\Runs\Runs;
use App\Entity\Status\Status;
use App\Entity\Users\Users;
use App\Repository\Categories\CategoriesRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RunsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {


        $builder
            ->add('refCategories', EntityType::class, [
                'attr' => ['class' => 'form-select'],
                'class' => Categories::class,
                'query_builder' => function (CategoriesRepository $categoriesRepository) use ($options): QueryBuilder {
                    return $categoriesRepository->createQueryBuilder('c')
                        ->join('c.refGames', 'g', 'WITH', 'g.id = c.refGames')
                        ->where('g.id = :id')
                        ->setParameter('id', $options['game']->getId());
                },
                'choice_label' => 'name',
                'choice_value' => 'uuid',
                'label' => 'Category *',
            ])
            ->add('game', HiddenType::class, [
                'data' => $options['game']->getUuid(),
                'mapped' => false
            ])
            ->add('dateMade', DateType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Date *',
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
                'label' => 'Description',
            ])
            ->add('video', UrlType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' =>false,
                'label' => 'Video',
            ])
            ->add('modNotes', TextareaType::class, [
                "attr"=>[
                    "class"=>"form-control",
                ],
                'help' => 'Required if the run is rejected',
                "label" => "Mod Notes",
                'required' => false
            ])
            ->add('uuid' , HiddenType::class, [
                'data' => $options['uuid'],
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Runs::class,
            'game' => null,
            'uuid' => null
        ]);

        $resolver->setDefined(['game']);
    }
}
