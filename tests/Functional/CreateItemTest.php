<?php

namespace App\Tests\Functional;

use App\Entity\Cart;
use App\Entity\Item;
use App\Tests\Base\ShopTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateItemTest extends ShopTestBase
{
    public function testCreateItem(): void
    {
        $this->initShopTest();
        $this->testAuthCreateCart();
        $this->testCreatInExistingCart();
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

    private function testCreatInExistingCart(): void
    {
        $this->createCart();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertEquals(7, $this->countObjectsOnDb(Cart::class));
        $cartRepo = $this->entityManager->getRepository(Cart::class);
        $cart = $cartRepo->findOneBy([], ['id' => 'ASC']);
        $itemToBeCreatedWithCart = [
            'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11b',
            'printFormat' => '/print_formats/1',
            'cart' => '/carts/'.$cart->getToken(),
        ];

        $this->sendRequestToApi($itemToBeCreatedWithCart, self::ROUTE_ITEM, Request::METHOD_POST);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }
}
