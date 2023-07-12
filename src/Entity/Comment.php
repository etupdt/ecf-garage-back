<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'Le prénom est obligatoire'
    )]
    #[Assert\Length(
        min: 2,
        max: 32,
        minMessage: 'Le prénom doit faire au minimum {{ limit }} caractères de long',
        maxMessage: 'Le prénom doit faire au maximum {{ limit }} caractères de long',
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z -éèàêç]*$/",
        match: true,
        message: 'prénom : Caractères autorisés : lettres, tiret et quotes'
    )]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'Le nom est obligatoire'
    )]
    #[Assert\Length(
        min: 2, 
        max: 32,
        minMessage: 'Le nom doit faire au minimum {{ limit }} caractères de long',
        maxMessage: 'Le nom doit faire au maximum {{ limit }} caractères de long',
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z -éèàêç]*$/",
        match: true,
        message: 'nom : Caractères autorisés : lettres, tirets et quotes'
    )]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(
        message: 'Le commentaire est obligatoire'
    )]
    #[Assert\Length(
        min: 2,
        minMessage: 'Le commentaire doit faire au minimum {{ limit }} caractères de long',
    )]
    #[Assert\Regex(
        pattern: "/[0-9a-zA-Z -+*_='\/]*$/",
        match: true,
        message: 'commentaire : Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $comment = null;

    #[ORM\Column]
    #[Assert\Positive(
        message: 'La note doit être supérieure à zéro'
    )]
    #[Assert\LessThan(
        value: 6,
        message: 'La note doit être inférieur à 6'
    )]
    private ?int $note = null;

    #[ORM\Column]
    private ?bool $isApproved = null;

    #[ORM\ManyToOne(inversedBy: 'comments', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Garage $garage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function isIsApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): self
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    public function getGarage(): ?Garage
    {
        return $this->garage;
    }

    public function setGarage(?Garage $garage): self
    {
        $this->garage = $garage;

        return $this;
    }
}
