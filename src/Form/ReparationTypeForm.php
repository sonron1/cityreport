<?php

namespace App\Form;

use App\Entity\Reparation;
use App\Entity\Utilisateur;
use App\Enum\StatutReparation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReparationTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Description de la réparation',
                'required' => true,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Décrivez les travaux à effectuer...',
                    'minlength' => 10,
                    'maxlength' => 1000
                ]
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date et heure de début',
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d\TH:i'),
                    'class' => 'form-control date-debut',
                    'onchange' => 'updateDateFinMin()'
                ]
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'Date et heure de fin',
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'form-control date-fin'
                ]
            ])
            ->add('statut', EnumType::class, [
                'class' => StatutReparation::class,
                'label' => 'Statut',
                'required' => true,
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaires',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Commentaires sur la réparation (optionnel)...'
                ]
            ])
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => function(Utilisateur $utilisateur) {
                    return $utilisateur->getPrenom() . ' ' . $utilisateur->getNom();
                },
                'label' => 'Technicien assigné',
                'required' => false,
                'placeholder' => 'Sélectionner un technicien (optionnel)',
                'attr' => [
                    'class' => 'form-select'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reparation::class,
        ]);
    }
}