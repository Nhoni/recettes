<?php

namespace App\Controller\Admin;

use App\Entity\Mark;
use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Contact;
use App\Entity\Category;
use App\Entity\Ingredient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('CookShare');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retour au site', 'fas fa-home', 'home.index');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Recettes', 'fas fa-list', Recipe::class);
        yield MenuItem::linkToCrud('Ingrédients', 'fas fa-cake', Ingredient::class);
        yield MenuItem::linkToCrud('Notes', 'fas fa-star', Mark::class);
        yield MenuItem::linkToCrud('Contact', 'fas fa-envelope', Contact::class);
        

    }
}
