<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ExceptionManager;
use DateTime;

class RegisterController extends AbstractController
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

     #[Route('/register', name: 'app_register', methods: 'GET')]
     public function index(): JsonResponse
     {
          return $this->json([
               'message' => 'Welcome GET work',
               'path' => 'src/Controller/RegisterController.php',
          ]);
     }

     #[Route('/register', name: 'register', methods: ['POST'])]
     public function register(Request $request): JsonResponse
     {

          // REMARQUE :
          // 

          $data = $request->request->all();

          // Donnée manquante
          if (
               !isset($data['firstname'])  ||
               !isset($data['lastname'])   ||
               !isset($data['email'])      ||
               !isset($data['password'])   ||
               !isset($data['birthDate'])
          ) {
               return $this->exceptionManager->missingData();
          }

          // Format d'email invalide
          if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
               return $this->exceptionManager->invalidEmail();
          }

          // Format du mot de passe invalide
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

          // Format de la date de naissance invalide
          $dateOfBirth = $data['birthDate'];
          if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dateOfBirth)) {
               return $this->exceptionManager->invalidDateOfBirthFormat();
          }

          // Extraction du jour, du mois et de l'année de la date de naissance
          list($day, $month, $year) = explode('/', $dateOfBirth);

          // Vérification des limites de jour, mois et année
          if ($month > 12 || $day > 31 || $year > 2024) {
               return $this->exceptionManager->invalidDateOfBirthFormat();
          }

          // Calcul de l'âge
          $dob = DateTime::createFromFormat('d/m/Y', $dateOfBirth);
          $today = new DateTime();
          $age = $today->diff($dob)->y;

          // Age minimum non respecté (moins de 12 ans)
          if ($age < 12) {
               return $this->exceptionManager->minimumAgeNotMet();
          }

          // Format de téléphone invalide
          $tel = $data['tel'];
          if ($tel !== '' && !preg_match('/^0[1-9]([0-9]{2}){4}$/', $tel)) {
               return $this->exceptionManager->invalidPhoneNumberFormat();
          }

          // Valeur de sexe invalide
          $allowedGenders = ['0', '1', '']; // 0 pour femme, 1 pour homme, '' pour non spécifié
          if (!in_array($data['sexe'], $allowedGenders)) {
               return $this->exceptionManager->invalidGenderValue();
          }

          // Email déjà utilisé
          $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
          if ($existingUser !== null) {
               return $this->exceptionManager->emailAlreadyUsed();
          }

          // Si tout est bon, authentification réussie
          $user = new User();
          $user->setIdUser("User_". uniqid());
          $user->setFirstName($data['firstname']);
          $user->setLastName($data['lastname']);
          $user->setEmail($data['email']);
          $user->setPassword($password);
          $user->setBirthDate($dateOfBirth);
          $user->setTel($data['tel']);
          $user->setSexe($data['sexe']);
          $user->setCreateAt(new \DateTimeImmutable());
          $user->setUpdateAt(new \DateTime());
          $this->entityManager->persist($user);
          $this->entityManager->flush();
          return $this->json([
               'error' => 'false',
               'path' => 'L\'utilisateur a bien été créé avec succès.',
               'user' => $user->serializer(),
          ], 201);
     }
}
