<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
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
    private $name;

    // /**
    //  * @ORM\OneToMany(targetEntity=BonAdresse::class, mappedBy="category")
    //  */
    // private $bonadresses;

    public function __construct()
    {
        // $this->bonadresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // /**
    //  * @return Collection<int, BonAdresse>
    //  */
    // public function getBonAdresses(): Collection
    // {
    //     return $this->bonadresses;
    // }

    // public function addBonAdresse(BonAdresse $bonadresse): self
    // {
    //     if (!$this->bonadresses->contains($bonadresse)) {
    //         $this->bonadresses[] = $bonadresse;
    //         $bonadresse->setCategory($this);
    //     }

    //     return $this;
    // }

    // public function removeBonAdresse(BonAdresse $bonadresse): self
    // {
    //     if ($this->bonadresses->removeElement($bonadresse)) {
    //         // set the owning side to null (unless already changed)
    //         if ($bonadresse->getCategory() === $this) {
    //             $bonadresse->setCategory(null);
    //         }
    //     }

    //     return $this;
    // }

    public function __toString()
    {
        return $this->name;
    }
}
