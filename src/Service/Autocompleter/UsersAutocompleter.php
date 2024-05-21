<?php

namespace App\Service\Autocompleter;

use App\Entity\Users\Users;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\UX\Autocomplete\EntityAutocompleterInterface;

#[AutoconfigureTag('ux.entity_autocompleter', ['alias' => 'users'])]
class UsersAutocompleter implements EntityAutocompleterInterface
{
    public function getEntityClass(): string
    {
        return Users::class;
    }

    public function createFilteredQueryBuilder(EntityRepository $repository, string $query): QueryBuilder
    {
        return $repository
            ->createQueryBuilder('users')
            ->andWhere('users.username LIKE :search')
            ->setParameter('search', '%'.$query.'%')
            ;
    }

    public function getLabel(object $entity): string
    {
        return $entity->getUsername();
    }

    public function getValue(object $entity): string
    {
        return $entity->getUsername();
    }

    public function isGranted(Security $security): bool
    {
        // see the "security" option for details
        return true;
    }
}