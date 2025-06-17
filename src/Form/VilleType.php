<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
        ->add('nom', TextType::class, [
            'label' => 'Nom de la ville',
            'attr' => ['class' => 'form-control']
        ])
        ->add('departement', EntityType::class, [
            'class' => Departement::class,
            'choice_label' => 'nom',
            'label' => 'Département',
            'placeholder' => '-- Sélectionner un département --',
            'attr' => ['class' => 'form-control']
        ])
        ->add('latitudeCentre', NumberType::class, [
            'label' => 'Latitude',
            'scale' => 6,
            'attr' => [
                'class' => 'form-control',
                'step' => '0.000001'
            ]
        ])
        ->add('longitudeCentre', NumberType::class, [
            'label' => 'Longitude',
            'scale' => 6,
            'attr' => [
                'class' => 'form-control',
                'step' => '0.000001'
            ]
        ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
        'data_class' => Ville::class,
    ]);
  }
}