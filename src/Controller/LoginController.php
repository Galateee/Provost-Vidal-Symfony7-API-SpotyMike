<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ExceptionManager;

class LoginController extends AbstractController
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

    #[Route('/login', name: 'app_login', methods: 'GET')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome GET work',
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
    public function login(Request $request): JsonResponse
    {

        // REMARQUE :
        // Si le password n'est pas le bon par rapport à l'email -> succes quand même
        // 
        // BONUS : ajout d'une exception "vérification si l'utilisateur existe"            

        $data = $request->request->all();

        // Données manquante
        if (
            !isset($data['email']) ||
            !isset($data['password'])
        ) {
            return $this->exceptionManager->missingDataLogin();
        }

        // Format d'email invalide
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->exceptionManager->invalidEmailLogin();
        }

        // Format du mot de passe invalide
        if (
            strlen($data['password']) < 8                     ||         // au moins 8 caractères
            !preg_match('/[A-Z]/', $data['password'])         ||         // au moins une majuscule
            !preg_match('/[a-z]/', $data['password'])         ||         // au moins une minuscule
            !preg_match('/\d/', $data['password'])            ||         // au moins un chiffre
            !preg_match('/[^a-zA-Z0-9]/', $data['password'])             // au moins un caractère spécial
        ) {
            return $this->exceptionManager->invalidPasswordCriteriaLogin();
        }

        // Récupération de l'utilisateur par son email
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        // Si aucun utilisateur n'est trouvé pour cet email
        if (!$user) {
            return $this->exceptionManager->userNotFoundLogin();
        }

        // Compte non activé ou suspendu
        if ($user->getIsActive() == false) {
            return $this->exceptionManager->inactiveAccountLogin();
        }

        // Trop de tentatives 

        // Vérification si le nombre de tentatives est supérieur ou égal à 5
        if ($user->getNbTry() >= 5) {
            return $this->exceptionManager->maxPasswordTryLogin();
        }

        // Vérification du mot de passe
        if (!password_verify($data['password'], $user->getPassword())) {
            // Augmentation du nombre de tentatives
            $user->setNbTry($user->getNbTry() + 1);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->exceptionManager->invalidCredentialsLogin();
        }

        // Réinitialisation du nombre de tentatives en cas de connexion réussie
        $user->setNbTry(0);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Si tout est bon, authentification réussie
        return $this->json([
            'error' => false,
            'message' => 'L\'utilisateur a été authentifié avec succès.',
            'user' => $user->serializer(),
       ], 200);
    }
}
