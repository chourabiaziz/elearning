<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
#[Assert\NotBlank(message: "Le nom de la catégorie ne doit pas être vide.")]
private ?string $nom = null;

    

    #[ORM\Column(length: 255)]
    
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    
    private ?\DateTimeInterface $dateCreated = null;

   
    #[ORM\Column(length: 255)]
   
    private ?string $image = null;
    

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Cours::class, cascade: ['remove'])]
    private Collection $cours;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'Categories')]
    private Collection $Users;

   

    #[ORM\OneToMany(targetEntity: CustomService::class, mappedBy: 'Categorie')]
    private Collection $customServices;

    public function __construct()
    {
        $this->Users = new ArrayCollection();
        $this->cours = new ArrayCollection();
        $this->customServices = new ArrayCollection();
        $this->dateCreated = new \DateTime(); // Initialise la date de création
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->Users;
    }

    public function addUser(User $User): static
    {
        if (!$this->Users->contains($User)) {
            $this->Users->add($User);
            $User->addCategory($this);
        }

        return $this;
    }

    public function removeUser(User $User): static
    {
        if ($this->Users->removeElement($User)) {
            $User->removeCategory($this);
        }

        return $this;
    }

    public function getCours(): Collection
    {
        return $this->cours;
    }
    

    public function addCour(Cours $cour): static
    {
        if (!$this->cours->contains($cour)) {
            $this->cours->add($cour);
            $cour->setCategorie($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): static
    {
        if ($this->cours->removeElement($cour)) {
            if ($cour->getCategorie() === $this) {
                $cour->setCategorie(null);
            }
        }

        return $this;
    }

    public function getCustomServices(): Collection
    {
        return $this->customServices;
    }

    public function addCustomService(CustomService $customService): static
    {
        if (!$this->customServices->contains($customService)) {
            $this->customServices->add($customService);
            $customService->setCategorie($this);
        }

        return $this;
    }

    public function removeCustomService(CustomService $customService): static
    {
        if ($this->customServices->removeElement($customService)) {
            if ($customService->getCategorie() === $this) {
                $customService->setCategorie(null);
            }
        }

        return $this;
    }
}
