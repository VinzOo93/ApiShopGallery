<?php

namespace App\Tests\Authentication;

use App\Tests\Base\AuthenticationTestBase;

class AuthenticationTest extends AuthenticationTestBase
{
    /**
     * testLogin
     *
     * @return void
     */
    public function testLogin(): void
    {

        $this->initTest();
        $this->initEntityUserTest();

        $response = $this->prepareUser();
        $json = $response->toArray();
        $this->assertArrayHasKey('token', $json);
        $this->getErrorAuth();
        $this->assertResponseStatusCodeSame(401);

        $this->getAuthentication($json);
        $this->assertResponseIsSuccessful();
    }

    /**
     * getErrorAuth
     *
     * @return void
     */
    private function getErrorAuth(): void
    {
        $this->client->request(
            'GET',
            parent::URL_TEST,
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]
        );
    }
}
