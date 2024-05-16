<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findPublicRecipe(?int $nbRecipes): array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.isPublic = 1')
            ->orderBy('r.id', 'DESC');

        if ($nbRecipes === 0 || !$nbRecipes === null) {
            $qb->setMaxResults($nbRecipes);
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

}
