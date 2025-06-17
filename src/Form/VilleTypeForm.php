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

class VilleTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la ville',
                'attr' => ['placeholder' => 'Ex: Cotonou']
            ])
            ->add('departement', EntityType::class, [
                'class' => Departement::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez un département',
                'required' => true,
                'label' => 'Département'
            ])
            ->add('latitudeCentre', NumberType::class, [
                'label' => 'Latitude du centre',
                'scale' => 7,
                'attr' => ['placeholder' => 'Ex: 6.3676953']
            ])
            ->add('longitudeCentre', NumberType::class, [
                'label' => 'Longitude du centre',
                'scale' => 7,
                'attr' => ['placeholder' => 'Ex: 2.3912362']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}