<?php

namespace App\Tests\Base;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiTestBase extends ApiTestCase
{
    use ReloadDatabaseTrait;

    protected const EMAIL_TEST = 'test@example.com';
    protected const PASSWORD_TEST = '$3CR3T';
    protected const ROLE_TEST = ['ROLE_USER'];
    protected const ROUTE_AUTH = 'auth';

    protected Client $client;
    protected ContainerInterface $container;
    protected EntityManagerInterface $entityManager;
    protected User $user;


    /**
     * initTest
     *
     * @return void
     */
    protected function initTest(): void
    {
        $this->client = static::createClient();
        $this->container  = self::getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager();
    }


    /**
     * initEntityUserTest
     *
     * @return void
     */
    protected function initEntityUserTest(): void
    {
        $encryptedPwd = $this->container->get('app.auth_password_hasher.test')->hashPassword(self::PASSWORD_TEST);
        $this->user = new User();
        $this->user = $this->user->setEmail(self::EMAIL_TEST)
            ->setPassword($encryptedPwd)
            ->setRoles(self::ROLE_TEST);
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();
    }



    /**
     * getUrlWithAuthentication
     *
     * @param  array $json
     * @param  string $keyToken
     * @param  string $urlTest
     * @return ResponseInterface
     */
    protected function getUrlWithAuthentication(array $json, string $keyToken, string $urlTest): ResponseInterface
    {
        return $this->client->request(
            'GET',
            $urlTest,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $json[$keyToken]
                ]
            ]
        );
    }

    /**
     * testGetErrorAuth
     *
     * @param  string $urlTest
     * @return void
     */
    protected function testGetErrorAuth(string $urlTest, string $method): void
    {
        $this->client->request(
            $method,
            $urlTest,
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(401);
    }
    /**
     * prepareUser
     *
     * @param  string $urlAuth
     * @return ResponseInterface
     */
    protected function prepareUser(string $urlAuth): ResponseInterface
    {
        return $this->client->request(
            'POST',
            $urlAuth,
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
