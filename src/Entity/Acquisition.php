<?php

namespace App\Entity;

use App\Repository\AcquisitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AcquisitionRepository::class)]
#[ORM\Index(fields: ['date'])]
#[ORM\UniqueConstraint(fields: ['ref'])]
class Acquisition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64, nullable: false)]
    private ?string $ref = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $comment = null;

    /**
     * @var Collection<int, ItemAcquisition>
     */
    #[ORM\OneToMany(targetEntity: ItemAcquisition::class, mappedBy: 'acquisition')]
    private Collection $itemAcquisitions;

    public function __construct()
    {
        $this->itemAcquisitions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(?string $ref): static
    {
        $this->ref = $ref;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

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
     * @return Collection<int, ItemAcquisition>
     */
    public function getItemAcquisitions(): Collection
    {
        return $this->itemAcquisitions;
    }

    public function addItemAcquisition(ItemAcquisition $itemAcquisition): static
    {
        if (!$this->itemAcquisitions->contains($itemAcquisition)) {
            $this->itemAcquisitions->add($itemAcquisition);
            $itemAcquisition->setAcquisition($this);
        }

        return $this;
    }

    public function removeItemAcquisition(ItemAcquisition $itemAcquisition): static
    {
        if ($this->itemAcquisitions->removeElement($itemAcquisition)) {
            // set the owning side to null (unless already changed)
            if ($itemAcquisition->getAcquisition() === $this) {
                $itemAcquisition->setAcquisition(null);
            }
        }

        return $this;
    }
}
