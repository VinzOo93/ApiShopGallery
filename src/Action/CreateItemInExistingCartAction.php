<?php

namespace App\Action;

use App\Dto\CreateItemDto;
use App\Entity\Cart;
use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateItemInExistingCartAction extends BaseShopAction
{
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        string $content = '')
    {
        parent::__construct($entityManager, $validator, $serializer, $content);
    }

    public function __invoke(Request $request): Item
    {
        $this->content = $request->getContent();

        $this->entityManager->beginTransaction();
        $this->validateObject(CreateItemDto::class);


        try {
            $itemData = json_decode($this->content, true);

            $cart = $this->getObjectDatabase($itemData,
                Cart::class,
                [
                    'key' => 'id',
                    'field' => 'cart',
                ]
            );
            $item = $this->createItemAction($cart, $itemData);
            $this->checkErrors($this->validator->validate($item));

            $this->entityManager->persist($item);

            $this->entityManager->flush();
            $this->entityManager->commit();

            return $item;
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, "impossible to create Item $e");
        }
    }
}
