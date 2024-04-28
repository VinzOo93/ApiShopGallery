<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use App\Dto\CreateItemDto;
use App\Entity\Cart;
use App\Entity\Item;
use App\Entity\PrintFormat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseShopProcessor
{
    private const float TAXE_RATE = 0.20;

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

    protected function createItemAction(Cart $cart, CreateItemDto $itemData): Item
    {
        $item = new Item();
        $printFormat = $this->entityManager->getRepository(PrintFormat::class)->findOneBy(['name' => $itemData->printFormat]);
        $item->setQuantity($itemData->quantity)
            ->setImage($itemData->image)
            ->setPrintFormat($printFormat)
            ->setUnitPrice($itemData->unitPrice)
            ->setUnitPreTaxPrice($itemData->unitPreTaxPrice)
            ->setPreTaxPrice($itemData->preTaxPrice)
            ->setTaxPrice($itemData->taxPrice)
            ->setCart($cart);

        return $item;
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
            dump($item);
            $pretaxPrice = $item->getPreTaxPrice() * $item->getQuantity();
            $subtotal += $pretaxPrice;
            $taxes += $pretaxPrice * self::TAXE_RATE;
        }
        $total += $subtotal + $taxes + $cart->getShipping();
        dump($total, $subtotal);

        $cart->setSubtotal($subtotal);
        $cart->setTaxes($taxes);
        $cart->setTotal($total);

        return $cart;
    }
}
