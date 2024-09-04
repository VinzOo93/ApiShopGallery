<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Item;

class DeleteItemInExistingCartProcessor extends BaseShopProcessor implements ProcessorInterface
{
    /**
     * @throws \Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $item = $data;
        if (!$item instanceof Item) {
            return false;
        }

        $cart = $item->getCart();
        $this->entityManager->remove($item);
        $this->entityManager->flush();

        $cartUpdated = $this->updateCart($cart);
        $this->checkErrors($this->validator->validate($cartUpdated));

        return $this->persistProcessor->process($cartUpdated, $operation, $uriVariables, $context);
    }
}
