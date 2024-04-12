<?php

namespace App\Controller;

use App\Entity\Artist;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ExceptionManager;

class ArtistController extends AbstractController
{

    private $exceptionManager;
    private $repository;
    private $entityManager;

    public function __construct(ExceptionManager $exceptionManager, EntityManagerInterface $entityManager)
    {
        $this->exceptionManager = $exceptionManager;
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Artist::class);
    }

    #[Route('/artist', name: 'artist_put', methods: 'PUT')]
    public function update(): JsonResponse
    {
        $phone = "0668000000";
        if (preg_match("/^[0-9]{10}$/", $phone)) {

            $artist = $this->repository->findOneBy(["id" => 1]);
            $old = $artist->getTel();
            $artist->setTel($phone);
            $this->entityManager->flush();
            return $this->json([
                "New_tel" => $artist->getTel(),
                "Old_tel" => $old,
                "artist" => $artist->serializer(),
            ]);
        }
        return $this->json([]);
    }

    #[Route('/artist', name: 'artist_delete', methods: 'DELETE')]
    public function delete(): JsonResponse
    {
        $this->entityManager->remove($this->repository->findOneBy(["id" => 1]));
        $this->entityManager->flush();
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ArtistController.php',
        ]);
    }

    #[Route('/artist/all', name: 'artist_get_all', methods: 'GET')]
    public function readAll(): JsonResponse
    {
        $result = [];

        try {
            if (count($artists = $this->repository->findAll()) > 0)
                foreach ($artists as $artist) {
                    array_push($result, $artist->serializer());
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

    #[Route('/artist', name: 'artist_post', methods: 'POST')]
    public function create(Request $request): JsonResponse
    {
        /*
        $artist = new Artist();
        $artist->setFirstName("The Weeknd");
        $artist->setLastName(null);
        $artist->setSexe("Male");
        $artist->setBirthDate(new DateTimeImmutable());
        $artist->setlabel("XO");
        $artist->setIdUser("Artist_".rand(0,999));
        $password = "TheWeeknd";

        $this->entityManager->persist($artist);
        $this->entityManager->flush();

        return $this->json([
            'artist' => $artist->serializer(),
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ArtistController.php',
        ]);
        */

        $data = $request->request->all();

        // Donnée obligatoires manquantes 
        if (
            !isset($data['label'])  ||
            !isset($data['fullname'])
        ) {
            return $this->exceptionManager->missingData();
        }

        // Format de l'id du label invalide
        if (!preg_match("/^[a-zA-Z0-9_]+$/", $data['label'])) {
            return $this->exceptionManager->invalidLabelFormat();
        }

        // Non authentifié A FAIRE

        // Utilisateur non éligible pour être artist (condition = avoir 16 ans minimum)
        $birthdate = new DateTimeImmutable($data['birthdate']);
        $minimumAge = 16;
        $today = new DateTimeImmutable();
        $age = $today->diff($birthdate)->y;
        if ($age < $minimumAge) {
            return $this->exceptionManager->minimumAgeForArtist();
        }

        // Compte artist existant pour l'utilisateur A FAIRE 

        // Nom d'artist déja utilisé
        $existingArtist = $this->repository->findOneBy(['fullname' => $data['fullname']]);
        if ($existingArtist !== null) {
            return $this->exceptionManager->artistAllreadyExist();
        }

        // pas oublié de gérer l'envoie de artist_id
        return new JsonResponse(['succes' => 'true', 'message' => 'Votre compte d\'artiste a été créé avec succès. Bienvenue dans notre communauté d\'artistes !', 'artist_id' => ''], 201);
    }

    #[Route('/artist', name: 'artist_get', methods: 'GET')]
    public function read(Request $request): JsonResponse
    {
        $data = $request->request->all();

        // Paramètre de pagination invalide 
        if (!is_numeric($data['currentPage']) || $data['currentPage'] <= 0) {
            return $this->exceptionManager->invalidPaginationValue();
        }

        // Non authentifié A FAIRE

        // Aucun artiste trouvé
        $currentPage = $data['currentPage'];
        $artistsPerPage = 5;

        $offset = ($currentPage - 1) * $artistsPerPage;

        $artists = $this->repository->findBy([], null, $artistsPerPage, $offset);

        if (empty($artists)) {
            return $this->exceptionManager->NoArtistInPagination();
        }

        // A FAIRE
        return new JsonResponse(['succes' => 'true', 'message' => ''], 200);
    }

    #[Route('/artist/{fullname}', name: 'artist_get_info', methods: ['GET'])]
    public function getInfo(string $fullname): JsonResponse
    {
        // Nom d'artiste non fourni
        if (empty($fullname)) {
            return $this->exceptionManager->missingArtistName();
        }

        // Format du nom d'artiste invalide
        if (!preg_match("/^[a-zA-Z\s]+$/", $fullname)) {
            return $this->exceptionManager->invalidArtistNameFormat();
        }

        // Non authentifié A FAIRE

        // Artiste non trouvé
        $artist = $this->repository->findOneBy(['fullname' => $fullname]);
        if (!$artist) {
            return $this->exceptionManager->artistNotFound();
        }

        // Succès
        return $this->json([
            'artist' => $artist->serializer(),
            'message' => 'Artist information retrieved successfully.',
        ]);
    }
}
