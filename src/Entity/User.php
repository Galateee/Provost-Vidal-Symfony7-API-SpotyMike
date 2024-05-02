<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // #[ORM\Id]
    #[ORM\Column(length: 90, unique: true)]
    private ?string $idUser = null;

    #[ORM\Column(length: 55)]
    private ?string $firstname = null;

    #[ORM\Column(length: 55)]
    private ?string $lastname = null;

    #[ORM\Column(length: 55, nullable: true)]
    private ?int $sexe = null;

    #[ORM\Column(length: 55)]
    private ?\DateTimeImmutable $dateBirth = null;

    #[ORM\Column(length: 80, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $tel = "";

    #[ORM\Column(length: 90)]
    private ?string $password = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?int $nbTry = 0;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $isActive = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastTryTimestamp = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updateAt = null;


    #[ORM\OneToOne(mappedBy: 'User_idUser', cascade: ['persist', 'remove'])]
    private ?Artist $artist = null;

    #[ORM\ManyToMany(targetEntity: Artist::class, inversedBy: 'Artist_isFollow')]
    private Collection $user_follow_artist;

    #[ORM\ManyToMany(targetEntity: Playlist::class, inversedBy: 'playlist_isShare')]
    private Collection $User_share_Playlist;

    public function __construct()
    {
        $this->user_follow_artist = new ArrayCollection();
        $this->User_share_Playlist = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?string
    {
        return $this->idUser;
    }

    public function setIdUser(string $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe =  $sexe;

        return $this;
    }

    public function getDateBirth(): ?\DateTimeImmutable
    {
        return $this->dateBirth;
    }

    public function setDateBirth(string $dateBirth): self
    {
        $this->dateBirth = new \DateTimeImmutable($dateBirth);

        return $this;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getNbTry(): ?int
    {
        return $this->nbTry;
    }

    public function setNbTry(?int $nbTry): static
    {
        $this->nbTry =  $nbTry;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): static
    {
        $this->isActive =  $isActive;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): static
    {
        $this->tel = $tel !== null ? $tel : "";

        return $this;
    }

    public function getLastTryTimestamp(): ?\DateTimeImmutable
    {
        return $this->lastTryTimestamp;
    }

    public function setLastTryTimestamp(\DateTimeImmutable $lastTryTimestamp): static
    {
        $this->lastTryTimestamp = $lastTryTimestamp;

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

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(Artist $artist): static
    {
        // set the owning side of the relation if necessary
        if ($artist->getUserIdUser() !== $this) {
            $artist->setUserIdUser($this);
        }

        $this->artist = $artist;

        return $this;
    }

    public function getRoles(): array
    {

        return ["PUBLIC_ACCESS"];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function serializer()
    {
        return [
            "id" => $this->getId(),
            "idUser" => $this->getIdUser(),
            "firstname" => $this->getFirstname(),
            "lastname" => $this->getLastname(),
            "sexe" => $this->getSexe(),
            "dateBirth" => $this->getDateBirth(),
            "email" => $this->getEmail(),
            "tel" => $this->getTel(),
            'nbTry' => $this->getNbTry(),
            "isActive" => $this->getIsActive(),
            "lastTryTimestamp" => $this->getLastTryTimestamp(),
            "createAt" => $this->getCreateAt(),
            "updateeAt" => $this->getUpdateAt(),
            "artist" => $this->getArtist() ?  $this->getArtist()->serializer() : [],
        ];
    }

    public function serializerLogin()
    {
        return [
            "firstname" => $this->getFirstname(),
            "lastname" => $this->getLastname(),
            "email" => $this->getEmail(),
            "tel" => $this->getTel(),
            "sexe" => $this->getSexe(),
            "artist" => $this->getArtist() ?  $this->getArtist()->serializer() : [], // A REVOIR
            "dateBirth" => $this->getDateBirth(),
            "createAt" => $this->getCreateAt(),
        ];
    }

    public function serializerRegister()
    {
        return [
            "firstname" => $this->getFirstname(),
            "lastname" => $this->getLastname(),
            "email" => $this->getEmail(),
            "tel" => $this->getTel(),
            "sexe" => $this->getSexe(),
            "dateBirth" => $this->getDateBirth(),
            "createAt" => $this->getCreateAt(),
            "updateeAt" => $this->getUpdateAt(),
        ];
    }

    /**
     * @return Collection<int, Artist>
     */
    public function getUserFollowArtist(): Collection
    {
        return $this->user_follow_artist;
    }

    public function addUserFollowArtist(Artist $userFollowArtist): static
    {
        if (!$this->user_follow_artist->contains($userFollowArtist)) {
            $this->user_follow_artist->add($userFollowArtist);
        }

        return $this;
    }

    public function removeUserFollowArtist(Artist $userFollowArtist): static
    {
        $this->user_follow_artist->removeElement($userFollowArtist);

        return $this;
    }

    /**
     * @return Collection<int, Playlist>
     */
    public function getUserSharePlaylist(): Collection
    {
        return $this->User_share_Playlist;
    }

    public function addUserSharePlaylist(Playlist $userSharePlaylist): static
    {
        if (!$this->User_share_Playlist->contains($userSharePlaylist)) {
            $this->User_share_Playlist->add($userSharePlaylist);
        }

        return $this;
    }

    public function removeUserSharePlaylist(Playlist $userSharePlaylist): static
    {
        $this->User_share_Playlist->removeElement($userSharePlaylist);

        return $this;
    }
}
