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
     * testRefreshToken
     *
     * @return void
     */
    public function testRefreshToken(): void
    {
        $this->initAuthTest();

        $this->testGetErrorAuth(self::ROUTE_REFRESH_TOKEN, self::METHOD_REFRESH_TOKEN);

        $json = $this->getTokensUser(parent::ROUTE_AUTH);

        $this->assertArrayHasKey(self::KEY_REFRESH_TOKEN, $json);

        $response = $this->getUserRefreshToken($json[self::KEY_REFRESH_TOKEN]);

        $this->assertArrayHasKey(parent::KEY_AUTH_TOKEN, $response->toArray());

        $this->getUrlWithAuthentication($response->toArray(), parent::KEY_AUTH_TOKEN, parent::URL_TEST);
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
