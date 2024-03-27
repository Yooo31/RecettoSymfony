<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Validator\BanWord;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 3, max: 30, minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.', maxMessage: 'Le nom doit contenir au maximum {{ limit }} caractères.')]
    #[BanWord()]
    private ?string $name = '';

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min: 7, max: 7, minMessage: 'Le nom doit contenir {{ limit }} caractères.', maxMessage: 'Le nom doit contenir {{ limit }} caractères.')]
    #[Assert\Regex('/^#[0-9A-F]{6}$/i', message: 'La couleur doit être au format hexadécimal.')]
    #[BanWord()]
    private ?string $color = "";

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
