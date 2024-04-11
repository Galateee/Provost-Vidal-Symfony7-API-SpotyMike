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

    #[ORM\OneToOne(inversedBy: 'artist', cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User_idUser = null;

    #[ORM\Column(length: 55)]
    private ?string $fullName = null;

    #[ORM\Column(length: 55)]
    private ?string $sexe = null;

    #[ORM\Column(length: 55)]
    private ?string $tel = null;

    #[ORM\Column(length: 55)]
    private ?\DateTimeImmutable $birthDate = null;

    #[ORM\Column(length: 90)]
    private ?string $label = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column]
    private ?\DateTimeInterface $updateAt = null;


    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Song::class, mappedBy: 'Artist_idUser')]
    private Collection $songs;

    #[ORM\OneToMany(targetEntity: Album::class, mappedBy: 'artist_User_idUser')]
    private Collection $albums;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'user_follow_artist')]
    private Collection $Artist_isFollow;

    #[ORM\ManyToMany(targetEntity: Label::class, inversedBy: 'Label_forArtist')]
    private Collection $Artist_Has_Label;

    public function __construct()
    {
        $this->songs = new ArrayCollection();
        $this->albums = new ArrayCollection();
        $this->Artist_isFollow = new ArrayCollection();
        $this->Artist_Has_Label = new ArrayCollection();
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

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    
    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeImmutable $birthDate): static
    {
        $this->birthDate = $birthDate;

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

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeInterface $updateAt): static
    {
        $this->updateAt = $updateAt;

        return $this;
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

    public function serializer($children = false)
    {
        return [
            "id" => $this->getId(),
            "idUser" => ($children) ? $this->getUserIdUser() : null,
            "fullName" => $this->getFullName(),
            "sexe" => $this->getSexe(),
            "tel" => $this->getTel(),
            "birthDate" => $this->getBirthDate(),
            "label" => $this->getLabel(),
            "description" => $this->getDescription(),
            "createAt" => $this->getCreateAt(),
            "updateAt" => $this->getUpdateAt(),
            "songs" => $this->getSongs()
        ];
    }

    /**
     * @return Collection<int, User>
     */
    public function getArtistIsFollow(): Collection
    {
        return $this->Artist_isFollow;
    }

    public function addArtistIsFollow(User $artistIsFollow): static
    {
        if (!$this->Artist_isFollow->contains($artistIsFollow)) {
            $this->Artist_isFollow->add($artistIsFollow);
            $artistIsFollow->addUserFollowArtist($this);
        }

        return $this;
    }

    public function removeArtistIsFollow(User $artistIsFollow): static
    {
        if ($this->Artist_isFollow->removeElement($artistIsFollow)) {
            $artistIsFollow->removeUserFollowArtist($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Label>
     */
    public function getArtistHasLabel(): Collection
    {
        return $this->Artist_Has_Label;
    }

    public function addArtistHasLabel(Label $artistHasLabel): static
    {
        if (!$this->Artist_Has_Label->contains($artistHasLabel)) {
            $this->Artist_Has_Label->add($artistHasLabel);
        }

        return $this;
    }

    public function removeArtistHasLabel(Label $artistHasLabel): static
    {
        $this->Artist_Has_Label->removeElement($artistHasLabel);

        return $this;
    }
}