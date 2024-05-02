<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ExceptionManager;

class AccountDeactivationController extends AbstractController
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

     #[Route('/account-deactivation', name: 'account_deactivation', methods: 'DELETE')]
     public function account_deactivation(Request $request): JsonResponse
     {

          //Non authentifié A FAIRE
          $dataMiddellware = $this->tokenVerifier->checkToken($request);
          if (gettype($dataMiddellware) == 'boolean') {
               return $this->json($this->tokenVerifier->sendJsonErrorToken($dataMiddellware),401);
          }

          $user = $dataMiddellware;

          //Compte déjà désactivé
          if ($user->getIsActive() == false) {
               return $this->exceptionManager->isAccDesa();
          } else {
               $user->setIsActive(false);
               $this->entityManager->persist($user);
               $this->entityManager->flush();
          }

          return $this->json([
               'success' => true,
               'message' => 'Votre compte a été désactivé avec succès. Nous sommes désolés de vous voir partir.'
          ], 200);
     }
}
