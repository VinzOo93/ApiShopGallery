<?php

namespace App\Tests\Base;

use Symfony\Contracts\HttpClient\ResponseInterface;

class ShopTestBase extends ApiTestBase
{
    public const string ROUTE_CREATE_CART = '/carts';

    /**
     * initShopTest.
     */
    protected function initShopTest(): void
    {
        $this->initApiTest();
        $this->initApiEntityUserTest();
    }

    protected function createOnDb(mixed $object, string $route): ResponseInterface
    {
        $response = $this->prepareUser(parent::ROUTE_AUTH);
        return $this->postToApiWithAuthentication(
            $response->toArray(),
            $object,
            parent::KEY_AUTH_TOKEN,
            $route
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
    protected function createObjectWithNoAuth(string $route, array $object)
    {
        $this->client->request(
            'POST',
            $route,
            [
                'json' => json_encode($object),
            ]
        );
    }
}
