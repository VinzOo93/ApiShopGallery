<?php

namespace App\Tests\Authentication;

use App\Tests\Base\AuthenticationTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AuthenticationTest extends AuthenticationTestBase
{
    /**
     * testLogin.
     * @throws TransportExceptionInterface
     */
    public function testLogin(): void
    {
        $this->initAuthTest();
        $this->assertArrayHasKey(parent::KEY_AUTH_TOKEN, $response = $this->getLogin(Request::METHOD_GET));
        $this->testRouteWithLogin($response);
    }
}
