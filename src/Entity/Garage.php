<?php

namespace App\Entity;

use App\Repository\GarageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

#[ORM\Entity(repositoryClass: GarageRepository::class)]
class Garage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $raison = null;

    #[ORM\Column(length: 16)]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $address1 = null;

    #[ORM\Column(length: 255)]
    private ?string $address2 = null;

    #[ORM\Column(length: 10)]
    private ?string $zip = null;

    #[ORM\Column(length: 255)]
    private ?string $locality = null;

    #[ORM\Column(length: 255)]
    private ?string $day1hours = null;

    #[ORM\Column(length: 255)]
    private ?string $day2hours = null;

    #[ORM\Column(length: 255)]
    private ?string $day3hours = null;

    #[ORM\Column(length: 255)]
    private ?string $day4hours = null;

    #[ORM\Column(length: 255)]
    private ?string $day5hours = null;

    #[ORM\Column(length: 255)]
    private ?string $day6hours = null;

    #[ORM\Column(length: 255)]
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
    public function getComments(): Collection
    {
        return $this->comments;
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
    public function getServices(): Collection
    {
        return $this->services;
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
