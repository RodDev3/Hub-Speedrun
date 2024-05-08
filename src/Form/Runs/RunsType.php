<?php

namespace App\Form\Runs;

use App\Entity\Categories\Categories;
use App\Entity\Runs\Runs;
use App\Entity\Status\Status;
use App\Entity\Users\Users;
use App\Repository\Categories\CategoriesRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RunsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //TODO UN EVENT DESSUS ONCHANGE QUI CHARGE LES FIELDS LIE A LA CATE (SOUS CATE ?)
                //TODO CALL AJAX POUR RECUP LES FIELDS LIE A LA CATE
                    //TODO FAIRE LAFFICHAGE EN FONCTION DES DONNEES
                        //TODO FAIRE LES VERIFICATIONS BACK VIA UN 2EME CALL SUBMIT
                            //TODO INSERER LES RESULTS DANS FIELDDATA
                                //TODO AFFICHER LES RUNS DANS UN LEADERBOARD
                                    //TODO GESTION DE FILTRE
            ->add('refCategories', EntityType::class, [
                'class' => Categories::class,
                'query_builder' => function (CategoriesRepository $categoriesRepository) use ($options) : QueryBuilder
                {
                    return $categoriesRepository->createQueryBuilder('c')
                        ->join('c.refGames', 'g', 'WITH', 'g.id = c.refGames')
                        ->where('g.id = :id')
                        ->setParameter('id', $options['game']->getId())
                        ;
                },
                'choice_label' => 'name',
                'choice_value' => 'uuid'
            ])
            ->add('game', HiddenType::class, [
                'data' => $options['game']->getUuid(),
                'mapped' => false
            ])
            /*->add('refStatus', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'name',
            ])
            ->add('refUsers', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Runs::class,
            'game' => null
        ]);
    }
}
