<?php

namespace App\Entity;

use App\Repository\ItemAcquisitionRepository;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

#[ORM\Entity(repositoryClass: ItemAcquisitionRepository::class)]
class ItemAcquisition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'itemAcquisition', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Item $item = null;

    #[ORM\ManyToOne(inversedBy: 'itemAcquisitions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Acquisition $acquisition = null;

    #[ORM\Column(type: 'money', nullable: true)]
    private ?Money $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(Item $item): static
    {
        $this->item = $item;

        return $this;
    }

    public function getAcquisition(): ?Acquisition
    {
        return $this->acquisition;
    }

    public function setAcquisition(?Acquisition $acquisition): static
    {
        $this->acquisition = $acquisition;

        return $this;
    }

    public function getPrice(): ?Money
    {
        return $this->price;
    }

    public function setPrice(?Money $price): static
    {
        $this->price = $price;

        return $this;
    }
}
