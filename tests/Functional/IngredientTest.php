<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Entity\Ingredient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IngredientTest extends WebTestCase
{
    //création d'un ingrédient
    public function testIfCreateIngredientIsSuccessful(): void 
    {
        $client = static::createClient(); // On crée le client

        $urlGenerator = $client->getContainer()->get('router'); // On récupère le router

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager'); // On récupère l'entity manager

        $user = $entityManager->find(User::class, 1); // On récupère le 1er utilisateur

        //On se connecte
        $client->loginUser($user);

        //Se rendre sur la page de création d'ingrédient
        $crawler = $client->request('GET', $urlGenerator->generate('ingredient.new'));

        //gérer le formulaire
        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => 'Chocolat',
            'ingredient[price]' => floatval(2.99),
        ]);

        //On soumet le formulaire
        $client->submit($form);

        // On récupère le formulaire

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // On récupère le navigateur
        $client->followRedirect();

        // On vérifie que l'ingrédient a bien été crée
        $this->assertSelectorTextContains('div.alert-success', 'L\'ingrédient a bien été ajoutée avec succès!');

        $this->assertRouteSame('ingredient.index');
    }

    //lister les ingrédients
    public function testIfListingIngredientIsSuccessful(): void 
    {
        $client = static::createClient(); // On crée le client

        $urlGenerator = $client->getContainer()->get('router'); // On récupère le router

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager'); // On récupère l'entity manager

        $user = $entityManager->find(User::class, 1); // On récupère le 1er utilisateur

        //On se connecte
        $client->loginUser($user);

        //Se rendre sur la page de création d'ingrédient
        $client->request('GET', $urlGenerator->generate('ingredient.index'));

        $this->assertResponseIsSuccessful();

        $this->assertRouteSame('ingredient.index');
    }

    //edition d'un ingrédient
    public function testIfEditIngredientIsSuccessful(): void //test si l'ingrédient est bien modifie
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy(
            [
                'user' => $user
            ]
        );

        $client->loginUser($user);

        $crawler = $client->request('GET', $urlGenerator->generate('ingredient.edit', ['id' => $ingredient->getId()]));

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => 'Chocolat a la vanille',
            'ingredient[price]' => floatval(3.99),
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'L\'ingrédient a bien été modifié avec succès!');

        $this->assertRouteSame('ingredient.index');
    }

    //suppression d'un ingrédient
    public function testIfDeleteIngredientIsSuccessful(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $entityManager->find(User::class, 1);
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy(
            [
                'user' => $user
            ]
        );
        $client->loginUser($user);
        $client->request('GET', $urlGenerator->generate('ingredient.delete', ['id' => $ingredient->getId()]));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('div.alert-success', 'L\'ingrédient a bien été supprimé avec succès!');
        $this->assertRouteSame('ingredient.index');
    }
}
