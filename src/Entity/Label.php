<?php

namespace App\Entity;

use App\Repository\LabelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LabelRepository::class)]
class Label
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $yearCreation = null;

    #[ORM\ManyToMany(targetEntity: Artist::class, mappedBy: 'Artist_Has_Label')]
    private Collection $Label_forArtist;

    public function __construct()
    {
        $this->artistsLabel_id = new ArrayCollection();
        $this->Label_forArtist = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getYearCreation(): ?\DateTimeInterface
    {
        return $this->yearCreation;
    }

    public function setYearCreation(\DateTimeInterface $yearCreation): static
    {
        $this->yearCreation = $yearCreation;

        return $this;
    }

    /**
     * @return Collection<int, Artist>
     */
    public function getArtistsLabelId(): Collection
    {
        return $this->artistsLabel_id;
    }

    public function addArtistsLabelId(Artist $artistsLabelId): static
    {
        if (!$this->artistsLabel_id->contains($artistsLabelId)) {
            $this->artistsLabel_id->add($artistsLabelId);
            $artistsLabelId->addLabelId($this);
        }

        return $this;
    }

    public function removeArtistsLabelId(Artist $artistsLabelId): static
    {
        if ($this->artistsLabel_id->removeElement($artistsLabelId)) {
            $artistsLabelId->removeLabelId($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Artist>
     */
    public function getLabelForArtist(): Collection
    {
        return $this->Label_forArtist;
    }

    public function addLabelForArtist(Artist $labelForArtist): static
    {
        if (!$this->Label_forArtist->contains($labelForArtist)) {
            $this->Label_forArtist->add($labelForArtist);
            $labelForArtist->addArtistHasLabel($this);
        }

        return $this;
    }

    public function removeLabelForArtist(Artist $labelForArtist): static
    {
        if ($this->Label_forArtist->removeElement($labelForArtist)) {
            $labelForArtist->removeArtistHasLabel($this);
        }

        return $this;
    }
}
