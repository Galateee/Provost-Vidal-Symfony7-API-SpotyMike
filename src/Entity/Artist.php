<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
class Artist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'artist', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User_idUser = null;

    #[ORM\Column(length: 55)]
    private ?string $fullname = null;

    #[ORM\Column(length: 90)]
    private ?string $label = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $isActive = true;

    #[ORM\ManyToMany(targetEntity: Song::class, mappedBy: 'Artist_idUser')]
    private Collection $songs;

    #[ORM\OneToMany(targetEntity: Album::class, mappedBy: 'artist_User_idUser')]
    private Collection $albums;

    public function __construct()
    {
        $this->songs = new ArrayCollection();
        $this->albums = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserIdUser(): ?User
    {
        return $this->User_idUser;
    }

    public function setUserIdUser(User $User_idUser): static
    {
        $this->User_idUser = $User_idUser;

        return $this;
    }

    public function getfullname(): ?string
    {
        return $this->fullname;
    }

    public function setfullname(string $fullname): static
    {
        $this->fullname = $fullname;

        return $this;
    }
    
    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getArtistCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setArtistCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getArtistIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setArtistIsActive(?bool $isActive): static
    {
        $this->isActive =  $isActive;

        return $this;
    }

    public function serializer($children = false)
    {
        return [
            "id" => $this->getId(),
            "fullname" => $this->getFullname(),
            "label" => $this->getLabel(),
            "description" => $this->getDescription(),
            "album" => $this->getAlbums(),
            "songs" => $this->getSongs(),
            "user"=> $children ? $this->getUserIdUser() : []
        ];
    }

    public function serializerGetAllAlbums($children = false)
    {
        return [
            "firstname" => $this->getUserIdUser()->getFirstname(),
            "lastname" => $this->getUserIdUser()->getLastname(),
            "fullname" => $this->getFullname(),
            "avatar" => [],
            "sexe" => $this->getUserIdUser()->getSexe(),
            "dateBirth" => $this->getUserIdUser()->getDateBirth()->format('c'),
            "Artist.createdAt" => $this->getArtistCreateAt()->format('c'),
            "albums" => $children ? $this->getAlbums() : [],
        ];
    }

    /**
     * @return Collection<int, Song>
     */
    public function getSongs(): Collection
    {
        return $this->songs;
    }

    public function addSong(Song $song): static
    {
        if (!$this->songs->contains($song)) {
            $this->songs->add($song);
            $song->addArtistIdUser($this);
        }

        return $this;
    }

    public function removeSong(Song $song): static
    {
        if ($this->songs->removeElement($song)) {
            $song->removeArtistIdUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Album>
     */
    public function getAlbums(): Collection
    {
        return $this->albums;
    }

    public function addAlbum(Album $album): static
    {
        if (!$this->albums->contains($album)) {
            $this->albums->add($album);
            $album->setArtistUserIdUser($this);
        }

        return $this;
    }

    public function removeAlbum(Album $album): static
    {
        if ($this->albums->removeElement($album)) {
            // set the owning side to null (unless already changed)
            if ($album->getArtistUserIdUser() === $this) {
                $album->setArtistUserIdUser(null);
            }
        }

        return $this;
    }
}