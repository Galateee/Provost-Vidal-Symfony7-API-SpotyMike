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

class artistController extends AbstractController
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
    public function artist_post(Request $request): JsonResponse
    {

        $data = $request->request->all();

        // Donnée obligatoires manquantes 
        if (
            !isset($data['label'])  ||
            !isset($data['fullname'])
        ) {
            return $this->exceptionManager->noDataCreateArtist();
        }

        // Format du fullname invalide
        if (!preg_match('/^[a-zA-ZÀ-ÿ\-]{2,30}$/', $data['fullname'])) {
            return $this->exceptionManager->invalidFullnameFormatCreateArtist();
        }

        // Format de l'id du label invalide
        if (!preg_match("/^[a-zA-Z0-9_]+$/", $data['label'])) {
            return $this->exceptionManager->invalidLabelFormatCreateArtist();
        }

        // Non authentifié
        $dataMiddellware = $this->tokenVerifier->checkToken($request);
        if (gettype($dataMiddellware) == 'boolean') {
            return $this->json($this->tokenVerifier->sendJsonErrorToken($dataMiddellware), 401);
        }

        $user = $dataMiddellware;

        // Utilisateur non éligible pour être artist (condition = avoir 16 ans minimum)
        $dateBirth = $user->getDateBirth();
        $minimumAge = 16;
        $today = new DateTimeImmutable();
        $age = $today->diff($dateBirth)->y;
        if ($age < $minimumAge) {
            return $this->exceptionManager->minimumAgeCreateArtist();
        }

        // Nom d'artist déja utilisé
        $existingArtist = $this->repository->findOneBy(['fullname' => $data['fullname']]);
        if ($existingArtist !== null) {
            return $this->exceptionManager->artistAlreadyExistCreateArtist();
        }

        // base64
        if (isset($data['avatar'])) {
            $parameters = $request->getContent();
            parse_str($parameters, $data);

            $explodeData = explode(",", $data['avatar']);
            if (count($explodeData) == 2) {

                $file = base64_decode($data['avatar']);
                $chemin = $this->getParameter('upload_directory') . '/' . $user->getEmail();
                if (!file_exists($chemin)) {
                    mkdir($chemin);
                }
                file_put_contents($chemin . '/file.png', $file);
            }
        }

        // Erreur de décodage

        // Format de fichier non pris en charge 

        // Taille du fichier trop/pas assez volumineux 


        $artist = new Artist;
        $artist->setLabel($data["label"]);
        $artist->setFullname($data["fullname"]);
        if (!empty($data['description'])) {
            $artist->setDescription($data["description"]);
        }
        $artist->setUserIdUser($user);
        $artist->setDescription($data["description"]);

        $this->entityManager->persist($artist);
        $this->entityManager->flush();


        // pas oublié de gérer l'envoie de artist_id
        return $this->json([
            'success' => true,
            'message' => 'Votre compte d\'artiste a été créé avec succès. Bienvenue dans notre communauté d\'artistes !',
            'artist_id' => $user->getIdUser(),
        ], 201);
    }

    #[Route('/artist', name: 'artist_get', methods: 'GET')]
    public function artist_get(Request $request): JsonResponse
    {
        // récuperer tout les data recu
        $rawContent = $request->getContent();
        parse_str($rawContent, $data);

        // Paramètre de pagination invalide 
        if (!is_numeric($data['currentPage']) || $data['currentPage'] <= 0) {
            return $this->exceptionManager->invalidPaginationValueGetArtist();
        }

        // Non authentifié
        $dataMiddellware = $this->tokenVerifier->checkToken($request);
        if (gettype($dataMiddellware) == 'boolean') {
            return $this->json($this->tokenVerifier->sendJsonErrorToken($dataMiddellware), 401);
        }

        // Aucun artiste trouvé
        $currentPage = $data['currentPage'];
        $artistsPerPage = 5;

        $offset = ($currentPage - 1) * $artistsPerPage;

        $totalArtists = $this->repository->count();

        $totalPages = ceil($totalArtists / $artistsPerPage);

        $artists = $this->repository->findBy([], null, $artistsPerPage, $offset);

        if (empty($artists)) {
            return $this->exceptionManager->NoArtistInPaginationGetArtist();
        }

        $result = [];

        try {
            if (count($artists = $this->repository->findAll()) > 0)
                foreach ($artists as $artist) {
                    array_push($result, $artist->serializerGetAll());
                }
            return new JsonResponse([
                'error' => false,
                'artists' => $result,
                'message' => 'Informations des artistes récupérées avec succès.',
                'pagination' => [
                    'currentPage' => $currentPage,
                    'totalPages' => $totalPages,
                    'totalArtists' => $totalArtists,
                ],
            ], 201);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => $exception->getMessage()
            ], 404);
        }
    }

    #[Route('/artist/{fullname}', name: 'artist_get_info', methods: ['GET'])]
    public function getInfo(string $fullname): JsonResponse
    {
        // Nom d'artiste non fourni
        if (empty($fullname)) {
            return $this->exceptionManager->missingArtistNameArtistFullname();
        }

        // Format du nom d'artiste invalide
        if (!preg_match("/^[a-zA-Z\s]+$/", $fullname)) {
            return $this->exceptionManager->invalidArtistNameFormatArtistFullname();
        }

        // Non authentifié A FAIRE

        // Artiste non trouvé
        $artist = $this->repository->findOneBy(['fullname' => $fullname]);
        if (!$artist) {
            return $this->exceptionManager->artistNotFoundArtistFullname();
        }

        // Succès
        return $this->json([
            'artist' => $artist->serializer(),
            'message' => 'Artist information retrieved successfully.',
        ]);
    }
}
