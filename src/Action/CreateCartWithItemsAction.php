<?php

namespace App\Action;

use App\Dto\CreateCartDto;
use App\Dto\CreateItemWithCartDto;
use App\Entity\Cart;
use App\Entity\Item;
use App\Entity\PrintFormat;
use App\Repository\PrintFormatRepository;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * CreateCartWithItemsAction
 */
class CreateCartWithItemsAction
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer,
        private string $content  = ''
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->content = $content;
    }

    /**
     * __invoke
     *
     * @param  Request $request
     * @return Cart
     */
    public function __invoke(Request $request): Cart
    {
        $this->content = $request->getContent();

        $this->validateCart($this->content);
        $this->validateItem($this->content);

        $this->entityManager->beginTransaction();
        try {
            $cartData = json_decode($this->content, true);

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

    /**
     * validateCart
     *
     * @return void
     */
    private function validateCart()
    {
        $cartDto = $this->serializer->deserialize($this->content, CreateCartDto::class, 'json');
        $errors = $this->validator->validate($cartDto);
        $this->getErrors($errors);
    }

    /**
     * validateItem
     *
     * @return void
     */
    private function validateItem()
    {
        $itemDto = $this->serializer->deserialize($this->content, CreateItemWithCartDto::class, 'json');
        $errors = $this->validator->validate($itemDto);
        $this->getErrors($errors);
    }

    /**
     * getErrors
     *
     * @param  mixed $errors
     * @return void
     */
    private function getErrors(ConstraintViolationListInterface $errors)
    {
        if (!empty($errors)) {
            return new Response((string) $errors, 400);
        }
    }
}
