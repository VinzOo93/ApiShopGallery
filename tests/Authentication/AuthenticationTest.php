<?php

namespace App\Tests\Authentication;

use App\Tests\Base\AuthenticationTestBase;

class AuthenticationTest extends AuthenticationTestBase
{
    private const METHOD_AUTH = 'GET';

    /**
     * testLogin
     *
     * @return void
     */
    public function testLogin(): void
    {
        $this->initAuthTest();
        $this->testGetErrorAuth(parent::URL_TEST, self::METHOD_AUTH);
        $json = $this->getTokensUser(parent::ROUTE_AUTH, parent::KEY_AUTH_TOKEN);

        $this->assertArrayHasKey(parent::KEY_AUTH_TOKEN, $json);
        $this->assertResponseIsSuccessful();
    }
}
