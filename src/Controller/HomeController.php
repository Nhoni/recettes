<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home.index', methods: ['GET'])]
    public function index( 
        RecipeRepository $repository,
        PaginatorInterface $paginator,
        Request $request
        ): Response
    {
        $query = $request->query->get('query');
        $recipesQuery = $query ? $repository->findByQuery($query) : $repository->findBy([], ['id' => 'DESC']);

        $recipes = $paginator->paginate(
            $recipesQuery,
            $request->query->getInt('page', 1),
            12
        );
        
        return $this->render('pages/home.html.twig', [
            'recipes' => $recipes,
            'query' => $query,
        ]);
    }

    #[Route('/search/results', name: 'search_results', methods: ['GET'])]
    public function searchResults(
        RecipeRepository $repository,
        PaginatorInterface $paginator,
        Request $request
        ): Response
    {
        $query = $request->query->get('query');
        $recipesQuery = $repository->findByQuery($query);

        $recipes = $paginator->paginate(
            $recipesQuery,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('pages/search/search_results.html.twig', [
            'recipes' => $recipes,
            'query' => $query,
        ]);
    }
}
