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

     public function __construct(ExceptionManager $exceptionManager, EntityManagerInterface $entityManager)
     {
          $this->exceptionManager = $exceptionManager;
          $this->entityManager = $entityManager;
          $this->repository = $entityManager->getRepository(User::class);
     }

     #[Route('/account-deactivation', name: 'account_deactivation', methods: 'DELETE')]
     public function account_deactivation(Request $request): JsonResponse
     {
          $data = $request->request->all();


          //Non authentifié A FAIRE

          //Compte déjà désactivé


          return new JsonResponse(['success' => true, 'message' => 'Votre compte a été désactivé avec succès. Nous sommes désolés de vous voir partir.'], 200);
     }
}
