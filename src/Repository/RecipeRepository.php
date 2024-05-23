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
    {// la fonction permet de récupérer les recettes publiées
        $qb = $this->createQueryBuilder('r') // Requête Builder pour récupérer les recettes publiées
            ->where('r.isPublic = 1') //On récupère uniquement les recettes publiques
            ->orderBy('r.id', 'ASC');// On trie les recettes par id croissant

        if ($nbRecipes === 0 || !$nbRecipes === null) { // Si le nombre de recettes vaut 0 ou null
            $qb->setMaxResults($nbRecipes);// On récupère le nombre de recettes voulues
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

}
