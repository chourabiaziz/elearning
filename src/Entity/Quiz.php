<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "champ ne peut pas être vide")]
    private ?string $incorrect1 = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "champ ne peut pas être vide")]
    private ?string $incorrect2 = null;

    #[ORM\Column(length: 255)] 
    #[Assert\NotBlank(message: "champ ne peut pas être vide")]

    private ?string $correct = null;

    #[ORM\Column(nullable: true)]
    private ?bool $reponse = null;

    #[ORM\ManyToOne(inversedBy: 'quiz')]
    private ?Formation $formation = null;
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

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

    public function getIncorrect1(): ?string
    {
        return $this->incorrect1;
    }

    public function setIncorrect1(string $incorrect1): static
    {
        $this->incorrect1 = $incorrect1;

        return $this;
    }

    public function getIncorrect2(): ?string
    {
        return $this->incorrect2;
    }

    public function setIncorrect2(string $incorrect2): static
    {
        $this->incorrect2 = $incorrect2;

        return $this;
    }

    public function getCorrect(): ?string
    {
        return $this->correct;
    }

    public function setCorrect(string $correct): static
    {
        $this->correct = $correct;

        return $this;
    }

    public function isReponse(): ?bool
    {
        return $this->reponse;
    }

    public function setReponse(?bool $reponse): static
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }
}
