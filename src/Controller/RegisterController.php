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

          $user = new User();

          $data = $request->request->all();

          // Donnée manquante
          if (
               !isset($data['firstname'])  ||
               !isset($data['lastname'])   ||
               !isset($data['email'])      ||
               !isset($data['password'])   ||
               !isset($data['dateBirth'])
          ) {
               return $this->exceptionManager->missingData();
          }

          // Vérification des données fournies non valides
          if (!preg_match('/^[a-zA-ZÀ-ÿ\-]{1,60}$/', $data['firstname'])) {
               return $this->exceptionManager->invalidDataProvided();
          }
          if (!preg_match('/^[a-zA-ZÀ-ÿ\-]{1,60}$/', $data['lastname'])) {
               return $this->exceptionManager->invalidDataProvided();
          }

          // Format d'email invalide
          if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
               return $this->exceptionManager->invalidEmail();
          }

          // Format du mot de passe invalide
          if (
               strlen($data['password']) < 8                     ||         // au moins 8 caractères
               !preg_match('/[A-Z]/', $data['password'])         ||         // au moins une majuscule
               !preg_match('/[a-z]/', $data['password'])         ||         // au moins une minuscule
               !preg_match('/\d/', $data['password'])            ||         // au moins un chiffre
               !preg_match('/[^a-zA-Z0-9]/', $data['password'])             // au moins un caractère spécial
          ) {
               return $this->exceptionManager->invalidPasswordCriteria();
          }

          // Format de la date de naissance invalide
          if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data['dateBirth'])) {
               return $this->exceptionManager->invalidDateOfBirthFormat();
          }

          // Extraction du jour, du mois et de l'année de la date de naissance
          list($day, $month, $year) = explode('/', $data['dateBirth']);

          // Vérification des limites de jour, mois et année
          if ($month > 12 || $day > 31 || $year > 2024) {
               return $this->exceptionManager->invalidDateOfBirthFormat();
          }

          // Calcul de l'âge
          $dob = DateTime::createFromFormat('d/m/Y', $data['dateBirth']);
          $today = new DateTime();
          $age = $today->diff($dob)->y;

          // Age minimum non respecté (moins de 12 ans)
          if ($age <= 12) {
               return $this->exceptionManager->minimumAgeNotMet();
          }
          
          $dateBirth = DateTime::createFromFormat('d/m/Y', $data['dateBirth'])->format('Y-m-d');

          // Format de téléphone invalide
          if (!empty($data['tel']) && !preg_match('/^0[1-9]([0-9]{2}){4}$/', $data['tel'])) {
               return $this->exceptionManager->invalidPhoneNumberFormat();
          } else {
               $user->setTel("");
          }


          if (!empty($data['sexe'])) {
               if (!in_array($data['sexe'], ['0', '1'])) {
                    return $this->exceptionManager->invalidGenderValue();
               }
              $user->setSexe($data['sexe'][0] == '0' ? 'Homme': 'Femme');
          } else {
               $user -> setSexe("Homme");
          }

          // Email déjà utilisé
          
          $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
          if ($existingUser !== null) {
               return $this->exceptionManager->emailAlreadyUsed();
          }
          

          // Si tout est bon, register réussie
          $user->setIdUser("User_" . uniqid());
          $user->setFirstName($data['firstname']);
          $user->setLastName($data['lastname']);
          $user->setEmail($data['email']);
          $user->setPassword($data['password']);
          $user->setDateBirth($dateBirth);
          if (!empty($data['tel'])) {
               $user->setTel($data['tel']);
          }
          $user->setCreateAt(new \DateTimeImmutable());
          $user->setUpdateAt(new \DateTime());
          $this->entityManager->persist($user);
          $this->entityManager->flush();
          return $this->json([
               'error' => false,
               'message' => 'L\'utilisateur a bien été créé avec succès.',
               'user' => $user->serializer(),
          ], 201);
     }
}
