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

    // Route de récupération des albums
    #[Route('/albums', name: 'albums_get_all', methods: 'GET')]
    public function albums_get_all(Request $request): JsonResponse
    {
        return $this->json([
            'who' => 'Ici c\'est get /albums ',
            'error' => false,
        ], 200);
    }

    // Route de récupération d'un album
    #[Route('/album/{id}', name: 'album_get', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function album_get(Request $request, $id): JsonResponse
    {
        return $this->json([
            'who' => 'Ici c\'est get /album/{id} ',
            'error' => false,
        ], 200);
    }
    
    // Route de recherche d'albums
    #[Route('/album/search', name: 'album_search', methods: ['GET'])]
    public function album_search(Request $request): JsonResponse
    {
        return $this->json([
            'who' => 'Ici c\'est get /album/search ',
            'error' => false,
        ], 200);
    }

    // Route de création d'un album
    #[Route('/album', name: 'album_post', methods: 'POST')]
    public function album_post(Request $request): JsonResponse
    {
        return $this->json([
            'who' => 'Ici c\'est post /album ',
            'error' => false,
            'message' => 'Album créé avec succès.'
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
