<?php

namespace App\Tests\Functional;

use App\Tests\Base\ShopTestBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GetCartTest extends ShopTestBase
{
    private const GET_URL_CART = 'carts/U2FsdGVkX19zFZglY9uaxbJgmzermb3d1Eu6gj224lg=';

    /**
     * @throws TransportExceptionInterface
     */
    public function testGetCart(): void
    {
        $this->initShopTest();
        $this->testAuthorizedGetCartRoute();
        $this->testNotAuthorizedGetCartRoute();
    }

    private function testAuthorizedGetCartRoute(): void
    {
        $this->getApiRoute(self::GET_URL_CART);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    private function testNotAuthorizedGetCartRoute(): void
    {
        $this->client->request(
            'GET',
            self::GET_URL_CART
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
