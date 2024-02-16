<?php

namespace App\Tests\Base;

use Doctrine\Persistence\ObjectRepository;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ShopTestBase extends ApiTestBase
{
    public const ROUTE_CREATE_CART = '/carts';

    /** @var array<string,mixed> */
    public array $cartWithItems = [
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
        'token' => 'U2FsdGVkX19zFZglY9uaxbJgmzermb3d1Eu6gj224lg=',
    ];

    /**
     * initShopTest.
     */
    protected function initShopTest(): void
    {
        $this->initApiTest();
        $this->initApiEntityUserTest();
    }

    /**
     * @param array<string, mixed> $object
     */
    protected function createOnDb(array $object, string $route): void
    {
        $response = $this->prepareUser(parent::ROUTE_AUTH);
        $this->postToApiWithAuthentication(
            $response->toArray(),
            $object,
            parent::KEY_AUTH_TOKEN,
            $route
        );
    }

    /**
     * @param mixed $class
     * @return int
     */
    protected function countObjectsOnDb(mixed $class): int
    {
        $cartRepo = $this->entityManager->getRepository($class);

        return count($cartRepo->findAll());
    }

    /**
     * @param string $route
     * @return ResponseInterface
     */
    protected function getApiRoute(string $route): ResponseInterface
    {
        $response = $this->prepareUser(parent::ROUTE_AUTH);

        return $this->getUrlWithAuthentication(
            $response->toArray(),
            parent::KEY_AUTH_TOKEN,
            $route
        );
    }
}
