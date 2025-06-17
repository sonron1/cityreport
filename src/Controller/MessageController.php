<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Utilisateur;
use App\Form\MessageTypeForm;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/messages')]
class MessageController extends AbstractController
{
  #[Route('/', name: 'app_messages_inbox')]
  #[IsGranted('IS_AUTHENTICATED_FULLY')]
  public function inbox(MessageRepository $messageRepository): Response
  {
    $user = $this->getUser();

    $messagesRecus = $messageRepository->findReceivedMessagesForUser($user);
    $nombreNonLus = $messageRepository->countUnreadMessagesForUser($user);

    return $this->render('message/inbox.html.twig', [
        'messages' => $messagesRecus,
        'nombreNonLus' => $nombreNonLus,
    ]);
  }

  #[Route('/envoyes', name: 'app_messages_sent')]
  #[IsGranted('IS_AUTHENTICATED_FULLY')]
  public function sent(MessageRepository $messageRepository): Response
  {
    $user = $this->getUser();

    $messagesEnvoyes = $messageRepository->findSentMessagesByUser($user);

    return $this->render('message/sent.html.twig', [
        'messages' => $messagesEnvoyes,
    ]);
  }

  #[Route('/nouveau', name: 'app_message_new')]
  #[IsGranted('IS_AUTHENTICATED_FULLY')]
  public function new(Request $request, EntityManagerInterface $entityManager): Response
  {
    $user = $this->getUser();

    // Vérifier que l'utilisateur est validé
    if (!$user->isEstValide()) {
      $this->addFlash('error', 'Votre compte doit être validé pour accéder à la messagerie.');
      return $this->redirectToRoute('app_home');
    }

    $message = new Message();
    $message->setExpediteur($user);

    // Gérer les réponses (si reply est dans l'URL)
    $replyId = $request->query->get('reply');
    if ($replyId) {
      $originalMessage = $entityManager->getRepository(Message::class)->find($replyId);
      if ($originalMessage &&
          ($originalMessage->getExpediteur() === $user ||
              $originalMessage->getDestinataire() === $user)) {

        // Vérifier si la réponse est autorisée selon les règles de hiérarchie
        if ($this->canMessageUser($user, $originalMessage->getExpediteur())) {
          $message->setDestinataire($originalMessage->getExpediteur());

          // Préfixer le sujet avec "Re: "
          $sujet = $originalMessage->getSujet();
          if (!str_starts_with($sujet, 'Re: ')) {
            $message->setSujet('Re: ' . $sujet);
          } else {
            $message->setSujet($sujet);
          }

          // Associer le même signalements si il existe
          if ($originalMessage->getSignalementConcerne()) {
            $message->setSignalementConcerne($originalMessage->getSignalementConcerne());
          }
        } else {
          $this->addFlash('error', 'Vous n\'êtes pas autorisé à répondre à ce message.');
          return $this->redirectToRoute('app_messages_inbox');
        }
      }
    }

    // Créer le formulaire en passant l'utilisateur actuel
    $form = $this->createForm(MessageTypeForm::class, $message, [
        'current_user' => $user
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Vérification supplémentaire côté serveur
      if (!$this->canMessageUser($user, $message->getDestinataire())) {
        $this->addFlash('error', 'Vous n\'êtes pas autorisé à envoyer un message à cet utilisateur.');
        return $this->render('message/new.html.twig', [
            'form' => $form->createView(),
            'isReply' => $replyId !== null,
        ]);
      }

      $entityManager->persist($message);
      $entityManager->flush();

      $this->addFlash('success', 'Message envoyé avec succès.');
      return $this->redirectToRoute('app_messages_sent');
    }

    return $this->render('message/new.html.twig', [
        'form' => $form->createView(),
        'isReply' => $replyId !== null,
    ]);
  }

  #[Route('/{id}', name: 'app_message_show', requirements: ['id' => '\d+'])]
  #[IsGranted('IS_AUTHENTICATED_FULLY')]
  public function show(Message $message, EntityManagerInterface $entityManager): Response
  {
    $user = $this->getUser();

    // Vérifier que l'utilisateur est soit l'expéditeur soit le destinataire
    if ($message->getExpediteur() !== $user && $message->getDestinataire() !== $user) {
      throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce message.');
    }

    // Marquer comme lu si c'est le destinataire
    if ($message->getDestinataire() === $user && !$message->isEstLu()) {
      $message->setEstLu(true);
      $entityManager->flush();
    }

    // Vérifier si l'utilisateur peut répondre selon les règles de hiérarchie
    $canReply = false;
    if ($message->getDestinataire() === $user) {
      $canReply = $this->canMessageUser($user, $message->getExpediteur());
    }

    return $this->render('message/show.html.twig', [
        'message' => $message,
        'canReply' => $canReply,
    ]);
  }

  #[Route('/{id}/archiver', name: 'app_message_archive', methods: ['POST'], requirements: ['id' => '\d+'])]
  #[IsGranted('IS_AUTHENTICATED_FULLY')]
  public function archive(Message $message, EntityManagerInterface $entityManager): Response
  {
    if ($message->getDestinataire() !== $this->getUser()) {
      throw $this->createAccessDeniedException('Vous ne pouvez archiver que vos propres messages.');
    }

    $message->setEstArchive(true);
    $entityManager->flush();

    $this->addFlash('success', 'Message archivé.');
    return $this->redirectToRoute('app_messages_inbox');
  }

  #[Route('/{id}/supprimer', name: 'app_message_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
  #[IsGranted('IS_AUTHENTICATED_FULLY')]
  public function delete(Message $message, EntityManagerInterface $entityManager): Response
  {
    $user = $this->getUser();

    // Vérifier que l'utilisateur est soit l'expéditeur soit le destinataire
    if ($message->getExpediteur() !== $user && $message->getDestinataire() !== $user) {
      throw $this->createAccessDeniedException('Vous ne pouvez supprimer que vos propres messages.');
    }

    $entityManager->remove($message);
    $entityManager->flush();

    $this->addFlash('success', 'Message supprimé.');

    // Rediriger selon le rôle de l'utilisateur sur ce message
    if ($message->getExpediteur() === $user) {
      return $this->redirectToRoute('app_messages_sent');
    } else {
      return $this->redirectToRoute('app_messages_inbox');
    }
  }

  /**
   * Vérifie si un utilisateur peut envoyer un message à un autre selon la hiérarchie
   */
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
}