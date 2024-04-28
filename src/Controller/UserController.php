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
    private $tokenVerifier;

    public function __construct(ExceptionManager $exceptionManager, EntityManagerInterface $entityManager, TokenVerifierService $tokenVerifier)
    {
        $this->exceptionManager = $exceptionManager;
        $this->entityManager = $entityManager;
        $this->tokenVerifier = $tokenVerifier;
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
    public function user_post(Request $request): JsonResponse
    {

        $user = new User();

        $data = $request->request->all();

        // Vérifier si aucune donnée n'est envoyée
        if (
            empty($data['tel']) &&
            empty($data['sexe']) &&
            empty($data['firstname']) &&
            empty($data['lastname'])
        ) {
            return $this->exceptionManager->invalidDataProvidedUser();
        }

        // Format de téléphone invalide
        if (!empty($data['tel']) && !preg_match('/^0[1-9]([0-9]{2}){4}$/', $data['tel'])) {
            return $this->exceptionManager->invalidPhoneNumberFormatUser();
        }

        // vérification du sexe
        if (!empty($data['sexe'])) {
            if (!in_array($data['sexe'], ['0', '1'])) {
                return $this->exceptionManager->invalidGenderValueRegisterUser();
            }
            $user->setSexe($data['sexe'][0] == '0' ? 'Homme' : 'Femme');
        } else {
            $user->setSexe("Homme");
        }

        // Données fournies non valides
        $allowedKeys = ['tel', 'sexe', 'firstname', 'lastname'];
        $providedKeys = array_keys($data);

        if (array_diff($providedKeys, $allowedKeys)) {
            return $this->exceptionManager->invalidDataProvidedUser();
        }

        // Non authentifié
        $dataMiddellware = $this->tokenVerifier->checkToken($request);
        if (gettype($dataMiddellware) == 'boolean') {
            return $this->json($this->tokenVerifier->sendJsonErrorToken($dataMiddellware));
        }

        // Conflit dans les données
        if (!empty($data['tel'])) {
            $existingUser = $this->repository->findOneBy(['tel' => $data['tel']]);
            if ($existingUser !== null) {
                return $this->exceptionManager->telAlreadyUsedUser();
            }
        }

        // Erreur de validation
        if (!empty($data['firstname']) && !preg_match('/^[a-zA-ZÀ-ÿ\-]{2,60}$/', $data['firstname'])) {
            return $this->exceptionManager->errorDataValidationUser();
        }
        if (!empty($data['lastname']) && !preg_match('/^[a-zA-ZÀ-ÿ\-]{2,60}$/', $data['lastname'])) {
            return $this->exceptionManager->errorDataValidationUser();
        }

        /*
        $user->setIdUser("User_" . uniqid());
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setTel($data['tel']);

        $user->setLastTryTimestamp(new \DateTimeImmutable());
        $user->setCreateAt(new \DateTimeImmutable());
        $user->setUpdateAt(new \DateTime());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        */

        return $this->json([
            'error' => false,
            'message' => 'Votre inscription a bien été prise en compte'
        ], 200);
    }
}
