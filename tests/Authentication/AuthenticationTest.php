<?php

namespace App\Tests\Authentication;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AuthenticationTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    private const EMAIL_TEST = 'test@example.com';
    private const PASSWORD_TEST = '$3CR3T';
    private const ROLE_TEST = ['ROLE_USER'];
    private const URL_TEST = '/print_formats?page=1';

    private Client $client;

    /**
     * testLogin
     *
     * @return void
     */
    public function testLogin(): void
    {


        $this->client = static::createClient();
        $container = self::getContainer();

        $user = new User();
        $encryptedPwd = $container->get('app.auth_password_hasher.test')->hashPassword(self::PASSWORD_TEST);
        $user = $user->setEmail(self::EMAIL_TEST)
            ->setPassword($encryptedPwd)
            ->setRoles(self::ROLE_TEST);

        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();


        $response = $this->prepareUser();
        $json = $response->toArray();
        $this->assertArrayHasKey('token', $json);

        $this->client->request(
            'GET',
            self::URL_TEST,
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(401);
        $this->client->request(
            'GET',
            self::URL_TEST,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $json['token']
                ]
            ]
        );

        $this->assertResponseIsSuccessful();
    }

    private function prepareUser(): ResponseInterface
    {
        return $this->client->request(
            'POST',
            'auth',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'email' => self::EMAIL_TEST,
                    'password' => self::PASSWORD_TEST,
                ],
            ]
        );
    }
}
