<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ApiFilter(SearchFilter::class, properties: ['ref' => 'start'])]
#[ApiFilter(ExistsFilter::class, properties: ['children', 'parent', 'stocktakeItemLocations'])]
#[ORM\Entity(repositoryClass: LocationRepository::class)]
#[ORM\UniqueConstraint(fields: ['ref'])]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $ref = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    private Collection $children;

    /**
     * @var Collection<int, StocktakeItemLocation>
     */
    #[ORM\OneToMany(targetEntity: StocktakeItemLocation::class, mappedBy: 'location')]
    private Collection $stocktakeItemLocations;

    public function __construct()
    {
        $this->children = new ArrayCollection();
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

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
            $stocktakeItemLocation->setLocation($this);
        }

        return $this;
    }

    public function removeStocktakeItemLocation(StocktakeItemLocation $stocktakeItemLocation): static
    {
        if ($this->stocktakeItemLocations->removeElement($stocktakeItemLocation)) {
            // set the owning side to null (unless already changed)
            if ($stocktakeItemLocation->getLocation() === $this) {
                $stocktakeItemLocation->setLocation(null);
            }
        }

        return $this;
    }
}
