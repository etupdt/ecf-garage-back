<?php

namespace App\Entity;

use App\Repository\GarageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GarageRepository::class)]
#[\AllowDynamicProperties]
class Garage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'La raison sociale est obligatoire'
    )]
    #[Assert\Length(
        min: 2,
        minMessage: 'La raison sociale doit faire au minimum {{ limit }} caractères de long',
    )]
    #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z -+*_='\/]*$/",
        match: true,
        message: 'raison sociale : Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $raison = null;

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

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'L\'adresse 1 est obligatoire'
    )]
    #[Assert\Length(
        min: 2,
        minMessage: 'L\'adresse 1 doit faire au minimum {{ limit }} caractères de long',
    )]
    #[Assert\Regex(
        pattern: "/[0-9a-zA-Z -+*_='\/]{0,}/",
        match: true,
        message: 'adresse : Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $address1 = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: "/[0-9a-zA-Z -+*_='\/]{0,}/",
        match: false,
        message: 'complément d\'adresse : Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $address2 = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(
        message: 'Le code postal est obligatoire'
    )]
    #[Assert\Length(
        min: 5,
        max: 5,
        minMessage: 'Le code postal doit faire au minimum {{ limit }} caractères de long',
        maxMessage: 'Le code postal doit faire au maximum {{ limit }} caractères de long',
    )]
    #[Assert\Regex(
        pattern: "/^[0-9]*$/",
        match: true,
        message: 'code postal : Caractères autorisés : chiffres'
    )]
    private ?string $zip = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'La localité est obligatoire'
    )]
    #[Assert\Length(
        min: 2,
        minMessage: 'La localité doit faire au minimum {{ limit }} caractères de long',
    )]
    #[Assert\Regex(
        pattern: "/[0-9a-zA-Z -+*_='\/]{0,}/",
        match: true,
        message: 'Localité : Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $locality = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: "/[0-9a-zA-Z -+*_='\/]{0,}/",
        match: true,
        message: 'jour 1 : Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $day1hours = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: "/[0-9a-zA-Z -+*_='\/]{0,}/",
        match: true,
        message: 'jour 2 : Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $day2hours = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: "/[0-9a-zA-Z -+*_='\/]{0,}/",
        match: true,
        message: 'jour 3 : Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $day3hours = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: "/[0-9a-zA-Z -+*_='\/]{0,}/",
        match: true,
        message: 'jour 4 : Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $day4hours = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: "/[0-9a-zA-Z -+*_='\/]{0,}/",
        match: true,
        message: 'jour 5 : Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $day5hours = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: "/[0-9a-zA-Z -+*_='\/]{0,}/",
        match: true,
        message: 'jour 6 : Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $day6hours = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: "/[0-9a-zA-Z -+*_='\/]{0,}/",
        match: true,
        message: 'jour 7 : Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $day7hours = null;

    #[ORM\OneToMany(mappedBy: 'garage', targetEntity: Contact::class, orphanRemoval: true)]
    private Collection $contacts;

    #[ORM\OneToMany(mappedBy: 'garage', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'garage', targetEntity: Car::class, orphanRemoval: true)]
    private Collection $cars;

    #[ORM\ManyToMany(targetEntity: Service::class)]
    private Collection $services;

    #[ORM\OneToMany(mappedBy: 'garage', targetEntity: User::class)]
    private Collection $users;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->cars = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaison(): ?string
    {
        return $this->raison;
    }

    public function setRaison(string $raison): self
    {
        $this->raison = $raison;

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

    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    public function setAddress1(string $address1): self
    {
        $this->address1 = $address1;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function getLocality(): ?string
    {
        return $this->locality;
    }

    public function setLocality(string $locality): self
    {
        $this->locality = $locality;

        return $this;
    }

    public function getDay1hours(): ?string
    {
        return $this->day1hours;
    }

    public function setDay1hours(string $day1hours): self
    {
        $this->day1hours = $day1hours;

        return $this;
    }

    public function getDay2hours(): ?string
    {
        return $this->day2hours;
    }

    public function setDay2hours(string $day2hours): self
    {
        $this->day2hours = $day2hours;

        return $this;
    }

    public function getDay3hours(): ?string
    {
        return $this->day3hours;
    }

    public function setDay3hours(string $day3hours): self
    {
        $this->day3hours = $day3hours;

        return $this;
    }

    public function getDay4hours(): ?string
    {
        return $this->day4hours;
    }

    public function setDay4hours(string $day4hours): self
    {
        $this->day4hours = $day4hours;

        return $this;
    }

    public function getDay5hours(): ?string
    {
        return $this->day5hours;
    }

    public function setDay5hours(string $day5hours): self
    {
        $this->day5hours = $day5hours;

        return $this;
    }

    public function getDay6hours(): ?string
    {
        return $this->day6hours;
    }

    public function setDay6hours(string $day6hours): self
    {
        $this->day6hours = $day6hours;

        return $this;
    }

    public function getDay7hours(): ?string
    {
        return $this->day7hours;
    }

    public function setDay7hours(string $day7hours): self
    {
        $this->day7hours = $day7hours;

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setGarage($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getGarage() === $this) {
                $contact->setGarage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments()
    {
        return $this->comments->getValues();
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setGarage($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getGarage() === $this) {
                $comment->setGarage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Car>
     */
    public function getCars(): Collection
    {
        return $this->cars;
    }

    public function addCar(Car $car): self
    {
        if (!$this->cars->contains($car)) {
            $this->cars->add($car);
            $car->setGarage($this);
        }

        return $this;
    }

    public function removeCar(Car $car): self
    {
        if ($this->cars->removeElement($car)) {
            // set the owning side to null (unless already changed)
            if ($car->getGarage() === $this) {
                $car->setGarage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices()
    {
        return $this->services->getValues();
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        $this->services->removeElement($service);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setGarage($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getGarage() === $this) {
                $user->setGarage(null);
            }
        }

        return $this;
    }

    public function __toString() 
    {

        $retour = "========================================> Garage :\n";

        foreach($this->getServices() as $service) {
            $retour = $retour."Service : ".$service->getId()." - ".$service->getName()."\n";
        }

        return $retour;

    }

}
