<?php

namespace App\Tests\Base;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AuthenticationTestBase extends ApiTestCase
{
    use ReloadDatabaseTrait;

    protected const URL_TEST = '/print_formats?page=1';

    private const EMAIL_TEST = 'test@example.com';
    private const PASSWORD_TEST = '$3CR3T';
    private const ROLE_TEST = ['ROLE_USER'];
    private const ROUTE_AUTH = 'auth';


    protected Client $client;
    protected ContainerInterface $container;
    protected EntityManagerInterface $entityManager;
    private User $user;


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
    protected function initEntityUserTest()
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
     * prepareUser
     *
     * @return ResponseInterface
     */
    protected function prepareUser(): ResponseInterface
    {
        return $this->client->request(
            'POST',
            self::ROUTE_AUTH,
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

    /**
     * getAuthentifcation
     *
     * @param  array<string, string> $json
     * @return void
     */
    protected function getAuthentication(array $json)
    {
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
    }
}
