<?php

namespace App\Tests\Functional;

use App\Entity\Cart;
use App\Entity\Item;
use App\Tests\Base\ShopTestBase;
use Symfony\Component\HttpFoundation\Response;

class CreateItemTest extends ShopTestBase
{
    public function testCreateItem(): void
    {
        $this->initShopTest();
        $this->testAuthCreateCart();
        $this->testCreateCart();
        $this->testCreatInExistingCartCart();
    }

    /**
     * createCartWithItemNoAuth.
     */
    private function testAuthCreateCart(): void
    {
        $this->createObjectWithNoAuth(self::ROUTE_ITEM, $this->itemToBeCreated);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(0, $this->countObjectsOnDb(Item::class));
    }

    private function testCreateCart(): void
    {
        $this->createCart();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertEquals(1, $this->countObjectsOnDb(Cart::class));
    }


    private function testCreatInExistingCartCart(): void
    {
        $data = $this->createItemDto($this->itemToBeCreated);
        $this->createOnDb([$data], self::ROUTE_ITEM);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertEquals(2, $this->countObjectsOnDb(Item::class));
    }

}
