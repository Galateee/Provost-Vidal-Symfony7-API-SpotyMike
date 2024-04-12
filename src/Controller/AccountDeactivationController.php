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

     #[Route('/account-deactivation', name: 'account-deactivation', methods: 'DELETE')]
     public function index(Request $request): JsonResponse
     {
          $data = $request->request->all();


          return new JsonResponse(['success' => 'true', 'message' => ''], 200);
     }
}
