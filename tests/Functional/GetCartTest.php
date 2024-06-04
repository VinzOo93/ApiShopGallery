<?php

namespace App\Tests\Functional;

use App\Tests\Base\ShopTestBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GetCartTest extends ShopTestBase
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testGetCart(): void
    {
        $this->initShopTest();
        $this->createCart();
        $this->testAuthorizedGetCartRoute();
        $this->testNotAuthorizedGetCartRoute();
    }

    private function testAuthorizedGetCartRoute(): void
    {
        $cart = $this->getExistingCart();
        $this->getApiRoute(self::ROUTE_CART.'/'.$cart->getToken());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function testNotAuthorizedGetCartRoute(): void
    {
        $cart = $this->getExistingCart();
        $this->client->request(
            'GET',
            self::ROUTE_CART.'/'.$cart->getToken()
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

}
