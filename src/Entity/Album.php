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

    #[ORM\ManyToOne(inversedBy: 'albums', cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Artist $artist = null;

    #[ORM\Column(length: 90)]
    private ?string $title = null;

    #[ORM\Column(length: 20)]
    private array $categorie = [];

    #[ORM\Column(length: 1, nullable: false)]
    private ?int $visibility = 0;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;


    #[ORM\OneToMany(targetEntity: Song::class, mappedBy: 'album')]
    private Collection $song;

    public function __construct()
    {
        $this->song = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): static
    {
        $this->artist = $artist;

        return $this;
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


    public function serializer($children = false)
    {
        return [
            "id" => $this->getId(),
            "title" => $this->getTitle(),
            "categorie" => $this->getCategorie(),
            "visibility" => $this->getVisibility(),
            "creatAt" => $this->getAlbumCreateAt(),
            "artist" => $this->getArtist()
        ];
    }

    /**
     * @return Collection<int, Song>
     */
    public function getSongIdSong(): Collection
    {
        return $this->song;
    }

    public function addSongIdSong(Song $song): static
    {
        if (!$this->song->contains($song)) {
            $this->song->add($song);
            $song->setAlbum($this);
        }

        return $this;
    }

    public function removeSongIdSong(Song $song): static
    {
        if ($this->song->removeElement($song)) {
            // set the owning side to null (unless already changed)
            if ($song->getAlbum() === $this) {
                $song->setAlbum(null);
            }
        }

        return $this;
    }
}