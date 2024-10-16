<?php

namespace App\Entity;

use App\Repository\LitterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LitterRepository::class)]
class Litter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Breed $breed = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cat $catMother = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cat $catFather = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isActive = null;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getBreed(): ?Breed
    {
        return $this->breed;
    }

    public function setBreed(?Breed $breed): static
    {
        $this->breed = $breed;

        return $this;
    }

    public function getCatMother(): ?Cat
    {
        return $this->catMother;
    }

    public function setCatMother(?Cat $catMother): static
    {
        $this->catMother = $catMother;

        return $this;
    }

    public function getCatFather(): ?Cat
    {
        return $this->catFather;
    }

    public function setCatFather(?Cat $catFather): static
    {
        $this->catFather = $catFather;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }
}
