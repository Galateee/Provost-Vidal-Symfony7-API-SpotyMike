<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ExceptionManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class ResetPasswordController extends AbstractController
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

     #[Route('/reset-password/{token}', name: 'reset_password', methods: 'POST')]
     public function reset_password(string $token, Request $request, JWTTokenManagerInterface $JWTManager): JsonResponse
     {
          $data = $request->request->all();

          // Token manquant ou invalide A FAIRE

          // Nouveau mot de passe manquant
          if (empty($data['password'])) {
               return $this->exceptionManager->newMDPResetPass();
          }

          // Format du nouveau mot de passe invalide
          $password = $data['password'];
          if (
               strlen($password) < 8 ||                               // au moins 8 caractères
               !preg_match('/[A-Z]/', $password) ||                   // au moins une majuscule
               !preg_match('/[a-z]/', $password) ||                   // au moins une minuscule
               !preg_match('/\d/', $password) ||                      // au moins un chiffre
               !preg_match('/[^a-zA-Z0-9]/', $password)               // au moins un caractère spécial
          ) {
               return $this->exceptionManager->invalidFormatMDPResetPass();
          }

          // Token expiré A FAIRE

          return new JsonResponse([
               'success' => true,
               'message' => 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.'
          ], 200);
     }
}
