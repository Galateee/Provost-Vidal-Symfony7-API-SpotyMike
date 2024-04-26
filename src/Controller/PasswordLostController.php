<?php

namespace App\Controller;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ExceptionManager;

class PasswordLostController extends AbstractController
{
     private $exceptionManager;
     private $repository;
     private $entityManager;

     public function __construct(ExceptionManager $exceptionManager, EntityManagerInterface $entityManager)
     {
          $this->exceptionManager = $exceptionManager;
          $this->entityManager = $entityManager;
          $this->repository = $entityManager->getRepository(User::class);
     }

     #[Route('/password-lost', name: 'password_lost', methods: 'POST')]
     public function password_lost(Request $request): JsonResponse
     {
          $data = $request->request->all();
          $user = $this->repository->findOneBy(['email' => $data['email']]);

          // Email manquant
          if (empty($data['email'])) {
               return $this->exceptionManager->EmailMissingPassLost();
          }

          // Format d'email invalide 
          if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
               return $this->exceptionManager->invalidEmailPassLost();
          }

          // Email non trouvé
          if ($user === null) {
               return $this->exceptionManager->emailNotFoundPassLost();
          }

          // Trop de tentatives :
          // Vérification du nombre de tentatives et du délai entre les tentatives infructueuses
          if ($user->getNbTry() >= 3) {
               $lastTryTimestamp = $user->getLastTryTimestamp();
               $fiveMinutesAgo = (new \DateTimeImmutable())->sub(new \DateInterval('PT1M'));
               if ($lastTryTimestamp >= $fiveMinutesAgo) {
                    // L'utilisateur doit attendre
                    return $this->exceptionManager->lotTryPassLost();
               } else {
                    // Réinitialisation du compteur de tentatives
                    $user->setNbTry(0);
                    $user->setLastTryTimestamp(new \DateTimeImmutable());
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
               }
          }

          // Vérification du mot de passe
          if (!password_verify($data['password'], $user->getPassword())) {
               // Augmentation du nombre de tentatives
               $user->setNbTry($user->getNbTry() + 1);
               $this->entityManager->persist($user);
               $this->entityManager->flush();

               return $this->exceptionManager->invalidCredentialsLogin();
          } else {
               // Réinitialisation du compteur de tentatives
               $user->setNbTry(0);
               $user->setLastTryTimestamp(new \DateTimeImmutable());
               $this->entityManager->persist($user);
               $this->entityManager->flush();
          }

          return new JsonResponse(['success' => true, 'message' => 'Un mail de réinitialisation de mot de pass a été envoyé à votre adresse email. Veuillez suivre les instructions contenues dans l\'email pour éinitialiser votre mot de passe'], 200);
     }
}
