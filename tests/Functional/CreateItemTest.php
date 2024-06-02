<?php

namespace App\Tests\Functional;

use App\Entity\Cart;
use App\Entity\Item;
use App\Tests\Base\ShopTestBase;
use Symfony\Component\HttpFoundation\Response;

class CreateItemTest extends ShopTestBase
{
    private const string ROUTE_CREATE_ITEM = '/items';

    /** @var array<string,mixed> */
    private array $itemToBeCreated = [
        'quantity' => 2,
        'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
        'printFormat' => '30x20 cm',
        'unitPrice' => '480.00',
        'unitPreTaxPrice' => '400.00',
        'preTaxPrice' => '800.00',
        'taxPrice' => '960.00',
    ];

    /** @var array<string,mixed> */
    private array $itemToBeCreatedPrintFormat = [
        'quantity' => 2,
        'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
        'printFormat' => 'dfg cm',
        'unitPrice' => '480.00',
        'unitPreTaxPrice' => '400.00',
        'preTaxPrice' => '800.00',
        'taxPrice' => '960.00',
        'cart' => 1,
    ];

    public function testCreateItem(): void
    {
        $this->initShopTest();

        $this->testAuthCreateCart();
        $this->createCart();
        $this->testCreateItemPrintFormatFailure();
        $this->testCreatInExistingCartCart();
    }

    /**
     * createCartWithItemNoAuth.
     */
    private function testAuthCreateCart(): void
    {
        $this->createObjectWithNoAuth(self::ROUTE_CREATE_ITEM, $this->itemToBeCreated);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(0, $this->countObjectsOnDb(Item::class));
    }

    private function createCart(): void
    {
        $this->createOnDb($this->cartWithItems, self::ROUTE_CREATE_CART);
        $this->assertEquals(1, $this->countObjectsOnDb(Cart::class));
    }

    private function testCreateItemPrintFormatFailure()
    {
        $this->createOnDb($this->itemToBeCreatedPrintFormat, self::ROUTE_CREATE_CART);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals(1, $this->countObjectsOnDb(Cart::class));
    }

    private function testCreatInExistingCartCart(): void
    {
        $this->createOnDb($this->itemToBeCreated, self::ROUTE_CREATE_ITEM);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertEquals(2, $this->countObjectsOnDb(Item::class));
    }
}
