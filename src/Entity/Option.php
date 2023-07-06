<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ORM\Table(name: '`option`')]
class Option
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'Le nom de l\'option est obligatoire'
    )]
    #[Assert\Length(
        min: 2,
        minMessage: 'Le nom de l\'option doit faire au minimum {{ limit }} caractères de long',
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z -\']*$/",
        match: false,
        message: 'Caractères autorisés : lettres, tiret et quotes'
    )]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(
        message: 'La description est obligatoire'
    )]
    #[Assert\Length(
        min: 2,
        minMessage: 'La description doit faire au minimum {{ limit }} caractères de long',
    )]
    #[Assert\Regex(
        pattern: "/[0-9a-zA-Z -+*_='/]*$/",
        match: false,
        message: 'Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $description = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

}
