<?php

namespace App\Controller;

use App\Entity\PlaylistHasSong;
use App\Form\PlaylistHasSongType;
use App\Repository\PlaylistHasSongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/playlist/has/song')]
class PlaylistHasSongController extends AbstractController
{
    #[Route('/', name: 'app_playlist_has_song_index', methods: ['GET'])]
    public function index(PlaylistHasSongRepository $playlistHasSongRepository): Response
    {
        return $this->render('playlist_has_song/index.html.twig', [
            'playlist_has_songs' => $playlistHasSongRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_playlist_has_song_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $playlistHasSong = new PlaylistHasSong();
        $form = $this->createForm(PlaylistHasSongType::class, $playlistHasSong);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($playlistHasSong);
            $entityManager->flush();

            return $this->redirectToRoute('app_playlist_has_song_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('playlist_has_song/new.html.twig', [
            'playlist_has_song' => $playlistHasSong,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_playlist_has_song_show', methods: ['GET'])]
    public function show(PlaylistHasSong $playlistHasSong): Response
    {
        return $this->render('playlist_has_song/show.html.twig', [
            'playlist_has_song' => $playlistHasSong,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_playlist_has_song_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PlaylistHasSong $playlistHasSong, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlaylistHasSongType::class, $playlistHasSong);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_playlist_has_song_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('playlist_has_song/edit.html.twig', [
            'playlist_has_song' => $playlistHasSong,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_playlist_has_song_delete', methods: ['POST'])]
    public function delete(Request $request, PlaylistHasSong $playlistHasSong, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$playlistHasSong->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($playlistHasSong);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_playlist_has_song_index', [], Response::HTTP_SEE_OTHER);
    }
}
