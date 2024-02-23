<?php

namespace App\Action;

use App\Dto\CreateCartDto;
use App\Dto\CreateItemWithCartDto;
use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * CreateCartWithItemsAction.
 */
class CreateCartWithItemsAction extends BaseShopAction
{
    private const OBJECTS_DTO = [
        CreateCartDto::class,
        CreateItemWithCartDto::class,
    ];

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        string $content = '')
    {
        parent::__construct($entityManager, $validator, $serializer, $content);
    }

    /**
     * __invoke.
     */
    public function __invoke(Request $request): Cart
    {
        $this->content = $request->getContent();

        $this->initValidationAction(self::OBJECTS_DTO);

        $this->entityManager->beginTransaction();
        try {
            $cartData = json_decode($this->content, true);

            $cart = new Cart();

            $date = $this->getCurrentDateTimeEurope();

            $cart->setSubtotal($cartData['subtotal'])
                ->setCreatedAt($date)
                ->setUpdatedAt($date)
                ->setTaxes($cartData['taxes'])
                ->setShipping($cartData['shipping'])
                ->setTotal($cartData['total'])
                ->setToken($cartData['token']);

            foreach ($cartData['items'] as $itemData) {
                $item = $this->createItemAction($cart, $itemData);
                $this->entityManager->persist($item);
                $cart->addItem($item);
            }
            $this->checkErrors($this->validator->validate($cart));
            $this->entityManager->persist($cart);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $cart;
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, "impossible to create Cart $e");
        }
    }
}
