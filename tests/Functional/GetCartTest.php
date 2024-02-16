<?php

namespace App\Tests\Functional;

use App\Tests\Base\ShopTestBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GetCartTest extends ShopTestBase
{
    public const GET_URL = 'carts/U2FsdGVkX19zFZglY9uaxbJgmzermb3d1Eu6gj224lg=';

    /**
     * @throws TransportExceptionInterface
     */
    public function testGetCart(): void
    {
        $this->initShopTest();
        $response = $this->prepareUser(parent::ROUTE_AUTH);
        $this->getUrlWithAuthentication($response->toArray(),
            parent::KEY_AUTH_TOKEN, self::GET_URL);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);


    }
}
