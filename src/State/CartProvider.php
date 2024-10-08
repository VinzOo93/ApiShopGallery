<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Cart;
use App\Repository\CartRepository;

final readonly class CartProvider implements ProviderInterface
{
    public function __construct(private CartRepository $cartRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Cart
    {
        return $this->cartRepository->findOneBy(['token' => $uriVariables['token'] ?? null]);
    }
}
