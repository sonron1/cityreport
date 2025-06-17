<?php

namespace App\Form;

use Doctrine\ORM\EntityRepository;

use App\Entity\Arrondissement;
use App\Entity\Categorie;
use App\Entity\Signalement;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\EntityManagerInterface;

class SignalementTypeForm extends AbstractType
{
    private VilleRepository $villeRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(VilleRepository $villeRepository, EntityManagerInterface $entityManager)
    {
        $this->villeRepository = $villeRepository;
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'label_attr' => [
                    'class' => 'form-label fw-semibold'
                ],
                'attr' => [
                    'placeholder' => 'Donnez un titre à votre signalements',
                    'class' => 'form-control form-control-lg',
                    'autocomplete' => 'off'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un titre'
                    ])
                ],
                'help' => 'Choisissez un titre court et descriptif',
                'help_attr' => [
                    'class' => 'form-text text-muted'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'form-label fw-semibold'
                ],
                'attr' => [
                    'placeholder' => 'Décrivez le problème signalé en détail',
                    'class' => 'form-control',
                    'rows' => 5
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une description'
                    ])
                ],
                'help' => 'Décrivez précisément le problème pour faciliter son traitement',
                'help_attr' => [
                    'class' => 'form-text text-muted'
                ]
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo du problème',
                'label_attr' => [
                    'class' => 'form-label fw-semibold'
                ],
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG ou PNG)',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/jpeg,image/png'
                ],
                'help' => 'Formats acceptés: JPG, PNG. Taille maximale: 5Mo',
                'help_attr' => [
                    'class' => 'form-text text-muted'
                ]
            ])
            ->add('latitude', HiddenType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un emplacement sur la carte'
                    ])
                ],
                'attr' => [
                    'class' => 'coordinate-field'
                ]
            ])
            ->add('longitude', HiddenType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un emplacement sur la carte'
                    ])
                ],
                'attr' => [
                    'class' => 'coordinate-field'
                ]
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez une catégorie',
                'label' => 'Catégorie',
                'label_attr' => [
                    'class' => 'form-label fw-semibold'
                ],
                'attr' => [
                    'class' => 'form-select'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir une catégorie'
                    ])
                ],
                'help' => 'Sélectionnez la catégorie qui correspond le mieux au problème',
                'help_attr' => [
                    'class' => 'form-text text-muted'
                ]
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez une ville',
                'label' => 'Ville',
                'label_attr' => [
                    'class' => 'form-label fw-semibold'
                ],
                'choices' => $this->villeRepository->findAllOrdered(), // ✅ Utiliser une méthode existante
                'required' => true,
                'attr' => [
                    'class' => 'form-select ville-select',
                    'data-arrondissement-url' => $options['arrondissement_url'] ?? '#'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir une ville',
                        'groups' => ['Default']
                    ])
                ],
                'help' => 'Sélectionnez une ville du Bénin',
                'help_attr' => [
                    'class' => 'form-text text-muted'
                ]
            ])
            ->add('arrondissement', EntityType::class, [
            'class' => Arrondissement::class,
            'choice_label' => 'nom',
            'placeholder' => 'Choisissez un arrondissement (optionnel)',
            'required' => false,
            'choices' => [],
            'label' => 'Arrondissement',
            'label_attr' => [
                'class' => 'form-label fw-semibold'
            ],
            'attr' => [
                'class' => 'form-select arrondissement-select'
            ],
            'help' => 'Optionnel: précisez l\'arrondissement pour une localisation plus précise',
            'help_attr' => [
                'class' => 'form-text text-muted'
            ]
        ]);

// Dans SignalementTypeForm.php, ajoutez un événement PRE_SUBMIT pour mettre à jour les arrondissements
$builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
    $data = $event->getData();
    $form = $event->getForm();

    // Si une ville est sélectionnée, mettre à jour les arrondissements
    if (!empty($data['ville'])) {
        $villeId = $data['ville'];
        $ville = $this->entityManager->getRepository(Ville::class)->find($villeId);

        if ($ville) {
            $form->add('arrondissement', EntityType::class, [
                'class' => Arrondissement::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez un arrondissement (optionnel)',
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($ville) {
                    return $er->createQueryBuilder('a')
                        ->where('a.ville = :ville')
                        ->setParameter('ville', $ville)
                        ->orderBy('a.nom', 'ASC');
                },
                'label' => 'Arrondissement',
                'label_attr' => [
                    'class' => 'form-label fw-semibold'
                ],
                'attr' => [
                    'class' => 'form-select arrondissement-select'
                ],
                'help' => 'Optionnel: précisez l\'arrondissement pour une localisation plus précise',
                'help_attr' => [
                    'class' => 'form-text text-muted'
                ]
            ]);
        }
    }
});
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();

                // Si un arrondissement est sélectionné, mais pas de ville
                if (!empty($data['arrondissement']) && empty($data['ville'])) {
                    // Récupérer l'arrondissement et sa ville associée
                    $arrondissementId = $data['arrondissement'];
                    $arrondissement = $this->entityManager
                        ->getRepository(Arrondissement::class)
                        ->find($arrondissementId);

                    if ($arrondissement) {
                        // Définir la ville associée à l'arrondissement
                        $data['ville'] = $arrondissement->getVille()->getId();
                        $event->setData($data);
                    }
                }
            }
        );
// Dans SignalementTypeForm.php, ajoutez un événement PRE_SET_DATA pour configurer les choix d'arrondissement
$builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
    $signalement = $event->getData();
    $form = $event->getForm();

    // Si un signalements avec une ville existe, filtrer les arrondissements
    if ($signalement && $signalement->getVille()) {
        $ville = $signalement->getVille();
        $form->add('arrondissement', EntityType::class, [
            'class' => Arrondissement::class,
            'choice_label' => 'nom',
            'placeholder' => 'Choisissez un arrondissement (optionnel)',
            'required' => false,
            'query_builder' => function (EntityRepository $er) use ($ville) {
                return $er->createQueryBuilder('a')
                    ->where('a.ville = :ville')
                    ->setParameter('ville', $ville)
                    ->orderBy('a.nom', 'ASC');
            },
            'label' => 'Arrondissement',
            'label_attr' => [
                'class' => 'form-label fw-semibold'
            ],
            'attr' => [
                'class' => 'form-select arrondissement-select'
            ],
            'help' => 'Optionnel: précisez l\'arrondissement pour une localisation plus précise',
            'help_attr' => [
                'class' => 'form-text text-muted'
            ]
        ]);
    }
});
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Signalement::class,
            'attr' => [
                'class' => 'needs-validation',
                'novalidate' => 'novalidate'
            ],
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'signalement_form',
            'arrondissement_url' => null
        ]);

        $resolver->setAllowedTypes('arrondissement_url', ['string', 'null']);
    }
}