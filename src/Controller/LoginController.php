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

        $data = $request->request->all();

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);

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

        // Si aucun utilisateur n'est trouvé pour cet email
        if (!$user) {
            return $this->exceptionManager->userNotFoundLogin();
        }

        // Compte non activé ou suspendu
        if ($user->getIsActive() == false) {
            return $this->exceptionManager->inactiveAccountLogin();
        }

        // Trop de tentatives :
        // Vérification du nombre de tentatives et du délai entre les tentatives infructueuses
        if ($user->getNbTry() >= 5) {
            $lastTryTimestamp = $user->getLastTryTimestamp();
            $fiveMinutesAgo = (new \DateTimeImmutable())->sub(new \DateInterval('PT5M'));
            if ($lastTryTimestamp >= $fiveMinutesAgo) {
                // L'utilisateur doit attendre
                return $this->exceptionManager->maxPasswordTryLogin();
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

        // Si tout est bon, authentification réussie
        return $this->json([
            'error' => false,
            'message' => 'L\'utilisateur a été authentifié avec succès.',
            'user' => $user->serializer(),
        ], 200);
    }
}
