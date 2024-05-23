<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home.index', methods: ['GET'])]
    public function index( 
        RecipeRepository $repository,
        \Knp\Component\Pager\PaginatorInterface $paginator,
        \Symfony\Component\HttpFoundation\Request $request
        ): Response
    {
        $recipes = $paginator->paginate(
            $repository->findBy([], ['id' => 'DESC']),
            $request->query->getInt('page', 1),
            12
        );
        
        return $this->render('pages/home.html.twig', [
            'recipes' => $recipes

        ]);
    }
}
