<?php

namespace App\Form;

use App\Entity\Utilisateur;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UtilisateurType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
        ->add('prenom', TextType::class, [
            'label' => 'Prénom',
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le prénom est obligatoire',
                ]),
                new Assert\Length([
                    'min' => 2,
                    'max' => 50,
                    'minMessage' => 'Le prénom doit contenir au moins {{ limit }} caractères',
                    'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères',
                ]),
            ],
        ])
        ->add('nom', TextType::class, [
            'label' => 'Nom',
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le nom est obligatoire',
                ]),
                new Assert\Length([
                    'min' => 2,
                    'max' => 50,
                    'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                    'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères',
                ]),
            ],
        ])
        ->add('email', EmailType::class, [
            'label' => 'Adresse email',
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'L\'adresse email est obligatoire',
                ]),
                new Assert\Email([
                    'message' => 'Veuillez entrer une adresse email valide',
                ]),
            ],
        ])
        ->add('villeResidence', EntityType::class, [
            'class' => Ville::class,
            'choice_label' => 'nom',
            'label' => 'Ville de résidence',
            'attr' => ['class' => 'form-select'],
            'placeholder' => 'Choisissez une ville',
            'required' => false,
        ])
        ->add('roles', ChoiceType::class, [
            'label' => 'Rôles',
            'choices' => [
                'Utilisateur' => 'ROLE_USER',
                'Modérateur' => 'ROLE_MODERATOR',
                'Administrateur' => 'ROLE_ADMIN',
            ],
            'multiple' => true,
            'expanded' => true,
            'attr' => ['class' => 'form-check-input'],
            'label_attr' => ['class' => 'form-check-label'],
            'help' => 'Sélectionnez un ou plusieurs rôles pour cet utilisateur',
        ])
        ->add('estValide', CheckboxType::class, [
            'label' => 'Compte validé',
            'required' => false,
            'attr' => ['class' => 'form-check-input'],
            'label_attr' => ['class' => 'form-check-label'],
            'help' => 'Cochez cette case pour valider le compte de l\'utilisateur',
        ])
        ->add('plainPassword', PasswordType::class, [
            'label' => 'Mot de passe',
            'mapped' => false,
            'required' => false,
            'attr' => [
                'class' => 'form-control',
                'autocomplete' => 'new-password',
                'placeholder' => 'Laissez vide pour ne pas modifier',
            ],
            'help' => 'Laissez vide si vous ne souhaitez pas changer le mot de passe',
            'constraints' => [
                new Assert\Length([
                    'min' => 6,
                    'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                    'max' => 4096,
                ]),
            ],
        ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
        'data_class' => Utilisateur::class,
    ]);
  }
}