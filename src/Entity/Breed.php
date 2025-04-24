<?php

namespace App\Entity;

use App\Repository\BreedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BreedRepository::class)]
class Breed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $keyWord = null;

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

    public function getKeyWord(): ?string
    {
        return $this->keyWord;
    }

    public function setKeyWord(string $keyWord): static
    {
        $this->keyWord = $keyWord;

        return $this;
    }
    public function __toString(): string
    {
        return $this->name;
    }
}
