<?php

namespace App\Tests\Authentication;

use App\Tests\Base\AuthenticationTestBase;

class AuthenticationTest extends AuthenticationTestBase
{
    private const METHOD_AUTH = 'GET';

    /**
     * testLogin.
     */
    public function testLogin(): void
    {
        $this->initAuthTest();
        $this->assertArrayHasKey(parent::KEY_AUTH_TOKEN, $response = $this->getLogin(self::METHOD_AUTH));
        $this->testRouteWithLogin($response);
    }
}
