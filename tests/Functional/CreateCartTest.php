<?php

namespace App\Tests\Shop;

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
                'unitPrice' => '450.00',
                'unitPreTaxPrice' => '400.00',
                'preTaxPrice' => '800.00',
                'taxPrice' => '960.00',
            ],
        ],
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
    ];

    private const ROUTE_CREATE_CART = '/carts';

    /**
     * testCreateCartWithItem.
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
        // $this->testUnitPriceItemFailure();
        $this->testCartCreation();
    }

    /**
     * testCartCreation.
     * @throws TransportExceptionInterface
     */
    private function testCartCreation(): void
    {
        $this->createItem($this->cartWithItems);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    /**
     * testTotalCartFailure.
     * @throws TransportExceptionInterface
     */
    private function testTotalCartFailure(): void
    {
        $this->createItem($this->cartWithItemsTotal);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * testTaxesCartFailure.
     * @throws TransportExceptionInterface
     */
    private function testTaxesCartFailure(): void
    {
        $this->createItem($this->cartWithItemsTaxes);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * testNegativeCartFieldFailure.
     * @throws TransportExceptionInterface
     */
    private function testNegativeCartFieldFailure(): void
    {
        $this->createItem($this->cartWithItemsNegative);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * testQuantityItemFailure.
     */
    private function testQuantityItemFailure(): void
    {
        $this->createItem($this->cartWithItemsQuantity);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * testPrintFormatItemFieldFailure.
     */
    private function testPrintFormatItemFieldFailure(): void
    {
        $this->createItem($this->cartWithItemPrintFormat);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    // /**
    //  * testUnitPriceItemFailure
    //  *
    //  * @return void
    //  */
    // private function testUnitPriceItemFailure(): void
    // {
    //     $this->createItem($this->cartWithItemUnitPrice);
    //     $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    // }

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
