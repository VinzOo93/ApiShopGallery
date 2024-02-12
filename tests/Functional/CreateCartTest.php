<?php

namespace App\Tests\Shop;

use App\Tests\Base\ShopTestBase;
use Symfony\Component\HttpFoundation\Response;

class CreateCartTest extends ShopTestBase
{
    private array $cartWithItems = [
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

    public function testCreateCartWithItem(): void
    {
        $this->initShopTest();
        $this->testAuthCreateCart();

        $response = $this->prepareUser(parent::ROUTE_AUTH);
        $this->postToApiWithAuthentication(
            $response->toArray(),
            $this->cartWithItems,
            parent::KEY_AUTH_TOKEN,
            self::ROUTE_CREATE_CART
        );
        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }

    private function testAuthCreateCart()
    {
        $this->createCartWithItemNoAuth();
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    private function createCartWithItemNoAuth()
    {
        $this->client->request(
            'POST',
            self::ROUTE_CREATE_CART,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($this->cartWithItems)
        );
    }
}
