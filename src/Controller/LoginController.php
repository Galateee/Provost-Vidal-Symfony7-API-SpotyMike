<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\ExceptionManager;

class LoginController extends AbstractController
{

    private $exceptionManager;
    private $repository;
    private $entityManager;

    public function __construct(ExceptionManager $exceptionManager, EntityManagerInterface $entityManager){
        $this->exceptionManager = $exceptionManager;
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(User::class);
    }

    #[Route('/login', name: 'app_login', methods: 'GET')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to MikeLand',
            'path' => 'src/Controller/LoginController.php',
            ]);
        }
        
    /*    
    // use Symfony\Component\HttpFoundation\Request;
    #[Route('/login', name: 'app_login_post', methods: ['POST', 'PUT'])]
    public function login(Request $request): JsonResponse
    {

        $user = $this->repository->findOneBy(["email" => "mike.sylvestre@lyknowledge.io"]);

        $parameters = json_decode($request->getContent(), true);
        return $this->json([
            'user' => json_encode($user->serializer()),
            'data' => $request->getContent(),
            'message' => 'Welcome to MikeLand',
            'path' => 'src/Controller/LoginController.php',
        ]);
    }
    */
    
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request, /*UserPasswordEncoderInterface $passwordEncode*/): JsonResponse
    {
                // Récupération des données de la requête
                $data = json_decode($request->getContent(), true);

                // Vérification de la présence des données obligatoires
                if (!isset($data['email']) || !isset($data['password'])) {
                    return $this->exceptionManager->missingData();
                }
        
                // Validation de l'email
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    return $this->exceptionManager->invalidEmail();
                }
        
                // Vérification du mot de passe selon vos critères (par exemple, 8 caractères minimum)
                if (strlen($data['password']) < 8) {
                    return $this->exceptionManager->invalidPasswordCriteria();
                }
        
                // Votre logique de gestion de l'utilisateur, par exemple, récupérer l'utilisateur depuis la base de données
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        
                // Vérification si l'utilisateur existe
                if (!$user) {
                    return $this->exceptionManager->UserDontExist();
                }
        
                // Vérification si le compte est activé ou non
                if (!$user->isActive()) {
                    return $this->exceptionManager->inactiveAccount();
                }
        
                // Votre logique de gestion de rate limiting, par exemple, enregistrer les tentatives de connexion
        
                // Vérification du mot de passe
                /*
                if (!$passwordEncoder->isPasswordValid($user, $data['password'])) {
                    return $this->exceptionManager->invalidCredentials();
                }*/
        
                // Si tout est bon, authentification réussie
                return new JsonResponse(['success' => 'Authentification réussie.'], 200);
        
    }

}