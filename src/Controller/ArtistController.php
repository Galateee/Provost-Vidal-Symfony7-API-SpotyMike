<?php

namespace App\Controller;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ArtistController extends AbstractController
{

    private $repository;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Artist::class);
    }

    #[Route('/artist', name: 'artist_post', methods: 'POST')]
    public function create(Request $request): JsonResponse
    {

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
    }

    #[Route('/artist', name: 'artist_put', methods: 'PUT')]
    public function update(): JsonResponse
    {
        $phone = "0668000000";
        if(preg_match("/^[0-9]{10}$/", $phone)) {

            $artist = $this->repository->findOneBy(["id"=>1]);
            $old = $artist->getTel();
            $artist->setTel($phone);
            $this->entityManager->flush();
            return $this->json([
                "New_tel" => $artist->getTel(),
                "Old_tel" => $old,
                "artist" => $artist->serializer(),
            ]);
        }
    }

    #[Route('/artist', name: 'artist_delete', methods: 'DELETE')]
    public function delete(): JsonResponse
    {
        $this->entityManager->remove($this->repository->findOneBy(["id"=>1]));
        $this->entityManager->flush();
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ArtistController.php',
        ]);
    }

    #[Route('/artist', name: 'artist_get', methods: 'GET')]
    public function read(): JsonResponse
    {


        $serializer = new Serializer([new ObjectNormalizer()]);
        // $jsonContent = $serializer->serialize($person, 'json');
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
}
