<?php

namespace App\Tests\Shop;

use App\Tests\Base\ShopTestBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CreateCartTest extends ShopTestBase
{
    /** @var array<string,mixed>*/
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
                'unitPrice' => '500.00',
                'unitPreTaxPrice' => '400.00',
                'preTaxPrice' => '800.00',
                'taxPrice' => '200.00',
            ]
        ]
    ];

    /** @var array<string,mixed>*/
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
                'unitPrice' => '500.00',
                'unitPreTaxPrice' => '400.00',
                'preTaxPrice' => '800.00',
                'taxPrice' => '200.00',
            ]
        ]
    ];

    /** @var array<string,mixed>*/
    private array $cartWithItemsTaxes = [
        'subtotal' => '800.00',
        'taxes' => '200.00',
        'shipping' => '5.00',
        'total' => '1005.00',
        'items' => [
            [
                'quantity' => 2,
                'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
                'printFormat' => '30x20 cm',
                'unitPrice' => '500.00',
                'unitPreTaxPrice' => '400.00',
                'preTaxPrice' => '800.00',
                'taxPrice' => '200.00',
            ]
        ]
    ];

    private const ROUTE_CREATE_CART = '/carts';

    /**
     * testCreateCartWithItem
     *
     * @return void
     */
    public function testCreateCart(): void
    {
        $this->initShopTest();
        $this->testAuthCreateCart();

        $this->testTotalCartFailure();
        $this->testTaxesCartFailure();
        $this->testCartCreation();
    }

    /**
     * testCartCreation
     *
     * @return void
     */
    private function testCartCreation(): void
    {
        $this->createItem($this->cartWithItems);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    /**
     * testTotalCartFailure
     *
     * @return void
     */
    private function testTotalCartFailure(): void
    {
        $this->createItem($this->cartWithItemsTotal);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * testTaxesCartFailure
     *
     * @return void
     */
    private function testTaxesCartFailure(): void
    {
        $this->createItem($this->cartWithItemsTaxes);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * testAuthCreateCart
     *
     * @return void
     */
    private function testAuthCreateCart(): void
    {
        $this->createCartWithItemNoAuth();
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }



    /**
     * createItem
     *
     * @param  array<string,mixed> $cart
     * @return void
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
     * createCartWithItemNoAuth
     *
     * @return ResponseInterface
     */
    private function createCartWithItemNoAuth(): ResponseInterface
    {
        return $this->client->request(
            'POST',
            self::ROUTE_CREATE_CART,
            [
                'json' => json_encode($this->cartWithItems)
            ]
        );
    }
}
