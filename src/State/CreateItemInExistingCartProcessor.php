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
            $this->entityManager->beginTransaction();
            /** @var array $item */
            $item = $data->item;

            if (empty($item)) {
                return false;
            }

            $itemRepository = $this->entityManager->getRepository(Item::class);
            $printFormatRepository = $this->entityManager->getRepository(PrintFormat::class);

            /* @var Cart $cart */
            $cart = !empty($data->cart) ? $data->cart : $this->createCartAction();

            $existantItem = $itemRepository->findOneBy(
                [
                    'cart' => $cart,
                    'image' => $item['image'],
                    'printFormat' => $printFormatRepository->findOneBy(['name' => $item['printFormat']]),
                ]);
            $existantItem = !$existantItem ?
                $this->createItemAction($item) :
                $this->updateItemAction($existantItem, $item);

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
