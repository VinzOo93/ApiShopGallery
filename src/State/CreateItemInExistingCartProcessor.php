<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Cart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CreateItemInExistingCartProcessor extends BaseShopProcessor implements ProcessorInterface
{
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Cart
    {
        try {
            $this->entityManager->beginTransaction();
            /** @var array $item */
            $item = $data->item;
            /** @var Cart $cart */
            $cart = !empty($data->cart) ? $data->cart : $this->createCartAction();

            $item = $this->createItemAction($item);
            $this->checkErrors($this->validator->validate($item));
            $this->entityManager->persist($item);
            $cart->addItem($item);
            $cart = $this->updateCart($cart);
            $this->checkErrors($this->validator->validate($cart));
            $this->entityManager->commit();

            return $this->persistProcessor->process($cart, $operation, $uriVariables, $context);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, "impossible to create Item $e");
        }
    }
}
