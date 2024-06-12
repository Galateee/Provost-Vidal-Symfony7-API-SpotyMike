<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ExceptionManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class resetPassword extends AbstractController
{
     private $exceptionManager;
     private $repository;
     private $entityManager;
     private $tokenVerifier;
     private $jwtProvider;

     public function __construct(ExceptionManager $exceptionManager, EntityManagerInterface $entityManager, TokenVerifierService $tokenVerifier, JWSProviderInterface $jwtProvider)
     {
          $this->exceptionManager = $exceptionManager;
          $this->entityManager = $entityManager;
          $this->tokenVerifier = $tokenVerifier;
          $this->jwtProvider = $jwtProvider;
          $this->repository = $entityManager->getRepository(User::class);
     }

     #[Route('/reset-password/{token}', name: 'reset_password', methods: 'POST')]
     public function reset_password(Request $request, UserPasswordHasherInterface $passwordHash, string $token): JsonResponse
     {
          $tokenWithoutAmpersands = str_replace('&', '.', $token);

          $dataMiddellware = $this->tokenVerifier->checkTokenWithParam($tokenWithoutAmpersands);

          $password = $request->request->get('password');

          // Token manquant ou invalide
          if (gettype($dataMiddellware) == 'boolean') {
               return $this->exceptionManager->noTokenResetPass();
          }

          // Nouveau mot de passe manquant
          if (empty($password)) {
               return $this->exceptionManager->newMDPResetPass();
          }

          // Format du nouveau mot de passe invalide
          if (
               strlen($password) < 8 ||                               // au moins 8 caractères
               !preg_match('/[A-Z]/', $password) ||                   // au moins une majuscule
               !preg_match('/[a-z]/', $password) ||                   // au moins une minuscule
               !preg_match('/\d/', $password) ||                      // au moins un chiffre
               !preg_match('/[^a-zA-Z0-9]/', $password)               // au moins un caractère spécial
          ) {
               return $this->exceptionManager->invalidFormatMDPResetPass();
          }

          // Token expiré
          $currentTimestamp = new \DateTimeImmutable();
          $dataToken = $this->jwtProvider->load($tokenWithoutAmpersands);
          if ($dataToken->getPayload()['exp'] < $currentTimestamp->getTimestamp()) {
               return $this->exceptionManager->tokenExpirationResetPass();
          }

          $user = $dataMiddellware;
          $hash = $passwordHash->hashPassword($user, $password);
          $user->setPassword($hash);
          $user->setUpdateAt(new \DateTimeImmutable());
          $this->entityManager->flush();

          return new JsonResponse([
               'success' => true,
               'message' => 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.',
          ], 200);
     }
}
