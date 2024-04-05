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

          // Vérification de la présence des données obligatoires
          if (
               !isset($data['firstname'])  ||
               !isset($data['lastname'])   ||
               !isset($data['email'])      ||
               !isset($data['password'])   ||
               !isset($data['dateBirth'])
          ) {
               return $this->exceptionManager->missingData();
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

          // Validation du format de la date de naissance (JJ/MM/AAAA)
          $dateOfBirth = $data['dateBirth'];
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

          // Vérification de l'âge minimum (12 ans)
          if ($age < 12) {
               return $this->exceptionManager->minimumAgeNotMet();
          }

          // Validation du format de téléphone
          if (!preg_match('/^\+?\d{8,15}$/', $data['tel'])) {
               return $this->exceptionManager->invalidPhoneNumberFormat();
          }

          // Validation du sexe
          $gender = $data['sexe'];
          if ($gender !== '0' && $gender !== '1' && $gender !== '') {
               return $this->exceptionManager->invalidGenderValue();
          }

          // Vérification si l'email est déjà utilisé
          $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
          if ($existingUser !== null) {
               return $this->exceptionManager->emailAlreadyUsed();
          }

          // Si tout est bon, authentification réussie
          return new JsonResponse(['success' => 'Authentification réussie.'], 200);
     }
}
