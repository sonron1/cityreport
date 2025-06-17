<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\Signalement;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageTypeForm extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $currentUser = $options['current_user'] ?? null;

    $builder
        ->add('destinataire', EntityType::class, [
            'class' => Utilisateur::class,
            'choice_label' => function (Utilisateur $utilisateur) {
              $roleLabel = '';
              if (in_array('ROLE_ADMIN', $utilisateur->getRoles())) {
                $roleLabel = ' (Administrateur)';
              } elseif (in_array('ROLE_MODERATOR', $utilisateur->getRoles())) {
                $roleLabel = ' (Modérateur)';
              }
              return $utilisateur->getPrenom() . ' ' . $utilisateur->getNom() . $roleLabel . ' - ' . $utilisateur->getEmail();
            },
            'placeholder' => 'Choisir un destinataire...',
            'label' => 'Destinataire',
            'attr' => [
                'class' => 'form-select',
            ],
            'choice_filter' => function (?Utilisateur $user) use ($currentUser) {
              if (!$user || !$currentUser || $user === $currentUser || !$user->isEstValide()) {
                return false;
              }

              return $this->canMessageUser($currentUser, $user);
            },
            'required' => true,
            'help' => $this->getHelpTextForUser($currentUser),
            'help_attr' => [
                'class' => 'form-text text-muted'
            ],
        ])
        ->add('sujet', TextType::class, [
            'label' => 'Sujet',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Objet du message',
                'maxlength' => 255,
            ],
            'required' => true,
        ])
        ->add('contenu', TextareaType::class, [
            'label' => 'Message',
            'attr' => [
                'class' => 'form-control',
                'rows' => 8,
                'placeholder' => 'Tapez votre message ici...',
            ],
            'required' => true,
        ])
        ->add('signalementConcerne', EntityType::class, [
            'class' => Signalement::class,
            'choice_label' => function (Signalement $signalement) {
              return $signalement->getTitre() . ' - ' . $signalement->getVille()->getNom();
            },
            'placeholder' => 'Aucun signalements lié (optionnel)',
            'label' => 'Signalement concerné',
            'attr' => [
                'class' => 'form-select',
            ],
            'query_builder' => function (EntityRepository $er) use ($currentUser) {
              $qb = $er->createQueryBuilder('s')
                  ->orderBy('s.dateSignalement', 'DESC');

              if ($currentUser) {
                $currentUserRoles = $currentUser->getRoles();

                if (in_array('ROLE_ADMIN', $currentUserRoles) || in_array('ROLE_MODERATOR', $currentUserRoles)) {
                  // Les admins et modérateurs peuvent voir tous les signalements validés
                  $qb->where('s.etatValidation = :valide')
                      ->setParameter('valide', 'validé');
                } else {
                  // Les utilisateurs simples ne voient que leurs propres signalements
                  $qb->where('s.utilisateur = :currentUser')
                      ->setParameter('currentUser', $currentUser);
                }
              }

              return $qb;
            },
            'required' => false,
            'help' => 'Sélectionnez un signalements si votre message s\'y rapporte',
        ]);
  }

  private function canMessageUser(Utilisateur $expediteur, Utilisateur $destinataire): bool
  {
    $expediteurRoles = $expediteur->getRoles();
    $destinataireRoles = $destinataire->getRoles();

    // Les admins peuvent écrire aux modérateurs et autres admins
    if (in_array('ROLE_ADMIN', $expediteurRoles)) {
      return in_array('ROLE_MODERATOR', $destinataireRoles) ||
          in_array('ROLE_ADMIN', $destinataireRoles);
    }

    // Les modérateurs peuvent écrire aux utilisateurs simples et aux admins
    if (in_array('ROLE_MODERATOR', $expediteurRoles)) {
      return !in_array('ROLE_MODERATOR', $destinataireRoles) ||
          in_array('ROLE_ADMIN', $destinataireRoles);
    }

    // Les utilisateurs simples ne peuvent écrire qu'aux modérateurs
    return in_array('ROLE_MODERATOR', $destinataireRoles);
  }

  private function getHelpTextForUser(?Utilisateur $user): string
  {
    if (!$user) {
      return 'Sélectionnez un destinataire pour votre message';
    }

    $roles = $user->getRoles();

    if (in_array('ROLE_ADMIN', $roles)) {
      return 'En tant qu\'administrateur, vous pouvez contacter les modérateurs et autres administrateurs.';
    } elseif (in_array('ROLE_MODERATOR', $roles)) {
      return 'En tant que modérateur, vous pouvez contacter les utilisateurs et les administrateurs.';
    } else {
      return 'En tant qu\'utilisateur, vous pouvez uniquement contacter les modérateurs.';
    }
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
        'data_class' => Message::class,
        'current_user' => null,
    ]);

    $resolver->setAllowedTypes('current_user', ['null', Utilisateur::class]);
  }
}