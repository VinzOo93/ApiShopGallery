<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Item;

class UpdateItemQuantityInExistingCartProcessor extends BaseShopProcessor implements ProcessorInterface
{
    /**
     * @throws \Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $item = $context['previous_data'];

        if (!$item instanceof Item) {
            return false;
        }
        $item = $this->entityManager->find(Item::class, $item->getId());
        $less = $data->less;
        $quantity = $item->getQuantity();
        $quantity = $less ? --$quantity : ++$quantity;

        $cart = $item->getCart();

        if (1 <= $quantity) {
            $item = $this->updateItemAction($item, $quantity);
            $this->entityManager->persist($item);
        } else {
            $this->entityManager->remove($item);
        }
        $cartUpdated = $this->updateCart($cart);
        $this->checkErrors($this->validator->validate($cart));

        return $this->persistProcessor->process($cartUpdated, $operation, $uriVariables, $context);
    }
}
