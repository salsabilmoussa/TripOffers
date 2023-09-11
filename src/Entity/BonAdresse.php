<?php

namespace App\Entity;

use App\Repository\BonAdresseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BonAdresseRepository::class)
 */
class BonAdresse
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     */
    private $destination;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bonadresses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

   
    /**
     * @ORM\Column(type="string", length=255, nullable=true )
     */
    private $image;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $images;


    public function __construct()
    {
        // $this->destinations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    // public function getCategory(): ?Category
    // {
    //     return $this->category;
    // }

    // public function setCategory(?Category $category): self
    // {
    //     $this->category = $category;

    //     return $this;
    // }

    // /**
    //  * @return Collection<int, Destination>
    //  */
    // public function getDestinations(): Collection
    // {
    //     return $this->destinations;
    // }

    // public function addDestination(Destination $destination): self
    // {
    //     if (!$this->destinations->contains($destination)) {
    //         $this->destinations[] = $destination;
    //         $destination->setBonAdresse($this);
    //     }

    //     return $this;
    // }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }


    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): self
    {
        $this->images = $images;

        return $this;
    }

    // public function removeDestination(Destination $destination): self
    // {
    //     if ($this->destinations->removeElement($destination)) {
    //         // set the owning side to null (unless already changed)
    //         if ($destination->getBonAdresse() === $this) {
    //             $destination->setBonAdresse(null);
    //         }
    //     }

    //     return $this;
    // }

    public function __toString()
    {
        return $this->title;
    }
}
