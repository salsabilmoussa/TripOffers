<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OffreRepository::class)
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"voyage" = "Voyage", "omra" = "Omra", "croisiere" = "Croisiere", "excursions" = "Excursions"})
 */
class Offre
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
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $images = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $inclus;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nonInclus;

    /**
     * @ORM\ManyToOne(targetEntity=Destination::class)
     */
    private $destination;

   

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $agence;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class)
     */
    private $categorie;

    /**
     * @ORM\ManyToMany(targetEntity=BonAdresse::class)
     */
    private $bonadresses;

    /**
     * @ORM\OneToMany(targetEntity=GrilleTarifaire::class, mappedBy="offre", cascade={"persist"})
     */
    private $grilletarifaire;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="offre")
     */
    private $reservations;

  
   
    public function __construct()
    {
        $this->bonadresses = new ArrayCollection();
        $this->grilletarifaire = new ArrayCollection();
        $this->reservations = new ArrayCollection();
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
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

    public function getInclus(): ?string
    {
        return $this->inclus;
    }

    public function setInclus(string $inclus): self
    {
        $this->inclus = $inclus;

        return $this;
    }

    public function getNonInclus(): ?string
    {
        return $this->nonInclus;
    }

    public function setNonInclus(string $nonInclus): self
    {
        $this->nonInclus = $nonInclus;

        return $this;
    }

    public function getDestination(): ?Destination
    {
        return $this->destination;
    }

    public function setDestination(?Destination $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    
    public function getAgence(): ?Agence
    {
        return $this->agence;
    }

    public function setAgence(?Agence $agence): self
    {
        $this->agence = $agence;

        return $this;
    }

    public function getCategorie(): ?Category
    {
        return $this->categorie;
    }

    public function setCategorie(?Category $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, BonAdresse>
     */
    public function getBonadresses(): Collection
    {
        return $this->bonadresses;
    }

    public function addBonadress(BonAdresse $bonadress): self
    {
        if (!$this->bonadresses->contains($bonadress)) {
            $this->bonadresses[] = $bonadress;
        }

        return $this;
    }

    public function removeBonadress(BonAdresse $bonadress): self
    {
        $this->bonadresses->removeElement($bonadress);

        return $this;
    }

    /**
     * @return Collection<int, GrilleTarifaire>
     */
    public function getGrilletarifaire(): Collection
    {
        return $this->grilletarifaire;
    }

    public function addGrilletarifaire(GrilleTarifaire $grilletarifaire): self
    {
        if (!$this->grilletarifaire->contains($grilletarifaire)) {
            $this->grilletarifaire[] = $grilletarifaire;
            $grilletarifaire->setOffre($this);
        }

        return $this;
    }

    public function removeGrilletarifaire(GrilleTarifaire $grilletarifaire): self
    {
        if ($this->grilletarifaire->removeElement($grilletarifaire)) {
            // set the owning side to null (unless already changed)
            if ($grilletarifaire->getOffre() === $this) {
                $grilletarifaire->setOffre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setOffre($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getOffre() === $this) {
                $reservation->setOffre(null);
            }
        }

        return $this;
    }

   

   
}
