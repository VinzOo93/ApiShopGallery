<?php

namespace App\Tests\Base;

class AuthenticationTestBase extends ApiTestBase
{
    protected const URL_TEST = '/print_formats?page=1';

    protected function initAuthTest(): void
    {
        $this->initApiTest();
        $this->initApiEntityUserTest();
    }

    /**
     * getLogin
     *
     * @param  string $method
     * @return array<string,string>
     */
    protected function getLogin(string $method = 'GET', string $loginRoute = self::URL_TEST): array
    {
        $this->testGetErrorAuth($loginRoute, $method);

        return $this->getTokensUser(parent::ROUTE_AUTH);
    }

    /**
     * testRouteWithLogin
     *
     * @param  array<string,mixed> $response
     * @return void
     */
    protected function testRouteWithLogin(array $response): void
    {
        $this->getUrlWithAuthentication($response, self::KEY_AUTH_TOKEN, self::URL_TEST);
        $this->assertResponseIsSuccessful();
    }


    /**
     * getTokensUser
     *
     * @param  string $urlRequest
     * @return array<string,string>     */
    private function getTokensUser(string $urlRequest): array
    {
        return $this->prepareUser($urlRequest)->toArray();
    }
}
