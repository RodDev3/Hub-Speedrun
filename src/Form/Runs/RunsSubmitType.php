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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RunsSubmitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /* @var $category Categories */
        $category = $options['category'];

        $builder
            ->add('refCategories', EntityType::class, [
                'class' => Categories::class,
                'query_builder' => function (CategoriesRepository $categoriesRepository) use ($category, $options): QueryBuilder {
                    return $categoriesRepository->createQueryBuilder('c')
                        ->join('c.refGames', 'g', 'WITH', 'g.id = c.refGames')
                        ->where('g.id = :id')
                        ->setParameter('id', $category->getRefGames()->getId());
                },
                'choice_label' => 'name',
                'choice_value' => 'uuid'
            ]);

        /*if ($category !== null) {
            $fields = $category->getRefFields();

            foreach ($fields as $field) {
                switch ($field->getRefFieldTypes()->getBackName()) {
                    case 'time-goal':
                        $builder
                            ->add('hours', IntegerType::class, [
                                'mapped' => false,
                                'required' => true
                            ])
                            ->add('minutes', IntegerType::class, [
                                'mapped' => false
                            ])
                            ->add('secondes', IntegerType::class, [
                                'mapped' => false
                            ])
                            ->add('milliseconds', IntegerType::class, [
                                'mapped' => false
                            ]);
                        break;
                }
            }
        }*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Runs::class,
            'category' => null,
            'allow_extra_fields'=> true,
        ]);
    }
}
