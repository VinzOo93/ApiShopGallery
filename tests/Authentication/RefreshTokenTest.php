<?php

namespace App\Tests\Authentication;

use App\Tests\Base\AuthenticationTestBase;
use Symfony\Contracts\HttpClient\ResponseInterface;

class RefreshTokenTest extends AuthenticationTestBase
{
    private const ROUTE_REFRESH_TOKEN = '/token/refresh';

    /**
     * testRefreshToken
     *
     * @return void
     */
    public function testRefreshToken(): void
    {
        $this->initTest();
        $this->initEntityUserTest();

        $response = $this->prepareUser();
        $json = $response->toArray();
        $this->assertArrayHasKey('refresh_token', $json);

        $response = $this->getUserRefreshToken($json['refresh_token']);
        $this->assertResponseIsSuccessful();

        $json = $response->toArray();
        $this->assertArrayHasKey('token', $json);

        $this->getAuthentication($json);
        $this->assertResponseIsSuccessful();
    }

    /**
     * getUserRefreshToken
     *
     * @param  string $token
     * @return ResponseInterface
     */
    private function getUserRefreshToken(string $token): ResponseInterface
    {
        return $this->client->request(
            'POST',
            self::ROUTE_REFRESH_TOKEN,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'refresh_token' => $token
                ],
            ]
        );
    }
}
