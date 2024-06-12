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
    private $repository;
    private $entityManager;

    public function __construct(ExceptionManager $exceptionManager, EntityManagerInterface $entityManager)
    {
        $this->exceptionManager = $exceptionManager;
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(User::class);
    }


    #[Route('/artist', name: 'artist_post', methods: 'POST')]
    public function createArtist(Request $request): JsonResponse
    {

        $artist = new Artist();
        return $this->json([
            'artist' => $artist->serializer(),
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ArtistController.php',
        ]);
    }

    #[Route('/albums', name: 'albums_get', methods: 'GET')]
    public function create(Request $request): JsonResponse
    {

        $album = new Album();
        $album->setName("The Weeknd");
        $album->setCategory("Pop");
        $album->setCover("My Dear Melancholy");
        $album->setYear(new DateTimeImmutable());
        $album->setArtistUserIdUser($artist);
        $album->setIdAlbum($user);
        $this->entityManager->persist($album);
        $this->entityManager->flush();

        return $this->json([
            'album' => $album->serializer(),
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AlbumController.php',
        ]);


        $data = $request->request->all();

        return new JsonResponse(['succes' => 'true', 'message' => 'Succes', 'album_id' => '']);
    }


    #[Route('/albums', name: 'albums_get', methods: 'GET')]
    public function getAlbums(Request $request): JsonResponse
    {
        // Vérifier si l'utilisateur est authentifié
        if (!$this->isAuthenticated()) {
            return $this->exceptionManager->noAuthentication();
        }

        // Récupérer les paramètres de pagination
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 5);

        // Vérifier la pagination
        if ($page <= 1 || $limit <= 5) {
            return $this->exceptionManager->invalidPaginationValue();
        }

        // Récupérer les albums depuis la base de données ou tout autre source de données
        $albums = $this->getAlbumsFromDatabase($page, $limit);

        // Vérifier si des albums ont été trouvés
        if (empty($albums)) {
            return $this->exceptionManager->albumNotFound();
        }

        // Formatter les données des albums
        $formattedAlbums = $this->formatAlbums($albums);

        return new JsonResponse($formattedAlbums);
    }

    // Méthode factice pour vérifier si l'utilisateur est authentifié
    private function isAuthenticated(): bool
    {
        // Implémentez votre logique d'authentification ici
        // Par exemple, vérifiez si l'utilisateur est connecté
        // Retourne true si authentifié, sinon false
        return true;
    }

    // Méthode factice pour récupérer les albums depuis la base de données
    private function getAlbumsFromDatabase(int $page, int $limit): array
    {
        // Implémentez la logique pour récupérer les albums depuis la base de données
        // Utilisez $page et $limit pour la pagination
        // Retourne un tableau d'albums
        return [
            ['id' => 1, 'title' => 'Album 1', 'artist' => 'Artist 1'],
            ['id' => 2, 'title' => 'Album 2', 'artist' => 'Artist 2'],
        ];
    }

    // Méthode pour formatter les données des albums selon les spécifications
    private function formatAlbums(array $albums): array
    {
        // Implémentez la logique pour formatter les données des albums
        // selon les spécifications demandées
        $formattedAlbums = [];
        foreach ($albums as $album) {
            // Formattez chaque album selon vos besoins
            $formattedAlbums[] = [
                'id' => $album['id'],
                'title' => $album['title'],
                'artist' => $album['artist'],
                // Ajoutez d'autres données si nécessaire
            ];
        }
        return $formattedAlbums;
    }
}
