<?php

namespace App\Tests\Functional;

use App\Entity\Cart;
use App\Tests\Base\ShopTestBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CreateCartTest extends ShopTestBase
{
    /** @var array<string,mixed> */
    private array $cartWithItemsTotal = [
        'subtotal' => '800.00',
        'taxes' => '160.00',
        'shipping' => '0.00',
        'total' => '965.00',
        'items' => [
            [
                'quantity' => 2,
                'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
                'printFormat' => '30x20 cm',
                'unitPrice' => '480.00',
                'unitPreTaxPrice' => '400.00',
                'preTaxPrice' => '800.00',
                'taxPrice' => '960.00',
            ],
        ],
        'token' => 'U2FsdGVkX19zFZglY9uaxbJgmzermb3d1Eu6gj224lg=',
    ];

    /** @var array<string,mixed> */
    private array $cartWithItemsTaxes = [
        'subtotal' => '800.00',
        'taxes' => '160.00',
        'shipping' => '5.00',
        'total' => '1005.00',
        'items' => [
            [
                'quantity' => 2,
                'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
                'printFormat' => '30x20 cm',
                'unitPrice' => '480.00',
                'unitPreTaxPrice' => '400.00',
                'preTaxPrice' => '800.00',
                'taxPrice' => '960.00',
            ],
        ],
        'token' => 'U2FsdGVkX19zFZglY9uaxbJgmzermb3d1Eu6gj224lg=',
    ];

    /** @var array<string,mixed> */
    private array $cartWithItemsQuantity = [
        'subtotal' => '800.00',
        'taxes' => '160.00',
        'shipping' => '5.00',
        'total' => '1005.00',
        'items' => [
            [
                'quantity' => 0,
                'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
                'printFormat' => '30x20 cm',
                'unitPrice' => '480.00',
                'unitPreTaxPrice' => '400.00',
                'preTaxPrice' => '800.00',
                'taxPrice' => '960.00',
            ],
        ],
        'token' => 'U2FsdGVkX19zFZglY9uaxbJgmzermb3d1Eu6gj224lg=',
    ];

    /** @var array<string,mixed> */
    private array $cartWithItemsNegative = [
        'subtotal' => '-1.00',
        'taxes' => '160.00',
        'shipping' => '5.00',
        'total' => '1005.00',
        'items' => [
            [
                'quantity' => 0,
                'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
                'printFormat' => '30x20 cm',
                'unitPrice' => '480.00',
                'unitPreTaxPrice' => '400.00',
                'preTaxPrice' => '800.00',
                'taxPrice' => '960.00',
            ],
        ],
        'token' => 'U2FsdGVkX19zFZglY9uaxbJgmzermb3d1Eu6gj224lg=',
    ];

    /** @var array<string,mixed> */
    private array $cartWithItemPrintFormat = [
        'subtotal' => '800.00',
        'taxes' => '160.00',
        'shipping' => '5.00',
        'total' => '1005.00',
        'items' => [
            [
                'quantity' => 0,
                'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
                'printFormat' => 'bouuuubouuu',
                'unitPrice' => '480.00',
                'unitPreTaxPrice' => '400.00',
                'preTaxPrice' => '800.00',
                'taxPrice' => '960.00',
            ],
        ],
        'token' => 'U2FsdGVkX19zFZglY9uaxbJgmzermb3d1Eu6gj224lg=',
    ];

    /** @var array<string,mixed> */
    private array $cartWithItemUnitPrice = [
        'subtotal' => '800.00',
        'taxes' => '160.00',
        'shipping' => '5.00',
        'total' => '1005.00',
        'items' => [
            [
                'quantity' => 0,
                'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
                'printFormat' => '30x20 cm',
                'unitPrice' => '430.00',
                'unitPreTaxPrice' => '400.00',
                'preTaxPrice' => '800.00',
                'taxPrice' => '960.00',
            ],
        ],
        'token' => 'U2FsdGVkX19zFZglY9uaxbJgmzermb3d1Eu6gj224lg=',
    ];

    /** @var array<string,mixed> */
    private array $cartWithItemPreTaxPrice = [
        'subtotal' => '800.00',
        'taxes' => '160.00',
        'shipping' => '5.00',
        'total' => '1005.00',
        'items' => [
            [
                'quantity' => 0,
                'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
                'printFormat' => '30x20 cm',
                'unitPrice' => '480.00',
                'unitPreTaxPrice' => '400.00',
                'preTaxPrice' => '1000.00',
                'taxPrice' => '960.00',
            ],
        ],
        'token' => 'U2FsdGVkX19zFZglY9uaxbJgmzermb3d1Eu6gj224lg=',
    ];

    /** @var array<string,mixed> */
    private array $cartWithItemTaxPrice = [
        'subtotal' => '800.00',
        'taxes' => '160.00',
        'shipping' => '5.00',
        'total' => '1005.00',
        'items' => [
            [
                'quantity' => 0,
                'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
                'printFormat' => '30x20 cm',
                'unitPrice' => '480.00',
                'unitPreTaxPrice' => '400.00',
                'preTaxPrice' => '1000.00',
                'taxPrice' => '900.00',
            ],
        ],
        'token' => 'U2FsdGVkX19zFZglY9uaxbJgmzermb3d1Eu6gj224lg=',
    ];

    /** @var array<string,mixed> */
    private array $cartWithCartSubtotal = [
        'subtotal' => '700.00',
        'taxes' => '160.00',
        'shipping' => '5.00',
        'total' => '1005.00',
        'items' => [
            [
                'quantity' => 0,
                'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
                'printFormat' => '30x20 cm',
                'unitPrice' => '480.00',
                'unitPreTaxPrice' => '400.00',
                'preTaxPrice' => '1000.00',
                'taxPrice' => '900.00',
            ],
        ],
        'token' => 'U2FsdGVkX19zFZglY9uaxbJgmzermb3d1Eu6gj224lg=',
    ];

    /** @var array<string,mixed> */
    private array $cartWithCartToken = [
        'subtotal' => '900.00',
        'taxes' => '160.00',
        'shipping' => '5.00',
        'total' => '1005.00',
        'items' => [
            [
                'quantity' => 0,
                'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
                'printFormat' => '30x20 cm',
                'unitPrice' => '480.00',
                'unitPreTaxPrice' => '400.00',
                'preTaxPrice' => '1000.00',
                'taxPrice' => '900.00',
            ],
        ],
        'token' => 'chaussettes',
    ];


    /**
     * testCreateCartWithItem.
     *
     * @throws TransportExceptionInterface
     */
    public function testCreateCart(): void
    {
        $this->initShopTest();
        $this->testAuthCreateCart();

        $this->testTotalCartFailure();
        $this->testTaxesCartFailure();
        $this->testQuantityItemFailure();
        $this->testNegativeCartFieldFailure();
        $this->testPrintFormatItemFieldFailure();
        $this->testUnitPriceItemFailure();
        $this->testPreTaxPriceItemFailure();
        $this->testTaxPriceItemFailure();
        $this->testSubtotalCartFailure();
        $this->testTokenCartFailure();
        $this->testCartCreation();
        $this->testTokenDoubleCartFailure();
    }

    /**
     * testCartCreation.
     *
     * @throws TransportExceptionInterface
     */
    protected function testCartCreation(): void
    {
        $this->createOnDb($this->cartWithItems, self::ROUTE_CREATE_CART);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertEquals(1, $this->countObjectsOnDb(Cart::class));
    }

    /**
     * testTotalCartFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testTotalCartFailure(): void
    {
        $this->createOnDb($this->cartWithItemsTotal, self::ROUTE_CREATE_CART);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals(0, $this->countObjectsOnDb(Cart::class));
    }

    /**
     * testTaxesCartFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testTaxesCartFailure(): void
    {
        $this->createOnDb($this->cartWithItemsTaxes, self::ROUTE_CREATE_CART);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals(0, $this->countObjectsOnDb(Cart::class));
    }

    /**
     * testNegativeCartFieldFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testNegativeCartFieldFailure(): void
    {
        $this->createOnDb($this->cartWithItemsNegative, self::ROUTE_CREATE_CART);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals(0, $this->countObjectsOnDb(Cart::class));
    }

    /**
     * testQuantityItemFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testQuantityItemFailure(): void
    {
        $this->createOnDb($this->cartWithItemsQuantity, self::ROUTE_CREATE_CART);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals(0, $this->countObjectsOnDb(Cart::class));
    }

    /**
     * testPrintFormatItemFieldFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testPrintFormatItemFieldFailure(): void
    {
        $this->createOnDb($this->cartWithItemPrintFormat, self::ROUTE_CREATE_CART);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals(0, $this->countObjectsOnDb(Cart::class));
    }

    /**
     * testUnitPriceItemFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testUnitPriceItemFailure(): void
    {
        $this->createOnDb($this->cartWithItemUnitPrice, self::ROUTE_CREATE_CART);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals(0, $this->countObjectsOnDb(Cart::class));
    }

    /**
     * testUnitPriceItemFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testPreTaxPriceItemFailure(): void
    {
        $this->createOnDb($this->cartWithItemPreTaxPrice, self::ROUTE_CREATE_CART);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals(0, $this->countObjectsOnDb(Cart::class));
    }

    /**
     * testUnitPriceItemFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testTaxPriceItemFailure(): void
    {
        $this->createOnDb($this->cartWithItemTaxPrice, self::ROUTE_CREATE_CART);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals(0, $this->countObjectsOnDb(Cart::class));
    }

    /**
     * testUnitPriceItemFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testSubtotalCartFailure(): void
    {
        $this->createOnDb($this->cartWithCartSubtotal, self::ROUTE_CREATE_CART);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals(0, $this->countObjectsOnDb(Cart::class));
    }

    /**
     * testUnitPriceItemFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testTokenCartFailure(): void
    {
        $this->createOnDb($this->cartWithCartToken, self::ROUTE_CREATE_CART);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals(0, $this->countObjectsOnDb(Cart::class));
    }

    /**
     * testUnitPriceItemFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testTokenDoubleCartFailure(): void
    {
        $this->createOnDb($this->cartWithItems, self::ROUTE_CREATE_CART);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals(1, $this->countObjectsOnDb(Cart::class));
    }

    /**
     * testAuthCreateCart.
     */
    private function testAuthCreateCart(): void
    {
        $this->createCartWithItemNoAuth();
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(0, $this->countObjectsOnDb(Cart::class));
    }


    /**
     * createCartWithItemNoAuth.
     */
    private function createCartWithItemNoAuth(): void
    {
        $this->client->request(
            'POST',
            self::ROUTE_CREATE_CART,
            [
                'json' => json_encode($this->cartWithItems),
            ]
        );
    }
}
