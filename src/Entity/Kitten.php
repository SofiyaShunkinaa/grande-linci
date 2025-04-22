<?php

namespace App\Entity;

use App\Repository\KittenRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\StatusType;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: KittenRepository::class)]
#[Vich\Uploadable]
class Kitten
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Breed $breed = null;

    #[ORM\Column(nullable: false)]
    private ?int $price = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?KittenStatus $kittenStatus = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Gender $gender = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Color $color = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Litter $litter = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ["default" => "default.png"])]
    private ?string $imageLink = 'default.png';

    #[Vich\UploadableField(mapping: 'kitten_image', fileNameProperty: 'imageLink')]
    private ?File $imageFile = null;

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

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

    public function getKittenStatus(): ?KittenStatus
    {
        return $this->kittenStatus;
    }

    public function setKittenStatus(?KittenStatus $kittenStatus): static
    {
        $this->kittenStatus = $kittenStatus;

        return $this;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(?Gender $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getColor(): ?Color
    {
        return $this->color;
    }

    public function setColor(?Color $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getLitter(): ?Litter
    {
        return $this->litter;
    }

    public function setLitter(?Litter $litter): static
    {
        $this->litter = $litter;

        return $this;
    }

    public function getImageLink(): ?string
    {
        return $this->imageLink;
    }

    public function setImageLink(?string $imageLink): static
    {
        $this->imageLink = $imageLink;

        return $this;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getDetails(): string
    {
        return sprintf(
            "%s (Помет: %s, Окрас: %s)",
            $this->getName(),
            $this->getLitter()?->getName() ?? 'Неизвестно',
            $this->getColor()?->getName() ?? 'Неизвестно'
        );
    }

}
