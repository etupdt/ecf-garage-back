<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Date;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Positive(
        message: 'Le prix doit être supérieur à zéro'
    )]
    private ?float $price = null;

    #[ORM\Column]
    #[Assert\GreaterThan(
        value: 1950,
        message: 'L\'année doit être supérieure à 1950'
    )]
/*    #[Assert\Expression(
        "value <= date('Y')",
        message: 'L\'année doit être inférieure ou égale à l\'année en cours'
    )]*/
    private ?int $year = null;

    #[ORM\Column]
    #[Assert\Positive(
        message: 'Le prix doit être supérieur à zéro'
    )]
    private ?int $kilometer = null;

    #[ORM\ManyToMany(targetEntity: Option::class, inversedBy: 'features', cascade: ['persist'])]
    private Collection $options;

    #[ORM\OneToMany(mappedBy: 'car', targetEntity: Feature::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $features;

    #[ORM\ManyToOne(inversedBy: 'cars', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Garage $garage = null;

    #[ORM\OneToOne(cascade: ['persist'])]
    #[Assert\NotBlank(
        message: 'L\'image principale est obligatoire'
    )]
    private ?Image $image = null;

    #[ORM\OneToMany(mappedBy: 'car', targetEntity: Image::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $images;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'La marque doit être renseignée'
    )]
    #[Assert\Length(
        min: 2,
        minMessage: 'La marque doit faire au minimum {{ limit }} caractères de long',
    )]
    #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z -\']*$/",
        match: true,
        message: 'marque : Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $brand = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'Le modèle doit être renseigné'
    )]
    #[Assert\Length(
        min: 2,
        minMessage: 'Le modèle doit faire au minimum {{ limit }} caractères de long',
    )]
    #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z -\']*$/",
        match: true,
        message: 'modèle : Caractères autorisés : lettres, chiffres, tirets, signes et underscore'
    )]
    private ?string $model = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->features = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getKilometer(): ?int
    {
        return $this->kilometer;
    }

    public function setKilometer(int $kilometer): self
    {
        $this->kilometer = $kilometer;

        return $this;
    }

    /**
     * @return Collection<int, Option>
     */
    public function getOptions()
    {
        return $this->options->getValues();
    }

    public function addOption(Option $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
        }

        return $this;
    }

    public function removeOption(Option $option): self
    {
        $this->options->removeElement($option);

        return $this;
    }

    /**
     * @return Collection<int, Feature>
     */
    public function getFeatures()
    {
        return $this->features->getValues();
    }

    public function addFeature(Feature $feature): self
    {
        if (!$this->features->contains($feature)) {
            $this->features->add($feature);
            $feature->setCar($this);
        }

        return $this;
    }

    public function removeFeature(Feature $feature): self
    {
        if ($this->features->removeElement($feature)) {
            // set the owning side to null (unless already changed)
            if ($feature->getCar() === $this) {
                $feature->setCar(null);
            }
        }

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

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages()
    {
        return $this->images->getValues();
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setCar($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getCar() === $this) {
                $image->setCar(null);
            }
        }

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

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
