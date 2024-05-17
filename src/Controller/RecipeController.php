<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RecipeController extends AbstractController
{
    // lister les recettes
    #[Route('/recipe', name: 'recipe.index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        RecipeRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $recipes = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }


    //creer une recette
    #[Route('/recipe/creation', name: 'recipe.new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(
        Request $request,
        EntityManagerInterface $manager
    ): Response {
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
    #[IsGranted('ROLE_USER')]
    public function edit(
        Recipe $recipe,
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        // Vérifie si l'utilisateur est le propriétaire de la recette
        if ($recipe->getUser() !== $this->getUser()) {
            throw new AccessDeniedException('Vous ne pouvez pas modifier cet recette car vous n\'en êtes pas le propriétaire.');
        }
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

        //verifie si c'est la propriété de la recette
        if ($recipe->getUser() !== $this->getUser()) {
            throw new AccessDeniedException('Vous ne pouvez pas voir cette recette car vous n\'en êtes pas le propriétaire de cette recette.');
        }
        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash('success', 'La recette a bien été supprimé avec succès!');
        return $this->redirectToRoute('recipe.index');
    }

    //recette publique
    #[Route('/recipe/public', name: 'recipe.index.public', methods: ['GET'])]
    public function indexpublic(PaginatorInterface $paginator, Request $request, RecipeRepository $repository): Response
    {
        $recipes = $paginator->paginate(
            $repository->findPublicRecipes(null),
            $request->query->getInt('page', 1),
            50
        );

        return $this->render('pages/recipe/index_public.html.twig', [
            'recipes' => $recipes,
        ]);
    }


    //afficher une recette
    #[Route('/recette/{id}', name: 'recipe.show', methods: ['GET', 'POST'])]
    public function show(
        Recipe $recipe,
        AuthorizationCheckerInterface $authorizationChecker,
        Request $request,
        MarkRepository $markRepository,
        EntityManagerInterface $manager
    ): Response {

        $mark = new Mark();
        $form = $this->createForm(MarkType::class, $mark);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mark->setUser($this->getUser());
            $mark->setRecipe($recipe);

            $existingMark = $markRepository->findOneBy([
                'user' => $this->getUser(),
                'recipe' => $recipe
            ]);

            if (!$existingMark) {
                $manager->persist($mark);
            } else {
                $existingMark->setMark(
                    $form->getData()->getMark()
                );
            }
            $manager->flush();
            $this->addFlash('success', 'La note a bien été pris en compte!');

            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId()]);
        }


        // Vérifier si la recette est publique
        if ($recipe->getIsPublic()) {
            // La recette est publique, donc accessible à tous (utilisateurs connectés et visiteurs)

            // Afficher la recette
            return $this->render('pages/recipe/show.html.twig', [
                'recipe' => $recipe,
                'form' => $form->createView()
            ]);
        } else {
            // La recette n'est pas publique, vérifier si l'utilisateur est connecté et s'il est le propriétaire
            if ($authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED') && $recipe->getUser() === $this->getUser()) {
                // L'utilisateur est connecté et est le propriétaire de la recette, donc peut accéder à la recette même si elle n'est pas publique

                // Afficher la recette avec le formulaire de modification
                return $this->render('pages/recipe/show.html.twig', [
                    'recipe' => $recipe,
                    'form' => $form->createView()
                ]);
            } else {
                // L'utilisateur n'est pas connecté ou n'est pas le propriétaire de la recette
                // Dans ce cas, l'accès à la recette non publique est refusé
                throw new AccessDeniedException('Accès refusé. Cette recette n\'est pas publique.');
            }
        }
    }
}
