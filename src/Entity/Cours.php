<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    /**
     * @Assert\NotBlank(message="Le contenu est obligatoire.")
     * @Assert\Length(
     *     max=500,
     *     maxMessage="Le contenu doit avoir au maximum 500 caractères."
     * )
     * @Assert\Regex(
     *     pattern="/^(http:\/\/|https:\/\/)?(public\/image\/)?.*$/",
     *     message="Le contenu doit être un texte ou une URL de vidéo dans 'public/image'."
     * )
     */
    private ?string $contenu = null;

/**
 * @var string|null
 * @Assert\File(
 *     maxSize = "10240k",
 *     mimeTypes = {"video/mp4", "video/avi", "video/mpeg", "video/quicktime"},
 *     mimeTypesMessage = "Veuillez télécharger une vidéo valide (MP4, AVI, MPEG, MOV)"
 * )
 */

 #[ORM\Column(length: 255, nullable: true)]
 private ?string $video = null;
 

    
    #[ORM\Column(length: 255)]
    /**
     * @Assert\NotBlank(message="Le titre est obligatoire.")
     * @Assert\Length(
     *     min=3,
     *     minMessage="Le titre doit avoir au moins 3 caractères."
     * )
     * @Assert\Regex(
     *     pattern="/^[A-Z][a-zA-Z0-9 ]*$/",
     *     message="Le titre doit commencer par une majuscule et ne contenir que des lettres, chiffres ou espaces."
     * )
     */
    private ?string $titre = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\Column(length: 255)]
    /**
     * @Assert\NotBlank(message="L'image est obligatoire.")
     * @Assert\Image(
     *     mimeTypes={"image/jpeg", "image/png"},
     *     mimeTypesMessage="Seuls les fichiers JPEG ou PNG sont acceptés."
     * )
     */
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    /**
     * @Assert\NotBlank(message="La catégorie est obligatoire.")
     */
    private ?Categorie $categorie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVideo(): ?string
{
    return $this->video;
}

public function setVideo(?string $video): static
{
    $this->video = $video;
    return $this;
}


    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
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

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }
}
