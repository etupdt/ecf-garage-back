<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(
        message: 'L\'adresse mail n\'est pas valide.',
    )]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

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

    #[ORM\Column(length: 16)]
    #[Assert\NotBlank(
        message: 'Le téléphone est obligatoire'
    )]
    #[Assert\Length(
        min: 14,
        minMessage: 'Le téléphone doit comprendre au minimum {{ limit }} caractères de long',
    )]
    #[Assert\Regex(
        pattern: "/^(0)[1-9]( \d{2}){4}$/",
        match: true,
        message: 'Le téléphone n\'a pas le bon format. Caractères autorisés : chiffres et espaces'
    )]
    private ?string $phone = null;

    #[ORM\ManyToOne(inversedBy: 'users', cascade: ['persist'])]
    private ?Garage $garage = null;

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    /**
     * Méthode getUsername qui permet de retourner le champ qui est utilisé pour l'authentification.
     *
     * @return string
     */
    public function getUsername(): string {
        return $this->getUserIdentifier();
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

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
