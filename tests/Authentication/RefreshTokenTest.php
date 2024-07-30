<?php

namespace App\Tests\Authentication;

use App\Tests\Base\AuthenticationTestBase;
use Symfony\Contracts\HttpClient\ResponseInterface;

class RefreshTokenTest extends AuthenticationTestBase
{
    private const ROUTE_REFRESH_TOKEN = '/token/refresh';
    private const KEY_REFRESH_TOKEN = 'refresh_token';
    private const METHOD_REFRESH_TOKEN = 'POST';

    /**
     * testRefreshToken.
     */
    public function testRefreshToken(): void
    {
        $this->initAuthTest();

        $this->assertArrayHasKey(
            self::KEY_REFRESH_TOKEN,
            $json = $this->getLogin(
                self::METHOD_REFRESH_TOKEN,
                self::ROUTE_REFRESH_TOKEN
            )
        );

        $this->assertArrayHasKey(
            parent::KEY_AUTH_TOKEN,
            $response = $this->getUserRefreshToken($json[self::KEY_REFRESH_TOKEN])->toArray()
        );

        $this->testRouteWithLogin($response);
    }

    /**
     * getUserRefreshToken.
     */
    private function getUserRefreshToken(string $token): ResponseInterface
    {
        return $this->client->request(
            'POST',
            self::ROUTE_REFRESH_TOKEN,
            [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                    'Accept' => 'application/ld+json',
                ],
                'json' => [
                    'refresh_token' => $token,
                ],
            ]
        );
    }
}
