<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CategorieType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
        ->add('nom', TextType::class, [
            'label' => 'Nom de la catégorie',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Ex: Éclairage public, Voirie...'
            ],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le nom de la catégorie est obligatoire'
                ]),
                new Assert\Length([
                    'min' => 2,
                    'max' => 100,
                    'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                    'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères'
                ])
            ]
        ])
        ->add('description', TextareaType::class, [
            'label' => 'Description',
            'required' => false,
            'attr' => [
                'class' => 'form-control',
                'rows' => 4,
                'placeholder' => 'Description de la catégorie (optionnel)'
            ],
            'constraints' => [
                new Assert\Length([
                    'max' => 500,
                    'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères'
                ])
            ]
        ])
        ->add('couleur', ColorType::class, [
            'label' => 'Couleur d\'affichage',
            'required' => false,
            'attr' => [
                'class' => 'form-control form-control-color',
                'title' => 'Choisir une couleur pour cette catégorie'
            ],
            'help' => 'Cette couleur sera utilisée pour afficher la catégorie sur la carte et dans les listes'
        ])
        ->add('submit', SubmitType::class, [
            'label' => $options['is_edit'] ? 'Modifier la catégorie' : 'Créer la catégorie',
            'attr' => [
                'class' => 'btn btn-primary'
            ]
        ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
        'data_class' => Categorie::class,
        'is_edit' => false, // Pour différencier création/modification
    ]);
  }
}