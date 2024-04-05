<?php

namespace App\Entity;

use App\Repository\ArtistHasLabelRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistHasLabelRepository::class)]
class ArtistHasLabel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $createYear = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $deleateYear = null;

    #[ORM\Column(length: 255)]
    private ?string $id_Artist = null;

    #[ORM\Column(length: 255)]
    private ?string $id_Label = null;

    #[ORM\OneToMany(targetEntity: Playlist::class, mappedBy: 'artistHasLabel')]
    private Collection $Artist_idArtist;

    #[ORM\OneToMany(targetEntity: Song::class, mappedBy: 'artistHasLabel')]
    private Collection $Label_idLabel;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    public function __construct()
    {
        $this->Artist_idArtist = new ArrayCollection();
        $this->Label_idLabel = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreateYear(): ?\DateTimeInterface
    {
        return $this->createYear;
    }

    public function setCreateYear(\DateTimeInterface $createYear): static
    {
        $this->createYear = $createYear;

        return $this;
    }

    public function getDeleateYear(): ?\DateTimeInterface
    {
        return $this->deleateYear;
    }

    public function setDeleateYear(\DateTimeInterface $deleateYear): static
    {
        $this->deleateYear = $deleateYear;

        return $this;
    }

    public function getIdArtist(): ?string
    {
        return $this->id_Artist;
    }

    public function setIdArtist(string $id_Artist): static
    {
        $this->id_Artist = $id_Artist;

        return $this;
    }

    public function getIdLabel(): ?string
    {
        return $this->id_Label;
    }

    public function setIdLabel(string $id_Label): static
    {
        $this->id_Label = $id_Label;

        return $this;
    }

    public function serializer($children = false)
    {
        return [
            "id" => $this->getId(),
            "createYear" => $this->getCreateYear(),
            "deleteYear" => $this->getDeleteYear(),
            "idLabel" => ($children) ? $this->getIdLabel() : null,
            "idArtist" => ($children) ? $this->getIdArtist() : null
        ];
    }
}
