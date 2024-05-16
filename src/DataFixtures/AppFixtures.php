<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    
    public function load(ObjectManager $manager): void
    {
        //ingredients
        $ingredients = [];
        for ($i = 0; $i < 50; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word())
                ->setPrice(mt_rand(0, 100));

            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        //recipes
        for ($r = 0; $r < 50; $r++) {
            $recipe = new Recipe();
            $recipe->setName($this->faker->word())
                ->setTime(mt_rand(0,1) == 1 ? mt_rand(0, 1440) : null)
                ->setNbPeople(mt_rand(0,1) == 1 ? mt_rand(0, 100) : null)
                ->setDifficulty(mt_rand(0,1) == 1 ? mt_rand(0, 5) : null)
                ->setDescription($this->faker->text(300))
                ->setPrice(mt_rand(0,1) == 1 ? mt_rand(0, 1000) : null)
                ->setIsFavorite(mt_rand(0,1) == 1 ? true : false);

            for ($i = 0; $i < mt_rand(5, 15); $i++) {
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }
            $manager->persist($recipe);
        }

        $manager->flush();
    }
}