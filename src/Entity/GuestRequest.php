<?php

namespace App\Entity;

use App\Repository\GuestRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GuestRequestRepository::class)]
class GuestRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'guest_request.name.not_blank')]
    #[Assert\Regex(
        pattern: '/^[\p{L}\s\-]+$/u',
        message: 'guest_request.name.invalid_format'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'guest_request.phone.not_blank')]
    #[Assert\Regex(
        pattern: '/^\+?[0-9\s\-\(\)]+$/',
        message: 'guest_request.phone.invalid_format'
    )]
    private ?string $phone = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $requestDate = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isViewedByAdmin = false;

    public function isViewedByAdmin(): bool
    {
        return $this->isViewedByAdmin;
    }

    public function setIsViewedByAdmin(bool $isViewedByAdmin): static
    {
        $this->isViewedByAdmin = $isViewedByAdmin;
        return $this;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getRequestDate(): ?\DateTimeInterface
    {
        return $this->requestDate;
    }

    public function setRequestDate(\DateTimeInterface $requestDate): static
    {
        $this->requestDate = $requestDate;

        return $this;
    }
}