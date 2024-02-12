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
