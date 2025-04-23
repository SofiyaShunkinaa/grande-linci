<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Kitten::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Kitten $kitten = null;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotNull]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: ["В ожидании", "Подтверждено", "Отклонено"], message: "Invalid status")]
    private string $status = 'В ожидании';

    #[ORM\Column(type: 'boolean')]
    private bool $isViewed = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getKitten(): ?Kitten
    {
        return $this->kitten;
    }

    public function setKitten(?Kitten $kitten): self
    {
        $this->kitten = $kitten;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function isViewed(): bool
    {
        return $this->isViewed;
    }

    public function setIsViewed(bool $isViewed): self
    {
        $this->isViewed = $isViewed;
        return $this;
    }

    public function __toString(): string
    {
        return sprintf("Заявка %d: %s - %s", $this->id, $this->kitten->getName(), $this->status);
    }

    public function getUserDetails(): string
    {
        return $this->user ? sprintf("%s (%s)", $this->user->getFirstName(), $this->user->getEmail()) : 'Нет данных';
    }

    public function getKittenDetails(): string
    {
        return $this->kitten
            ? sprintf("%s (Помет: %s, Окрас: %s)", $this->kitten->getName(), $this->kitten->getLitter(), $this->kitten->getColor())
            : 'Нет данных';
    }


}
