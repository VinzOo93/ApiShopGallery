<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Cart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CreateCartWithItemsProcessor extends BaseShopProcessor implements ProcessorInterface
{
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Cart
    {

        try {
            $cart = new Cart();

            $date = $this->getCurrentDateTimeEurope();

            $cart->setSubtotal($data->subtotal)
                ->setCreatedAt($date)
                ->setUpdatedAt($date)
                ->setTaxes($data->taxes)
                ->setShipping($data->shipping)
                ->setTotal($data->total)
                ->setToken($this->generateToken());

            foreach ($data->items as $itemData) {
                $item = $this->createItemAction($cart, $itemData);
                $cart->addItem($item);
            }
            $this->checkErrors($this->validator->validate($cart));

            return $this->persistProcessor->process($cart, $operation, $uriVariables, $context);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, "impossible to create Cart $e");
        }
    }

    private function generateToken(): string
    {
        try {
            $bytes = random_bytes(33);

            // Convertir en base64
            $token = base64_encode($bytes);

            return str_replace(['+', '/', '='], ['-', '_', ''], $token);
        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to generate the token.');
        }
    }
}
