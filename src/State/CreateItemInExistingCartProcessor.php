<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Cart;
use App\Entity\Item;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CreateItemInExistingCartProcessor extends BaseShopProcessor implements ProcessorInterface
{
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Item
    {
        try {
            /** @var Cart $cart */
            $cart = $data->cart;
            $item = $this->createItemAction($cart, $data);
            $this->checkErrors($this->validator->validate($item));

            return $this->persistProcessor->process($item, $operation, $uriVariables, $context);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, "impossible to create Item $e");
        }
    }
}
