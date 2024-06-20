<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use App\Entity\Cart;
use App\Entity\Item;
use App\Entity\PrintFormat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseShopProcessor
{
    private const float TAXE_RATE = 0.20;
    private const float SHIPPING = 5.00;

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        public ProcessorInterface $persistProcessor,
        #[Autowire(service: 'api_platform.doctrine.orm.state.remove_processor')]
        public ProcessorInterface $removeProcessor,
        public readonly EntityManagerInterface $entityManager,
        public readonly ValidatorInterface $validator,
    ) {
    }

    protected function checkErrors(ConstraintViolationListInterface $errors): void
    {
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->entityManager->rollback();
                throw new UnprocessableEntityHttpException($error->getMessage());
            }
        }
    }

    /**
     * @throws \Exception
     */
    protected function getCurrentDateTimeEurope(): \DateTimeInterface
    {
        return new \DateTime('NOW', new \DateTimeZone('Europe/Paris'));
    }

    protected function createItemAction(string $image, PrintFormat $printFormat): Item|false
    {
        $item = new Item();
        $quantity = 1;
        $unitPrice = $printFormat->getPreTaxPrice() * 1.2;
        $item->setQuantity(1)
            ->setImage($image)
            ->setPrintFormat($printFormat)
            ->setUnitPrice($unitPrice)
            ->setUnitPreTaxPrice($printFormat->getPreTaxPrice())
            ->setPreTaxPrice($printFormat->getPreTaxPrice() * $quantity)
            ->setTaxPrice($unitPrice * $quantity);

        return $item;
    }

    protected function updateItemAction(Item $item, int $quantity): Item
    {
        $unitPrice = $item->getUnitPreTaxPrice() * 1.2;
        $item->setQuantity($quantity)
        ->setPreTaxPrice($item->getUnitPreTaxPrice() * $quantity)
        ->setTaxPrice($unitPrice * $quantity);

        return $item;
    }

    /**
     * @throws \Exception
     */
    protected function createCartAction(): Cart
    {
        $cart = new Cart();
        $date = $this->getCurrentDateTimeEurope();
        $cart->setCreatedAt($date)
            ->setUpdatedAt($date)
            ->setSubtotal(0)
            ->setTaxes(0)
            ->setTotal(0)
            ->setShipping(self::SHIPPING)
            ->setToken($this->generateToken());

        return $cart;
    }

    /**
     * @throws \Exception
     */
    protected function updateCart(Cart $cart): Cart
    {
        $subtotal = 0;
        $taxes = 0;
        $total = 0;

        $cart->setUpdatedAt($this->getCurrentDateTimeEurope());

        foreach ($cart->getItems() as $item) {
            $pretaxPrice = $item->getPreTaxPrice();
            $subtotal += $pretaxPrice;
            $taxes += $pretaxPrice * self::TAXE_RATE;
        }
        $total += $subtotal + $taxes + $cart->getShipping();
        $cart->setSubtotal($subtotal);
        $cart->setTaxes($taxes);
        $cart->setTotal($total);

        return $cart;
    }

    protected function generateToken(): string
    {
        try {
            $bytes = random_bytes(33);

            $token = base64_encode($bytes);

            return str_replace(['+', '/', '='], ['-', '_', ''], $token);
        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to generate the token.');
        }
    }
}
