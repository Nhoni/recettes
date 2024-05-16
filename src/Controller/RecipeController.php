<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    // lister les recettes
    #[Route('/recipe', name: 'recipe.index', methods: ['GET'])]
    public function index(
        RecipeRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        $recipes = $paginator->paginate(
            $repository->findBy(['user'=> $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    //creer une recette
    #[Route('/recipe/creation', name: 'recipe.new', methods: ['GET', 'POST'])]
        public function new(
        Request $request,
        EntityManagerInterface $manager
    ): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $recipe->setUser($this->getUser());

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash('success', 'La recette a été créée avec succès!');

            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


        //editer une recette
        #[Route('/recipe/edition/{id}', name: 'recipe.edit')]
        public function edit(
            Recipe $recipe,
            Request $request,
            EntityManagerInterface $manager
        ): Response
        {
            $form = $this->createForm(RecipeType::class, $recipe);
    
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $recipe = $form->getData();
    
                $manager->persist($recipe);
                $manager->flush();
    
                $this->addFlash('success', 'La recette a bien été modifié avec succès!');
    
                return $this->redirectToRoute('recipe.index');
            }
    
            return $this->render('pages/recipe/edit.html.twig', [
                'form' => $form->createView()
            ]);
        }
        
    
        //supprimer recette
        #[Route('/recipe/suppression/{id}', name: 'recipe.delete', methods: ['GET'])]
        public function delete(
            Recipe $recipe,
            EntityManagerInterface $manager
        ): Response {
            $manager->remove($recipe);
            $manager->flush();
            $this->addFlash('success', 'La recette a bien été supprimé avec succès!');
            return $this->redirectToRoute('recipe.index');
        }
        
}
