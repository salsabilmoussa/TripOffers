<?php

namespace App\Entity;

use App\Repository\VoyageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VoyageRepository::class)
 */
class Voyage extends Offre
{
    /**
     * @ORM\ManyToMany(targetEntity=Excursion::class)
     */
    private $excursion;

    public function __construct()
    {
        parent::__construct();
        $this->excursion = new ArrayCollection();
    }

    /**
     * @return Collection<int, Excursion>
     */
    public function getExcursion(): Collection
    {
        return $this->excursion;
    }

    public function addExcursion(Excursion $excursion): self
    {
        if (!$this->excursion->contains($excursion)) {
            $this->excursion[] = $excursion;
        }

        return $this;
    }

    public function removeExcursion(Excursion $excursion): self
    {
        $this->excursion->removeElement($excursion);

        return $this;
    }
}
