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

    protected function createItemAction(Cart $cart, array $itemData): Item|false
    {
        $item = new Item();
        $printFormat = $this->entityManager->getRepository(PrintFormat::class)->findOneBy(['name' => $itemData['printFormat']]);
        if (null === $printFormat) {
            $this->entityManager->rollback();
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'print Format not found');
        }
        $item->setQuantity($itemData['quantity'])
            ->setImage($itemData['image'])
            ->setPrintFormat($printFormat)
            ->setUnitPrice($itemData['unitPrice'])
            ->setUnitPreTaxPrice($itemData['unitPreTaxPrice'])
            ->setPreTaxPrice($itemData['preTaxPrice'])
            ->setTaxPrice($itemData['taxPrice'])
            ->setCart($cart);

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
