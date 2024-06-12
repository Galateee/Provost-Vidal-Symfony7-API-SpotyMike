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
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class passwordLost extends AbstractController
{
     private $exceptionManager;
     private $repository;
     private $entityManager;
     private $tokenVerifier;

     public function __construct(ExceptionManager $exceptionManager, EntityManagerInterface $entityManager, TokenVerifierService $tokenVerifier)
     {
          $this->exceptionManager = $exceptionManager;
          $this->entityManager = $entityManager;
          $this->tokenVerifier = $tokenVerifier;
          $this->repository = $entityManager->getRepository(User::class);
     }

     #[Route('/password-lost', name: 'password_lost', methods: 'POST')]
     public function password_lost(Request $request, JWTTokenManagerInterface $JWTManager): JsonResponse
     {
          $data = $request->request->all();

          if (isset($data['email'])) {

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
          } else {
               return $this->exceptionManager->EmailMissingPassLost();
          }

          // Trop de tentatives :
          // Vérification du nombre de tentatives et du délai entre les tentatives infructueuses
          if ($user->getNbTry() >= 3) {
               $lastTryTimestamp = $user->getLastTryTimestamp();
               $fiveMinutesAgo = (new \DateTimeImmutable())->sub(new \DateInterval('PT5M'));
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

          $user->setNbTry($user->getNbTry() + 1);
          $user->setLastTryTimestamp(new \DateTimeImmutable());
          $this->entityManager->persist($user);
          $this->entityManager->flush();

          $currentDateTime = new \DateTime('now', new \DateTimeZone('UTC'));
          $expiration = clone $currentDateTime;
          $expiration->modify('+2 minutes');

          $token = $JWTManager->create($user, ['exp' => $expiration->getTimestamp()]);

          // Remplacer tous les points dans le token par des esperluettes
          $tokenWithAmpersands = str_replace('.', '&', $token);


          return new JsonResponse([
               'success' => true,
               'token' => $tokenWithAmpersands,
               'message' => 'Un email de réinitialisation de mot de passe a été envoyé à votre adresse email. Veuillez suivre les instructions contenues dans l\'email pour réinitialiser votre mot de passe.'
          ], 200);
     }
}
