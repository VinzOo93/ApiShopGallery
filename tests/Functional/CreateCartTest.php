<?php

namespace App\Tests\Functional;

use App\Tests\Base\ShopTestBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CreateCartTest extends ShopTestBase
{
    /** @var array<string,mixed> */
    private array $cartWithItems = [
        'subtotal' => '800.00',
        'taxes' => '160.00',
        'shipping' => '5.00',
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
        'token' => 'U2FsdGVkX19zFZglY9uxbJgmze/rmb3d1Eu6gj224lg=',
    ];

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
        'token' => 'U2FsdGVkX19zFZglY9uxbJgmze/rmb3d1Eu6gj224lg=',
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
        'token' => 'U2FsdGVkX19zFZglY9uxbJgmze/rmb3d1Eu6gj224lg=',
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
        'token' => 'U2FsdGVkX19zFZglY9uxbJgmze/rmb3d1Eu6gj224lg=',
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
        'token' => 'U2FsdGVkX19zFZglY9uxbJgmze/rmb3d1Eu6gj224lg=',
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
        'token' => 'U2FsdGVkX19zFZglY9uxbJgmze/rmb3d1Eu6gj224lg=',
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
        'token' => 'U2FsdGVkX19zFZglY9uxbJgmze/rmb3d1Eu6gj224lg=',
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
        'token' => 'U2FsdGVkX19zFZglY9uxbJgmze/rmb3d1Eu6gj224lg=',
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
        'token' => 'U2FsdGVkX19zFZglY9uxbJgmze/rmb3d1Eu6gj224lg=',
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
        'token' => 'U2FsdGVkX19zFZglY9uxbJgmze/rmb3d1Eu6gj224lg=',
    ];

    /** @var array<string,mixed> */
    private array $cartWithCartToken = [
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
        'token' => 'chaussettes',
    ];

    private const ROUTE_CREATE_CART = '/carts';

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
    }

    /**
     * testCartCreation.
     *
     * @throws TransportExceptionInterface
     */
    private function testCartCreation(): void
    {
        $this->createItem($this->cartWithItems);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    /**
     * testTotalCartFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testTotalCartFailure(): void
    {
        $this->createItem($this->cartWithItemsTotal);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * testTaxesCartFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testTaxesCartFailure(): void
    {
        $this->createItem($this->cartWithItemsTaxes);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * testNegativeCartFieldFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testNegativeCartFieldFailure(): void
    {
        $this->createItem($this->cartWithItemsNegative);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * testQuantityItemFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testQuantityItemFailure(): void
    {
        $this->createItem($this->cartWithItemsQuantity);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * testPrintFormatItemFieldFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testPrintFormatItemFieldFailure(): void
    {
        $this->createItem($this->cartWithItemPrintFormat);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * testUnitPriceItemFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testUnitPriceItemFailure(): void
    {
        $this->createItem($this->cartWithItemUnitPrice);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * testUnitPriceItemFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testPreTaxPriceItemFailure(): void
    {
        $this->createItem($this->cartWithItemPreTaxPrice);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * testUnitPriceItemFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testTaxPriceItemFailure(): void
    {
        $this->createItem($this->cartWithItemTaxPrice);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * testUnitPriceItemFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testSubtotalCartFailure(): void
    {
        $this->createItem($this->cartWithCartSubtotal);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * testUnitPriceItemFailure.
     *
     * @throws TransportExceptionInterface
     */
    private function testTokenCartFailure(): void
    {
        $this->createItem($this->cartWithCartToken);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * testAuthCreateCart.
     */
    private function testAuthCreateCart(): void
    {
        $this->createCartWithItemNoAuth();
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * createItem.
     *
     * @param array<string,mixed> $cart
     *
     * @throws TransportExceptionInterface
     */
    private function createItem(array $cart): void
    {
        $response = $this->prepareUser(parent::ROUTE_AUTH);
        $this->postToApiWithAuthentication(
            $response->toArray(),
            $cart,
            parent::KEY_AUTH_TOKEN,
            self::ROUTE_CREATE_CART
        );
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
