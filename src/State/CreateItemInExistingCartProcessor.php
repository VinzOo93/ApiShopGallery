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
        // to test
        try {
            $cart = $this->entityManager->getRepository(Cart::class)->findOneBy(['cart' => $data->cart]);
            $item = $this->createItemAction($cart, $data);
            $this->checkErrors($this->validator->validate($item));

            $this->entityManager->persist($item);

            $this->entityManager->flush();
            $this->entityManager->commit();

            return $this->persistProcessor->process($cart, $operation, $uriVariables, $context);
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, "impossible to create Item $e");
        }
    }
}
