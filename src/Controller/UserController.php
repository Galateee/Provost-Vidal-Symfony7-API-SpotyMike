<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\ExceptionManager;

class UserController extends AbstractController
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

    #[Route('/user', name: 'user_delete', methods: 'DELETE')]
    public function delete(): JsonResponse
    {
        $this->entityManager->remove($this->repository->findOneBy(["id" => 1]));
        $this->entityManager->flush();
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/user', name: 'user_get', methods: 'GET')]
    public function read(): JsonResponse
    {


        $serializer = new Serializer([new ObjectNormalizer()]);
        // $jsonContent = $serializer->serialize($person, 'json');
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/user/all', name: 'user_get_all', methods: 'GET')]
    public function readAll(): JsonResponse
    {
        $result = [];

        try {
            if (count($users = $this->repository->findAll()) > 0)
                foreach ($users as $user) {
                    array_push($result, $user->serializer());
                }
            return new JsonResponse([
                'data' => $result,
                'message' => 'Successful'
            ], 400);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => $exception->getMessage()
            ], 404);
        }
    }

    #[Route('/user', name: 'user_post', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {

        $data = $request->request->all();

        // Vérifier si aucune donnée n'est envoyée
        if (
            empty($data['tel']) &&
            empty($data['sexe']) &&
            empty($data['firstname']) &&
            empty($data['lastname'])
        ) {
            return $this->exceptionManager->noDataProvided();
        }

        // Format de téléphone invalide
        if (!empty($data['tel'])) {
            $tel = $data['tel'];
            if (!preg_match('/^0[1-9]([0-9]{2}){4}$/', $tel)) {
                return $this->exceptionManager->invalidPhoneNumberFormat();
            }
        }

        // Valeur de sexe invalide
        if (!empty($data['sexe'])) {
            $allowedGenders = ['0', '1', '']; // 0 pour femme, 1 pour homme, '' pour non spécifié
            if (!in_array($data['sexe'], $allowedGenders)) {
                return $this->exceptionManager->invalidGenderValue();
            }
        }

        // Vérification des données fournies non valides
        if (!empty($data['firstname']) && !preg_match('/^[a-zA-ZÀ-ÿ\-]+$/', $data['firstname'])) {
            return $this->exceptionManager->invalidDataProvided();
        }
        if (!empty($data['lastname']) && !preg_match('/^[a-zA-ZÀ-ÿ\-]+$/', $data['lastname'])) {
            return $this->exceptionManager->invalidDataProvided();
        }


        // Non authentifié A FAIRE

        // Conflit dans les données
        $existingUser = $this->repository->findOneBy(['tel' => $data['tel']]);
        if ($existingUser !== null) {
            return $this->exceptionManager->telAlreadyUsed();
        }

        // Erreur de validation A FAIRE

        return new JsonResponse(['error' => 'false','message' => 'Votre inscription a bien été prise en compte'], 200);
    }
}
