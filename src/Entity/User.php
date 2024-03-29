<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // #[ORM\Id]
    #[ORM\Column(length: 90)]
    private ?string $idUser = null;

    #[ORM\Column(length: 55)]
    private ?string $firstName = null;

    #[ORM\Column(length: 55)]
    private ?string $lastName = null;

    #[ORM\Column(length: 55)]
    private ?string $sexe = null;

    #[ORM\Column(length: 55)]
    private ?\Date $dateBirth = null;

    #[ORM\Column(length: 80)]
    private ?string $email = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $tel = null;

    #[ORM\Column(length: 90)]
    private ?string $password = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updateAt = null;


    #[ORM\OneToOne(mappedBy: 'User_idUser', cascade: ['persist', 'remove'])]
    private ?Artist $artist = null;

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

    public function getfirstName(): ?string
    {
        return $this->firstName;
    }

    public function setfirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getlastName(): ?string
    {
        return $this->lastName;
    }

    public function setlastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getsexe(): ?string
    {
        return $this->sexe;
    }

    public function setsexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getdateBirth(): ?\Date
    {
        return $this->dateBirth;
    }

    public function setdateBirth(\Date $dateBirth): static
    {
        $this->dateBirth = $dateBirth;

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

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): static
    {
        $this->tel = $tel;

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
}