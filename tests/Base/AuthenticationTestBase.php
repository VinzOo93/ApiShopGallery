<?php

namespace App\Tests\Base;

class AuthenticationTestBase extends ApiTestBase
{
    protected const URL_TEST = '/print_formats?page=1';
    protected const KEY_AUTH_TOKEN = 'token';

    protected function initAuthTest(): void
    {
        $this->initTest();
        $this->initEntityUserTest();
    }

    /**
     * getTokensUser
     *
     * @param  mixed $urlRequest
     * @param  mixed $key
     * @return array 
     */
    protected function getTokensUser(string $urlRequest): array
    {
        $response = $this->prepareUser($urlRequest);

        return $response->toArray();
    }
}
