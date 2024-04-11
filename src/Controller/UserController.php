<?php

namespace App\Controller;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\MiddlewareController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\ExceptionManager;
use Exception;

class UserController extends MiddlewareController
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

    #[Route('/user', name: 'user_put', methods: 'PUT')]
    public function update(): JsonResponse
    {
        $phone = "0668000000";
        if (preg_match("/^[0-9]{10}$/", $phone)) {

            $user = $this->repository->findOneBy(["id" => 1]);
            $old = $user->getTel();
            $user->setTel($phone);
            $this->entityManager->flush();
            return $this->json([
                "New_tel" => $user->getTel(),
                "Old_tel" => $old,
                "user" => $user->serializer(),
            ]);
        }
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
    public function register(Request $request, ExceptionManager $exceptionManager): JsonResponse
    {

        $data = $request->request->all();

        // Vérifier si aucune donnée n'est envoyée
        if (empty($data)) {
            return $this->exceptionManager->invalidDataProvided();
        }

        // Validation du format de téléphone (format français)
        if (!empty($data['tel'])) {
            $tel = $data['tel'];
            if (!preg_match('/^0[1-9]([0-9]{2}){4}$/', $tel)) {
                return $this->exceptionManager->invalidPhoneNumberFormat();
            }
        }

        // Validation du sexe
        if (!empty($data['sexe'])) {
            $allowedGenders = ['0', '1', '']; // 0 pour femme, 1 pour homme, '' pour non spécifié
            if (!in_array($data['sexe'], $allowedGenders)) {
                return $this->exceptionManager->invalidGenderValue();
            }
        }




        return new JsonResponse(['success' => 'Authentification réussie.'], 200);
    }
}
