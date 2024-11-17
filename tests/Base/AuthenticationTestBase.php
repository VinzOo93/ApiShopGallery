<?php

namespace App\Tests\Base;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AuthenticationTestBase extends ApiTestBase
{
    protected const string URL_TEST = '/print_formats?page=1';

    protected function initAuthTest(): void
    {
        $this->initApiTest();
        $this->initApiEntityUserTest();
    }

    /**
     * getLogin.
     *
     * @return array<string,string>
     *
     * @throws TransportExceptionInterface
     */
    protected function getLogin(string $method = 'GET', string $loginRoute = self::URL_TEST): array
    {
        $this->testGetErrorAuth($loginRoute, $method);

        return $this->getTokensUser();
    }

    /**
     * testRouteWithLogin.
     *
     * @param array<string,mixed> $response
     * @throws TransportExceptionInterface
     */
    protected function testRouteWithLogin(array $response): void
    {
        $this->getUrlWithAuthentication($response, self::KEY_AUTH_TOKEN, self::URL_TEST);
        $this->assertResponseIsSuccessful();
    }

    /**
     * getTokensUser.
     *
     * @return array<string,string>
     * @throws TransportExceptionInterface
     */
    protected function getTokensUser(): array
    {
        return $this->prepareUser(parent::ROUTE_AUTH)->toArray();
    }
}
