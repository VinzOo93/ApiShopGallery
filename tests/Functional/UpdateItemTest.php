<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Cart;
use App\Entity\Item;
use App\Repository\CartRepository;
use App\Repository\ItemRepository;
use App\Tests\Base\ShopTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateItemTest extends ShopTestBase
{
    private CartRepository $cartRepository;
    private ItemRepository $itemRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->initShopTest();
        $this->createCart();
        $this->cartRepository = $this->getContainer()->get(CartRepository::class);
        $this->itemRepository = $this->getContainer()->get(ItemRepository::class);
    }

    public function testUpdateItemQuantity(): void
    {
        /** @var Cart $cart */
        $cart = $this->cartRepository->findOneBy([], ['id' => 'ASC']);
        /** @var Item $item */
        $items = $cart->getItems();
        $this->sendRequestToApi(['less' => false], self::ROUTE_ITEM.'/'.$items[0]->getId(), Request::METHOD_PATCH);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $item1 = $this->itemRepository->findOneBy(['cart' => $cart]);
        $this->assertEquals(2, $item1->getQuantity());

    }

}
