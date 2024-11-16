<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Dto\CreateItemDto;
use App\Dto\UpdateItemQuantityDto;
use App\Repository\ItemRepository;
use App\State\CreateItemInExistingCartProcessor;
use App\State\DeleteItemInExistingCartProcessor;
use App\State\UpdateItemQuantityInExistingCartProcessor;
use App\Validator as AcmeAssert;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource()]
#[Post(
    input: CreateItemDto::class,
    processor: CreateItemInExistingCartProcessor::class
)]
#[Patch(
    input: UpdateItemQuantityDto::class,
    processor: UpdateItemQuantityInExistingCartProcessor::class
)]
#[Delete(
    processor: DeleteItemInExistingCartProcessor::class
)]
#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[Groups(['cart:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    #[Groups(['cart:read'])]
    #[Assert\GreaterThanOrEqual(value: 1, message: 'La quantité doit être supérieur à 1.')]
    #[ORM\Column]
    private ?int $quantity = null;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Groups(['cart:read'])]
    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[Groups(['cart:read'])]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PrintFormat $printFormat = null;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    #[Groups(['cart:read'])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'La valeur doit être positive.')]
    #[AcmeAssert\Constraints\CartTaxes]
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $unitPrice = null;

    #[Groups(['cart:read'])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'La valeur doit être positive.')]
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $unitPreTaxPrice = null;

    #[Groups(['cart:read'])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'La valeur doit être positive.')]
    #[AcmeAssert\Constraints\CartTotal]
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $preTaxPrice = null;

    #[Groups(['cart:read'])]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'La valeur doit être positive.')]
    #[AcmeAssert\Constraints\CartTotal]
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $taxPrice = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cart $cart = null;

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

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): static
    {
        $this->cart = $cart;

        return $this;
    }
}
