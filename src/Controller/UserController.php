<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class UserController extends AbstractController
{
    #[Route('/utilisateur/edition/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordhasher): Response
    {
        //Vérifie si l'utilisateur est connecté et si l'utilisateur est le propriétaire de l'utilisateur
        if (!$this->getUser() instanceof UserInterface || !$this->getUser()->getRoles() || !in_array('ROLE_USER', $this->getUser()->getRoles(), true) || $this->getUser() !== $user) {
            throw new AccessDeniedException('Vous ne pouvez pas accéder à cette page');
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifie si le mot de passe est valide
            if ($passwordhasher->isPasswordValid($user, $form->get('plainPassword')->getData())) {
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Les informations ont bien été mises à jour.'
                );

                return $this->redirectToRoute('recipe.index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe est incorrect.'
                );
            }
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/utilisateur/edition-mot-de-passe/{id}', name: 'user.edit.password', methods: ['GET', 'POST'])]
    public function editPassword(
        User $user,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $manager,
        FormFactoryInterface $formFactory,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        // Verifie si l'utilisateur est connecté et si l'utilisateur est le propriétaire de l'utilisateur
        if (!$authorizationChecker->isGranted('ROLE_USER') || $this->getUser() !== $user) {
            throw new AccessDeniedException('Vous ne pouvez pas accéder à cette page');
        }

        $form = $formFactory->create(UserPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($passwordHasher->isPasswordValid($user, $form->getData()['plainPassword'])) {
                $user->setUpdatedAt(new \DateTimeImmutable());
                $user->setPlainPassword($form->getData()['newPassword']);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Le mot de passe a bien été mis à jour.'
                );

                return $this->redirectToRoute('recipe.index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe est incorrect.'
                );
            }
        }

        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
