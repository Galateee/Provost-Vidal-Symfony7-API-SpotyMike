<?php

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
class Album
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 90)]
    private ?string $title = null;

    #[ORM\Column(length: 20)]
    private array $categorie = [];

    #[ORM\Column(length: 1, nullable: false)]
    private ?int $visibility = 0;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\ManyToMany(targetEntity: Artist::class, mappedBy: 'artist_album')]
    private Collection $album_artist;

    #[ORM\ManyToMany(targetEntity: Song::class, inversedBy: 'song_album')]
    private Collection $album_song;

    #[ORM\ManyToMany(targetEntity: Playlist::class, mappedBy: 'playlist_album')]
    private Collection $album_playlist;

    public function __construct()
    {
        $this->album_artist = new ArrayCollection();
        $this->album_song = new ArrayCollection();
        $this->album_playlist = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCategorie(): array
    {
        return $this->categorie;
    }

    public function setCategorie(array $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getVisibility(): ?int
    {
        return $this->visibility;
    }

    public function setVisibility(?int $visibility): static
    {
        $this->visibility =  $visibility;

        return $this;
    }

    public function getAlbumCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setAlbumCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }


    public function serializer()
    {
        return [
            "id" => $this->getId(),
            "title" => $this->getTitle(),
            "categorie" => $this->getCategorie(),
            "visibility" => $this->getVisibility(),
            "creatAt" => $this->getAlbumCreateAt(),
        ];
    }

    /**
     * @return Collection<int, Artist>
     */
    public function getAlbumArtist(): Collection
    {
        return $this->album_artist;
    }

    public function addAlbumArtist(Artist $albumArtist): static
    {
        if (!$this->album_artist->contains($albumArtist)) {
            $this->album_artist->add($albumArtist);
            $albumArtist->addArtistAlbum($this);
        }

        return $this;
    }

    public function removeAlbumArtist(Artist $albumArtist): static
    {
        if ($this->album_artist->removeElement($albumArtist)) {
            $albumArtist->removeArtistAlbum($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, song>
     */
    public function getAlbumSong(): Collection
    {
        return $this->album_song;
    }

    public function addAlbumSong(song $albumsong): static
    {
        if (!$this->album_song->contains($albumsong)) {
            $this->album_song->add($albumsong);
        }

        return $this;
    }

    public function removeAlbumSong(song $albumsong): static
    {
        $this->album_song->removeElement($albumsong);

        return $this;
    }

    /**
     * @return Collection<int, Playlist>
     */
    public function getAlbumPlaylist(): Collection
    {
        return $this->album_playlist;
    }

    public function addAlbumPlaylist(Playlist $albumPlaylist): static
    {
        if (!$this->album_playlist->contains($albumPlaylist)) {
            $this->album_playlist->add($albumPlaylist);
        }

        return $this;
    }

    public function removeAlbumPlaylist(Playlist $albumPlaylist): static
    {
        $this->album_playlist->removeElement($albumPlaylist);

        return $this;
    }
}
