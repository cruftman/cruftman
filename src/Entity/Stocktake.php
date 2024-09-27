<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\StocktakeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ORM\Entity(repositoryClass: StocktakeRepository::class)]
class Stocktake
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $ref = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $comment = null;

    /**
     * @var Collection<int, StocktakeItemLocation>
     */
    #[ORM\OneToMany(targetEntity: StocktakeItemLocation::class, mappedBy: 'stocktake')]
    private Collection $stocktakeItemLocations;

    public function __construct()
    {
        $this->stocktakeItemLocations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): static
    {
        $this->ref = $ref;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return Collection<int, StocktakeItemLocation>
     */
    public function getStocktakeItemLocations(): Collection
    {
        return $this->stocktakeItemLocations;
    }

    public function addStocktakeItemLocation(StocktakeItemLocation $stocktakeItemLocation): static
    {
        if (!$this->stocktakeItemLocations->contains($stocktakeItemLocation)) {
            $this->stocktakeItemLocations->add($stocktakeItemLocation);
            $stocktakeItemLocation->setStocktake($this);
        }

        return $this;
    }

    public function removeStocktakeItemLocation(StocktakeItemLocation $stocktakeItemLocation): static
    {
        if ($this->stocktakeItemLocations->removeElement($stocktakeItemLocation)) {
            // set the owning side to null (unless already changed)
            if ($stocktakeItemLocation->getStocktake() === $this) {
                $stocktakeItemLocation->setStocktake(null);
            }
        }

        return $this;
    }
}
