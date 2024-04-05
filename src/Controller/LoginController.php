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
    public function login(Request $request): JsonResponse
    {

                // REMARQUE :
                // Si le password n'est pas le bon par rapport à l'email -> succes quand même
                // l'exception "compte non activé ou suspendu" pas encore fait
                // 
                // BONUS : ajout d'une exception "vérification si l'utilisateur existe"            

                $data = $request->request->all();

                // Vérification de la présence des données obligatoires
                if (!isset($data['email']) || !isset($data['password'])) {
                    return $this->exceptionManager->missingEmailOrPassword();
                }
        
                // Validation du format de l'email
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    return $this->exceptionManager->invalidEmail();
                }
        
                // Vérification du mot de passe selon plusieurs critères
                $password = $data['password'];
                if (
                    strlen($password) < 8 ||                               // au moins 8 caractères
                    !preg_match('/[A-Z]/', $password) ||                   // au moins une majuscule
                    !preg_match('/[a-z]/', $password) ||                   // au moins une minuscule
                    !preg_match('/\d/', $password) ||                      // au moins un chiffre
                    !preg_match('/[^a-zA-Z0-9]/', $password)               // au moins un caractère spécial
                ) {
                    return $this->exceptionManager->invalidPasswordCriteria();
                }
        
                // Votre logique de gestion de l'utilisateur, par exemple, récupérer l'utilisateur depuis la base de données
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        
                // Vérification si l'utilisateur existe
                if (!$user) {
                    return $this->exceptionManager->UserDontExist();
                }

                /*
                // Vérification du mot de passe pour mdp hashé
                if (!password_verify($data['password'], $user->getPassword())) {
                    return $this->exceptionManager->invalidPassword();
                }*/

                // Vérification du mot de passe pour mdp pas hashé
                if ($data['password'] !== $user->getPassword()) {
                    return $this->exceptionManager->invalidPassword();
                }
        
                // Si tout est bon, authentification réussie
                return new JsonResponse(['success' => 'Authentification réussie.'], 200);
        
    }

}