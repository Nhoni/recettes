<?php

namespace App\Tests\Unit;

use App\Entity\Mark;
use App\Entity\User;
use App\Entity\Recipe;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeTest extends KernelTestCase
{
    /**Cette méthode est appelée avant chaque test */
    public function getEntity(): Recipe // Recipe est une entité
    {
        return (new Recipe()) // On crée une nouvelle entité
            ->setName('Recipe #1') // On lui affecte une valeur ...
            ->setDescription('Description #1') //
            ->setIsFavorite(true) //
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());
    }


    public function testEntityIsValid(): void // On test si l'entité est valide
    {
        self::bootKernel(); // On boot le kernel
        $container = static::getContainer(); // On récupère le container

        $recipe = $this->getEntity(); // On récupère l'entité

        $errors = $container->get('validator')->validate($recipe); // On valide l'entité

        $this->assertCount(0, $errors); // On vérifie qu'il n'y a pas d'erreurs
    }



    public function testInvalidName(): void // On test si l'entité est invalide
    {
        self::bootKernel(); // On boot le kernel
        $container = static::getContainer(); // On récupère le container

        $recipe = $this->getEntity(); // On récupère l'entité
        $recipe->setName(''); // On lui affecte une valeur

        $errors = $container->get('validator')->validate($recipe); // On valide l'entité
        $this->assertCount(2, $errors); // On vérifie qu'il y a 2 erreurs
    }


    public function testGetAverage() // On test si l'entité est invalide
    {
        $recipe = $this->getEntity(); // On récupère l'entité
        $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class , 1); // On récupère l'utilisateur

        for ($i = 0; $i < 5; $i++) { // On ajoute 5 marks
            $mark = new Mark(); // On crée une nouvelle entité
            $mark->setMark(2); // On lui affecte une valeur
            $mark->setUser($user);  // On lui affecte une valeur
            
            $recipe->addMark($mark);    // On lui affecte une valeur
        }

        $this->assertTrue(2.0 === $recipe->getAverage());
    }
}
