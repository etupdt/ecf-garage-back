<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'Le message est obligatoire'
    )]
    #[Assert\Length(
        min: 5,
        minMessage: 'Le sujet doit faire au minimum {{ limit }} caractères de long',
    )]
    #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z -\']*$/",
        match: false,
        message: 'Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(
        message: 'Le message est obligatoire'
    )]
    #[Assert\Length(
        min: 2,
        minMessage: 'Le message doit faire au minimum {{ limit }} caractères de long',
    )]
    #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z -\']*$/",
        match: false,
        message: 'Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'contacts', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Garage $garage = null;

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
        pattern: "/^[a-zA-Z -\']*$/",
        match: false,
        message: 'Caractères autorisés : lettres, tiret et quotes'
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
        pattern: "/^[a-zA-Z -\']*$/",
        match: false,
        message: 'Caractères autorisés : lettres, tirets et quotes'
    )]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email(
        message: 'L\'adresse mail n\'est pas valide.',
    )]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'Le téléphone est obligatoire'
    )]
    #[Assert\Length(
        min: 14,
        minMessage: 'Le téléphone doit comprendre au minimum {{ limit }} caractères de long',
    )]
    #[Assert\Regex(
        pattern: "/^(0)[1-9]( \d{2}){4}$/",
        match: false,
        message: 'Le téléphone n\'a pas le bon format. Caractères autorisés : chiffres et espaces'
    )]
    private ?string $phone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
}
