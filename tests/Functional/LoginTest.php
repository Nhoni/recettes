<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    public function testIsLoginIsSuccessful(): void // On test si le login est réussi
    {
        $client = static::createClient(); // On crée le client

        $urlGenerator = $client->getContainer()->get('router'); // On récupère le router

        $crawler = $client->request('GET', $urlGenerator->generate('security.login')); // On récupère la page de login

        $form = $crawler->filter('form[name=login]')->form([ // On récupère le formulaire de login
            '_username' => 'cookshare@admin.fr', // On récupère l'email
            '_password' => 'password', // On récupère le mot de passe
        ]);

        $client->submit($form); // On soumet le formulaire

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // On vérifie que la page redirige

        $client->followRedirect(); // On redirige

        $this->assertResponseIsSuccessful('home.index'); // On redirige sur la page d'accueil
    }


    public function testIsLoginFailWhenPasswordIsWrong(): void  // On test si le mot de passe est incorrect
    {
        $client = static::createClient(); // On crée le client

        $urlGenerator = $client->getContainer()->get('router'); // On récupère le router

        $crawler = $client->request('GET', $urlGenerator->generate('security.login')); // On récupère la page de login

        $form = $crawler->filter('form[name=login]')->form([ // On récupère le formulaire de login
            '_username' => 'cookshare@admin.fr', // On récupère l'email
            '_password' => 'wrongpassword', // On récupère le mot de passe
        ]);

        $client->submit($form); // On soumet le formulaire

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // On vérifie que la page redirige

        $client->followRedirect(); // On redirige

        $this->assertResponseIsSuccessful('security.login'); // On redirige sur la page de login

        $this->assertSelectorTextContains('div.alert-danger', 'Invalid credentials.'); // On vérifie qu'il y a un message d'erreur
    }
}
