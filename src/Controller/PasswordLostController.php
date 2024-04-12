<?php

namespace App\Controller;

use App\Entity\User;
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

     #[Route('/password-lost', name: 'password-lost', methods: 'POST')]
     public function index(Request $request): JsonResponse
     {
          $data = $request->request->all();

          // Email manquant
          if (empty($data['email'])) {
               return $this->exceptionManager->EmailMissing();
          }

          // Format d'email invalide 
          if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
               return $this->exceptionManager->invalidEmail();
          }

          // Email non trouvé
          $existingUser = $this->repository->findOneBy(['email' => $data['email']]);
          if ($existingUser === null) {
               return $this->exceptionManager->emailNotFound();
          }

          // Trop de demandes A FAIRE

          return new JsonResponse(['success' => 'true', 'message' => 'Un mail de réinitialisation de mot de pass a été envoyé à votre adresse email. Veuillez suivre les instructions contenues dans l\'email pour éinitialiser votre mot de passe'], 200);
     }
}
