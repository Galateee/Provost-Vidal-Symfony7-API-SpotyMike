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

    // Route création artiste 
    #[Route('/artist', name: 'artist_post', methods: 'POST')]
    public function artist_post(Request $request): JsonResponse
    {

        $data = $request->request->all();

        // Donnée obligatoires manquantes 
        if (
            !isset($data['label']) || $data['label'] == "" ||
            !isset($data['fullname']) || $data['fullname'] == ""
        ) {
            return $this->exceptionManager->noDataCreateArtist();
        }

        // Format du fullname invalide
        if (!preg_match('/^[\w\W]{2,30}$/', $data['fullname'])) {
            return $this->exceptionManager->invalidFullnameFormatCreateArtist();
        }

        // Format de l'id du label invalide
        if (!preg_match("/^[a-zA-Z0-9_]+$/", $data['label'])) {
            return $this->exceptionManager->invalidLabelFormatCreateArtist();
        }

        // Non authentifié
        $dataMiddellware = $this->tokenVerifier->checkToken($request);
        if (gettype($dataMiddellware) == 'boolean') {
            return $this->exceptionManager->noAuthenticationCreateArtist();
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
                $file = base64_decode($explodeData[1], true);

                // Erreur de décodage
                if ($file === false) {
                    return $this->exceptionManager->decodageCreateArtist();
                }

                // Format de fichier non pris en charge 
                $mimeType = explode(';', explode(':', $explodeData[0])[1])[0];
                if (!in_array($mimeType, ['image/jpeg', 'image/png'])) {
                    return $this->exceptionManager->errorFormatFileCreateArtist();
                }

                // Taille du fichier trop/pas assez volumineux 
                $fileSize = strlen($file);
                $minSize = 1 * 1024 * 1024 / 8; // 1 MB
                $maxSize = 7 * 1024 * 1024 / 8; // 7 MB
                if ($fileSize < $minSize || $fileSize > $maxSize) {
                    return $this->exceptionManager->sizeFileCreateArtist();
                }

                $chemin = $this->getParameter('upload_directory') . '/' . $user->getEmail();
                if (!file_exists($chemin)) {
                    mkdir($chemin);
                }
                file_put_contents($chemin . '/avatar.png', $file);
            }
        }

        $artist = new Artist;
        $artist->setUserIdUser($user);
        $artist->setFullname($data["fullname"]);
        $artist->setLabel($data["label"]);
        if (!empty($data['description'])) {
            $artist->setDescription($data["description"]);
        }
        //$artist->setDescription($data["description"]);
        $artist->setArtistCreateAt(new \DateTimeImmutable());

        $this->entityManager->persist($artist);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Votre compte d\'artiste a été créé avec succès. Bienvenue dans notre communauté d\'artistes !',
            'artist_id' => $user->getId(),
        ], 201);
    }

    // Route de récupération toutes les infos des artistes
    #[Route('/artist', name: 'artist_get', methods: 'GET')]
    public function artist_get(Request $request): JsonResponse
    {
        $rawContent = $request->getContent();
        parse_str($rawContent, $data);

        // Paramètre de pagination invalide 
        if (!isset($data['currentPage']) || !is_numeric($data['currentPage']) || $data['currentPage'] <= 0) {
            return $this->exceptionManager->invalidPaginationValueGetArtist();
        }

        // Non authentifié
        $dataMiddellware = $this->tokenVerifier->checkToken($request);
        if (gettype($dataMiddellware) == 'boolean') {
            return $this->exceptionManager->noAuthenticationGetArtist();
        }

        // Aucun artiste trouvé
        $currentPage = $data['currentPage'];
        $artistsPerPage = isset($data['limit']) && is_numeric($data['limit']) ? (int)$data['limit'] : 5;

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
                    array_push($result, $artist->serializerGetAllAlbums());
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

    // Route de récupération toutes les infos d'un artiste
    #[Route('/artist/{fullname}', name: 'artist_get_info', methods: ['GET'])]
    public function artist_get_info(?string $fullname = null, Request $request): JsonResponse
    {
        // Vérifier si le fullname est vide ou non fourni
        if ($fullname === null || trim($fullname) === '') {
            return $this->exceptionManager->missingArtistNameArtistFullname();
        }

        // Format du nom d'artiste invalide
        if (!preg_match('/^[\w\W]{2,30}$/', $fullname)) {
            return $this->exceptionManager->invalidArtistNameFormatArtistFullname();
        }

        // Non authentifié
        $dataMiddellware = $this->tokenVerifier->checkToken($request);
        if (gettype($dataMiddellware) == 'boolean') {
            return $this->exceptionManager->noAuthenticationArtistFullname();
        }

        // Artiste non trouvé
        $artist = $this->repository->findOneBy(['fullname' => $fullname]);
        if (!$artist) {
            return $this->exceptionManager->artistNotFoundArtistFullname();
        }

        // Succès
        return $this->json([
            'error' => false,
            'artist' => $artist->serializerGetAllAlbums(),
            'message' => 'Artist information retrieved successfully.',
        ]);
    }

    // Route de mise à jour de compte artist

    // Route de désactivation du compte artist
    #[Route('/artist', name: 'artist_delete', methods: 'DELETE')]
    public function artist_delete(Request $request): JsonResponse
    {

        //Non authentifié
        $dataMiddellware = $this->tokenVerifier->checkToken($request);
        if (gettype($dataMiddellware) == 'boolean') {
            return $this->exceptionManager->noAuthenticationDeleteArtist();
        }

        $artist = $dataMiddellware->getArtist();

        // Artiste non trouvé
        if ($artist == null) {
            return $this->exceptionManager->nameUsedDeleteArtist();
        }

        //Compte déjà désactivé
        if ($artist->getArtistIsActive() == false) {
            return $this->exceptionManager->isDeleteArtist();
        } else {
            $artist->setArtistIsActive(false);
            $this->entityManager->persist($artist);
            $this->entityManager->flush();
        }

        return $this->json([
            'success' => true,
            'message' => 'Le compte artiste a été désactivé avec succès.'
        ], 200);
    }
}
