<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Action\CreateCartWithItemsAction;
use App\Dto\CreateCartDto;
use App\Repository\CartRepository;
use App\State\CartProvider;
use App\Validator as AcmeAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource]
#[Get(
    uriTemplate: '/carts/{token}',
    uriVariables: [
        'token' => new Link(
            fromProperty: 'token',
            toProperty: 'token',
            fromClass: Cart::class,
        )],
    provider: CartProvider::class,
)]
#[Post(
    controller: CreateCartWithItemsAction::class,
    input: CreateCartDto::class
)]
#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ApiProperty(identifier: false)]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[Assert\GreaterThanOrEqual(value: 0, message: 'La valeur doit être positive.')]
    #[AcmeAssert\Constraints\CartTotal]
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $subtotal = null;

    #[Assert\GreaterThanOrEqual(value: 0, message: 'La valeur doit être positive.')]
    #[AcmeAssert\Constraints\CartTaxes]
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $taxes = null;

    #[Assert\GreaterThanOrEqual(value: 0, message: 'La valeur doit être positive.')]
    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    private ?string $shipping = null;

    #[Assert\GreaterThanOrEqual(value: 0, message: 'La valeur doit être positive.')]
    #[AcmeAssert\Constraints\CartTotal]
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $total = null;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: Item::class, orphanRemoval: true)]
    private Collection $items;

    #[Assert\Length(
        min: 44,
        max: 50,
        exactMessage: 'La chaîne doit avoir exactement 44 caractères.'
    )]
    #[ApiProperty(identifier: true)]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $token = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getSubtotal(): ?string
    {
        return $this->subtotal;
    }

    public function setSubtotal(string $subtotal): self
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function getTaxes(): ?string
    {
        return $this->taxes;
    }

    public function setTaxes(string $taxes): self
    {
        $this->taxes = $taxes;

        return $this;
    }

    public function getShipping(): ?string
    {
        return $this->shipping;
    }

    public function setShipping(string $shipping): self
    {
        $this->shipping = $shipping;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getItems(): mixed
    {
        return $this->items->getValues();
    }

    public function addItem(Item $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setCart($this);
        }

        return $this;
    }

    public function removeItem(Item $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getCart() === $this) {
                $item->setCart(null);
            }
        }

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }
}
