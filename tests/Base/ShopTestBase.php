<?php

namespace App\Tests\Base;

use App\Entity\Cart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ShopTestBase extends ApiTestBase
{
    public const string ROUTE_CART = '/carts';
    protected const string ROUTE_ITEM = '/items';

    /** @var array<string,mixed> */
    public array $itemToBeCreated = [
        'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11b',
        'printFormat' => '/print_formats/1',
    ];

    /**
     * initShopTest.
     */
    protected function initShopTest(): void
    {
        $this->initApiTest();
        $this->initApiEntityUserTest();
    }

    protected function sendRequestToApi(mixed $object, string $route, string $method): ResponseInterface
    {
        $response = $this->prepareUser(parent::ROUTE_AUTH);

        return $this->sendToApiWithAuthentication(
            $response->toArray(),
            $object,
            parent::KEY_AUTH_TOKEN,
            $route,
            $method
        );
    }

    /**
     * @param class-string $className
     */
    protected function countObjectsOnDb(string $className): int
    {
        /** @var class-string $className * */
        $repository = $this->entityManager->getRepository($className);

        return count($repository->findAll());
    }

    protected function getApiRoute(string $route): ResponseInterface
    {
        $response = $this->prepareUser(parent::ROUTE_AUTH);

        return $this->getUrlWithAuthentication(
            $response->toArray(),
            parent::KEY_AUTH_TOKEN,
            $route
        );
    }

    /**
     * @return void
     */
    protected function createObjectWithNoAuth(string $route, array $object): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $route,
            [
                'json' => json_encode($object),
            ]
        );
    }

    protected function getExistingCart(): Cart
    {
        $cartRepository = $this->entityManager->getRepository(Cart::class);

        return $cartRepository->findOneBy([], ['id' => 'ASC']);
    }

    protected function createCart(): void
    {
        $this->sendRequestToApi($this->itemToBeCreated, self::ROUTE_ITEM, Request::METHOD_POST);
    }
}
