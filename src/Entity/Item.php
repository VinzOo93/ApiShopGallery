<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;



    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PrintFormat $printFormat = null;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: Cart::class)]
    private Collection $cart;



    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $unitPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $unitPreTaxPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $preTaxPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $taxPrice = null;

    public function __construct()
    {
        $this->cart = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

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

    public function getPrintFormat(): ?PrintFormat
    {
        return $this->printFormat;
    }

    public function setPrintFormat(?PrintFormat $printFormat): self
    {
        $this->printFormat = $printFormat;

        return $this;
    }

    /**
     * @return Collection<int, Cart>
     */
    public function getCarts(): Collection
    {
        return $this->cart;
    }

    public function addCart(Cart $cart): self
    {
        if (!$this->cart->contains($cart)) {
            $this->cart->add($cart);
            $cart->setItem($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): self
    {
        if ($this->cart->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getItem() === $this) {
                $cart->setItem(null);
            }
        }

        return $this;
    }

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(string $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getUnitPreTaxPrice(): ?string
    {
        return $this->unitPreTaxPrice;
    }

    public function setUnitPreTaxPrice(string $unitPreTaxPrice): static
    {
        $this->unitPreTaxPrice = $unitPreTaxPrice;

        return $this;
    }

    public function getPreTaxPrice(): ?string
    {
        return $this->preTaxPrice;
    }

    public function setPreTaxPrice(string $preTaxPrice): static
    {
        $this->preTaxPrice = $preTaxPrice;

        return $this;
    }
    public function getTaxPrice(): ?string
    {
        return $this->taxPrice;
    }

    public function setTaxPrice(string $taxPrice): self
    {
        $this->taxPrice = $taxPrice;

        return $this;
    }
}
