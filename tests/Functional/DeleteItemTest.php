<?php

namespace App\Tests\Functional;

use App\Entity\Cart;
use App\Entity\Item;
use App\Repository\CartRepository;
use App\Repository\ItemRepository;
use App\Tests\Base\ShopTestBase;
use Symfony\Component\HttpFoundation\Request;

class DeleteItemTest extends ShopTestBase
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

    public function testDeleteItem()
    {
        /** @var Cart $cart */
        $cart = $this->cartRepository->findOneBy([], ['id' => 'DESC']);
        /** @var Item $item */
        $items = $cart->getItems();
        $this->sendRequestToApi([], self::ROUTE_ITEM.'/'.$items[0]->getId(), Request::METHOD_DELETE);
        $this->assertResponseIsSuccessful();
        $itemDeleted = $this->itemRepository->find($items[0]);
        $this->assertNull($itemDeleted);
    }
}
