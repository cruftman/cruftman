<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ApiFilter(SearchFilter::class, properties: [ 'ref' => 'start' ])]
#[ApiFilter(ExistsFilter::class, properties: [ 'itemAcquisition' ])]
#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ORM\UniqueConstraint(fields: ["ref"])]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $ref = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ItemModel $model = null;

    #[ORM\OneToOne(mappedBy: 'item', cascade: ['persist', 'remove'])]
    private ?ItemAcquisition $itemAcquisition = null;

    /**
     * @var Collection<int, StocktakeItemLocation>
     */
    #[ORM\OneToMany(targetEntity: StocktakeItemLocation::class, mappedBy: 'item')]
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getModel(): ?ItemModel
    {
        return $this->model;
    }

    public function setModel(?ItemModel $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getItemAcquisition(): ?ItemAcquisition
    {
        return $this->itemAcquisition;
    }

    public function setItemAcquisition(ItemAcquisition $itemAcquisition): static
    {
        // set the owning side of the relation if necessary
        if ($itemAcquisition->getItem() !== $this) {
            $itemAcquisition->setItem($this);
        }

        $this->itemAcquisition = $itemAcquisition;

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
            $stocktakeItemLocation->setItem($this);
        }

        return $this;
    }

    public function removeStocktakeItemLocation(StocktakeItemLocation $stocktakeItemLocation): static
    {
        if ($this->stocktakeItemLocations->removeElement($stocktakeItemLocation)) {
            // set the owning side to null (unless already changed)
            if ($stocktakeItemLocation->getItem() === $this) {
                $stocktakeItemLocation->setItem(null);
            }
        }

        return $this;
    }
}
