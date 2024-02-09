<?php

namespace App\Action;

use App\Entity\Cart;
use App\Entity\Item;
use App\Entity\PrintFormat;
use App\Repository\PrintFormatRepository;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * CreateCartWithItemsAction
 */
class CreateCartWithItemsAction
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * __invoke
     *
     * @param  Request $request
     * @return Cart
     */
    public function __invoke(Request $request): Cart
    {
        $this->entityManager->beginTransaction();
        try {
            $cartData = json_decode($request->getContent(), true);

            /** @var Cart */
            $cart = new Cart();

            /** @var DateTimeInterface */
            $date = new DateTime('NOW', new DateTimeZone('Europe/Paris'));

            /** @var PrintFormatRepository */
            $printFormatRepository = $this->entityManager->getRepository(PrintFormat::class);

            $cart->setSubtotal($cartData['subtotal'])
                ->setCreatedAt($date)
                ->setUpdatedAt($date)
                ->setTaxes($cartData['taxes'])
                ->setShipping($cartData['shipping'])
                ->setTotal($cartData['total']);

            $this->entityManager->persist($cart);
            $this->entityManager->flush();

            foreach ($cartData['items'] as $itemData) {
                /** @var Item */
                $item = new Item();

                /** @var PrintFormat */
                $printFormat = $printFormatRepository->findOneBy(['name' => $itemData['printFormat']]);

                $item->setQuantity($itemData['quantity'])
                    ->setImage($itemData['image'])
                    ->setPrintFormat($printFormat)
                    ->setUnitPrice($itemData['unitPrice'])
                    ->setUnitPreTaxPrice($itemData['unitPreTaxPrice'])
                    ->setPreTaxPrice($itemData['preTaxPrice'])
                    ->setTaxPrice($itemData['taxPrice'])
                    ->setCart($cart);

                $this->entityManager->persist($item);
            }
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $cart;
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw new HttpException(400, "impossible to create Cart $e");
        }
    }
}
