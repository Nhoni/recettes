<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName',TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '50',
                ],
                'label' => 'Nom / Prénom *',
                'label_attr' => [
                    'class'=> 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 50]),
                ],
            ])
            ->add('pseudo',TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '50',
                ],
                'required' => false,
                'label' => 'Pseudo (facultatif)',
                'label_attr' => [
                    'class'=> 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                ],
            ])
            ->add('email',EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '180',
                ],
                'label' => ' Adresse Email *',
                'label_attr' => [
                    'class'=> 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                    new Assert\Length(['min' => 2, 'max' => 180]),
                ],
                'invalid_message' => 'L\'adresse email est invalide.',
            ])
            ->add('plainPassword',RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de passe *',
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'label_attr' => [
                        'class'=> 'form-label mt-4'
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer votre mot de passe *',
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'label_attr' => [
                        'class'=> 'form-label mt-4'
                    ],
                ],
                'invalid_message' => 'Les mots de passe ne sont pas identiques.',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-secondary mt-4'
                ],
                'label' => 'S\'inscrire',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
