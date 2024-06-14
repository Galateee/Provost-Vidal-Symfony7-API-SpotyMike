<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Album;
use App\Entity\Artist;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\ExceptionManager;

class albumController extends AbstractController
{

    private $exceptionManager;
    private $repositoryArtist;
    private $repositoryAlbum;
    private $entityManager;
    private $tokenVerifier;

    public function __construct(ExceptionManager $exceptionManager, EntityManagerInterface $entityManager, TokenVerifierService $tokenVerifier)
    {
        $this->exceptionManager = $exceptionManager;
        $this->entityManager = $entityManager;
        $this->tokenVerifier = $tokenVerifier;
        $this->repositoryArtist = $entityManager->getRepository(Artist::class);
        $this->repositoryAlbum = $entityManager->getRepository(Album::class);
    }

    // Route de récupération des albums
    #[Route('/albums', name: 'albums_get_all', methods: 'GET')]
    public function albums_get_all(Request $request): JsonResponse
    {
        $rawContent = $request->getContent();
        parse_str($rawContent, $data);

        // Paramètre de pagination invalide 
        if (!isset($data['currentPage']) || !is_numeric($data['currentPage']) || $data['currentPage'] <= 0) {
            return $this->exceptionManager->invalidPaginationValueGetAlbums();
        }

        // Non authentifié
        $dataMiddellware = $this->tokenVerifier->checkToken($request);
        if (gettype($dataMiddellware) == 'boolean') {
            return $this->exceptionManager->noAuthenticationGetAlbums();
        }

        // Aucun album trouvé
        $currentPage = $data['currentPage'];
        $albumsPerPage = isset($data['limit']) && is_numeric($data['limit']) ? (int)$data['limit'] : 5;

        $offset = ($currentPage - 1) * $albumsPerPage;

        $totalAlbums = $this->repositoryAlbum->count();

        $totalPages = ceil($totalAlbums / $albumsPerPage);

        $albums = $this->repositoryAlbum->findBy([], null, $albumsPerPage, $offset);

        if (empty($albums)) {
            return $this->exceptionManager->albumNotFoundGetAlbums();
        }

        $result = [];

        try {
            if (count($albums = $this->repositoryAlbum->findAll()) > 0)
                foreach ($albums as $album) {
                    array_push($result, $album->serializer());
                }

                return new JsonResponse([
                'error' => false,
                'albums' => $result,
                'pagination' => [
                    'currentPage' => $currentPage,
                    'totalPages' => $totalPages,
                    'totalArtists' => $totalAlbums,
                ],
            ], 201);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => $exception->getMessage()
            ], 404);
        }
    }

    // Route de récupération d'un album
    #[Route('/album/{id}', name: 'album_get', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function album_get(Request $request, ?int $id): JsonResponse
    {
        // ID d'album non fourni
        if ($id === null || trim($id) === '') {
            return $this->exceptionManager->obligatoryIdAlbumId();
        }

        // Non authentifié
        $dataMiddellware = $this->tokenVerifier->checkToken($request);
        if (gettype($dataMiddellware) == 'boolean') {
            return $this->exceptionManager->noAuthenticationAlbumId();
        }

        // Album non trouvé
        $album = $this->repositoryAlbum->findOneBy(['id' => $id]);
        if (!$album) {
            return $this->exceptionManager->albumNotFoundAlbumId();
        }

        return $this->json([
            'error' => false,
            'album' => $album->serializer(),
        ], 200);
    }

    // Route de recherche d'albums
    #[Route('/album/search', name: 'album_search', methods: ['GET'])]
    public function album_search(Request $request): JsonResponse
    {
        $rawContent = $request->getContent();
        parse_str($rawContent, $data);

        // Paramètre de pagination invalide 
        if (!isset($data['currentPage']) || !is_numeric($data['currentPage']) || $data['currentPage'] <= 0) {
            return $this->exceptionManager->invalidPaginationValueSearch();
        }

        // Paramètres invalides
        $allowedKeys = ['currentPage', 'title', 'fullname', 'label', 'year', 'featuring', 'categorie', 'limit'];
        $providedKeys = array_keys($data);
        if (array_diff($providedKeys, $allowedKeys)) {
            return $this->exceptionManager->invalidParameterSearch();
        } else if (
            !isset($data['currentPage']) || $data['currentPage'] == "" ||
            !isset($data['title']) || $data['title'] == "" ||
            !isset($data['fullname']) || $data['fullname'] == ""
        ) {
            return $this->exceptionManager->invalidParameterSearch();
        }

        // Catégorie invalide
        $providedCategories = json_decode($data['categorie'], true);
        if (!is_array($providedCategories)) {
            return $this->exceptionManager->invalidCategorySearch();
        }
        $allowedCategories = ['rap', 'r\'n\'b', 'gospel', 'soul', 'country', 'hip hop', 'jazz', 'mike'];
        foreach ($providedCategories as $categorie) {
            if (!in_array($categorie, $allowedCategories, true)) {
                return $this->exceptionManager->invalidCategorySearch();
            }
        }

        // Featuring invalide 

        // Non authentifié
        $dataMiddellware = $this->tokenVerifier->checkToken($request);
        if (gettype($dataMiddellware) == 'boolean') {
            return $this->exceptionManager->noAuthenticationSearch();
        }

        // Aucun album trouvé 
        // A REVOIR
        $album = $this->repositoryAlbum->findOneBy(['title' => $data['title']]);
        if (!$album) {
            return $this->exceptionManager->albumNotFoundSearch();
        }

        // Année invalide 


        return $this->json([
            'error' => false,
            'albums' => $album->serializer(),
        ], 200);
    }

    // Route de création d'un album
    #[Route('/album', name: 'album_post', methods: 'POST')]
    public function album_post(Request $request): JsonResponse
    {
        $data = $request->request->all();

        // Paramètres invalides
        $allowedKeys = ['visibility', 'cover', 'title', 'categorie'];
        $providedKeys = array_keys($data);
        if (array_diff($providedKeys, $allowedKeys)) {
            return $this->exceptionManager->invalidParameterPostPutAlbum();
        } else if (
            !isset($data['visibility']) || $data['visibility'] == "" ||
            !isset($data['cover']) || $data['cover'] == "" ||
            !isset($data['title']) || $data['title'] == "" ||
            !isset($data['categorie']) || $data['categorie'] == ""
        ) {
            return $this->exceptionManager->invalidParameterPostPutAlbum();
        }

        // Valeur de visibilité invalide
        if (!in_array($data['visibility'], ['0', '1'])) {
            return $this->exceptionManager->invalidGenderValueRegisterUser();
        }

        // Erreur de validation
        $providedCategories = json_decode($data['categorie'], true);
        if (!is_array($providedCategories) || !preg_match("/^[\w\W]{2,90}$/", $data['title'])) {
            return $this->exceptionManager->validationDataErrorPostPutAlbum();
        }

        // Catégorie invalide
        $allowedCategories = ['rap', 'r\'n\'b', 'gospel', 'soul', 'country', 'hip hop', 'jazz', 'mike'];
        foreach ($providedCategories as $categorie) {
            if (!in_array($categorie, $allowedCategories, true)) {
                return $this->exceptionManager->invalidCategoryPostPutAlbum();
            }
        }

        // Non authentifié
        $dataMiddellware = $this->tokenVerifier->checkToken($request);
        if (gettype($dataMiddellware) == 'boolean') {
            return $this->exceptionManager->noAuthenticationPostPutAlbum();
        }


        // Accès refusé / Non autorisé

        // Titre d'album déjà utilisé
        $existingAlbum = $this->repositoryAlbum->findOneBy(['title' => $data['title']]);
        if ($existingAlbum !== null) {
            return $this->exceptionManager->titleUsePostPutAlbum();
        }

        // base64
        if (isset($data['cover'])) {
            $parameters = $request->getContent();
            parse_str($parameters, $data);

            $explodeData = explode(",", $data['cover']);
            if (count($explodeData) == 2) {
                $file = base64_decode($explodeData[1], true);

                // Erreur de décodage
                if ($file === false) {
                    return $this->exceptionManager->decodagePostPutAlbum();
                }

                // Format de fichier non pris en charge 
                $mimeType = explode(';', explode(':', $explodeData[0])[1])[0];
                if (!in_array($mimeType, ['image/jpeg', 'image/png'])) {
                    return $this->exceptionManager->errorFormatFilePostPutAlbum();
                }

                // Taille du fichier trop/pas assez volumineux 
                $fileSize = strlen($file);
                $minSize = 1 * 1024 * 1024 / 8; // 1 MB
                $maxSize = 7 * 1024 * 1024 / 8; // 7 MB
                if ($fileSize < $minSize || $fileSize > $maxSize) {
                    return $this->exceptionManager->sizeFilePostPutAlbum();
                }

                $chemin = $this->getParameter('upload_directory') . '/' . $dataMiddellware->getEmail();
                if (!file_exists($chemin)) {
                    mkdir($chemin);
                }
                file_put_contents($chemin . '/cover.png', $file);
            }
        }

        $user = $dataMiddellware;
        $artist = $this->repositoryArtist->findOneBy(['User_idUser'=>$user]);

        $album = new album;

        $album->setIdAlbum("Album_".rand(0,999999999999));
        $album->setTitle($data['title']);
        $album->setCategorie($providedCategories);
        $album->setVisibility($data['visibility']);
        $album->setAlbumCreateAt(new \DateTimeImmutable());
        $album->setArtistUserIdUser($artist);

        $this->entityManager->persist($album);
        $this->entityManager->flush();


        return $this->json([
            'error' => false,
            'message' => 'Album créé avec succès.',
            'id' => $album->getId(),
        ], 200);
    }

    // Route de modification d'un album
    #[Route('/album/{id}', name: 'album_put', methods: 'PUT')]
    public function album_put(Request $request, $id): JsonResponse
    {
        return $this->json([
            'who' => 'Ici c\'est put /album/{id} ',
            'error' => false,
            'message' => 'Album mis à jour avec succès.'
        ], 200);
    }

    // Route d'ajout de song
    #[Route('/album/{id}/song', name: 'album_post_song', methods: 'POST')]
    public function album_post_song(Request $request): JsonResponse
    {
        return $this->json([
            'who' => 'Ici c\'est post /album/{id}/song ',
            'error' => false,
            'message' => 'Album mis à jour avec succès.'
        ], 200);
    }
}
