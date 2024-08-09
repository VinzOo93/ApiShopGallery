<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Cart;
use App\Entity\Item;
use App\Entity\PrintFormat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CreateItemInExistingCartProcessor extends BaseShopProcessor implements ProcessorInterface
{
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Cart|false
    {
        try {
            /** @var PrintFormat $printFormat */
            $printFormat = $data->printFormat;
            $image = $data->image;
            if (null === $image && null === $printFormat) {
                $cart = $this->createCartAction();
                return $this->persistProcessor->process($cart, $operation, $uriVariables, $context);
            }

            $itemRepository = $this->entityManager->getRepository(Item::class);

            /* @var Cart $cart */
            $cart = !empty($data->cart) ? $data->cart : $this->createCartAction();
            $existantItem = $itemRepository->findOneBy(
                [
                    'cart' => $cart,
                    'image' => $image,
                    'printFormat' => $printFormat,
                ]);

            $this->entityManager->beginTransaction();

            $existantItem = !$existantItem ?
                $this->createItemAction($image, $printFormat) :
                $this->updateItemAction($existantItem, $existantItem->getQuantity() + 1);

            $cart->addItem($existantItem);
            $cart = $this->updateCart($cart);
            $this->checkErrors($this->validator->validate($cart));
            $this->entityManager->persist($cart);
            $this->entityManager->commit();

            return $this->persistProcessor->process($cart, $operation, $uriVariables, $context);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, "impossible to create Item $e");
        }
    }
}
