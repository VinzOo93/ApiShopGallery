<?php

namespace App\Tests\Base;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiTestBase extends ApiTestCase
{
    use ReloadDatabaseTrait;

    private const string EMAIL_TEST = 'dev@apiShopGallery.com';
    private const string PASSWORD_TEST = 'devShop';
    private const array ROLE_TEST = ['ROLE_USER'];
    protected const string ROUTE_AUTH = 'auth';
    protected const string KEY_AUTH_TOKEN = 'token';

    protected Client $client;
    protected ContainerInterface $container;
    protected EntityManagerInterface $entityManager;
    protected User $user;

    /**
     * initApiTest.
     */
    protected function initApiTest(): void
    {
        $this->client = static::createClient();
        $this->container = self::getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager();
    }

    /**
     * initApiEntityUserTest.
     */
    protected function initApiEntityUserTest(): void
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
     * getUrlWithAuthentication.
     *
     * @param array<string,mixed> $json
     *
     * @throws TransportExceptionInterface
     */
    protected function getUrlWithAuthentication(array $json, string $keyToken, string $urlTest): ResponseInterface
    {
        return $this->client->request(
            'GET',
            $urlTest,
            [
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Authorization' => 'Bearer '.$json[$keyToken],
                ],
            ]
        );
    }

    /**
     * sendToApiWithAuthentication.
     *
     * @param array<string,mixed> $json
     * @param array<string,mixed> $data
     *
     * @throws TransportExceptionInterface
     */
    protected function sendToApiWithAuthentication(
        array $json,
        mixed $data,
        string $keyToken,
        string $urlTest,
        string $method = Request::METHOD_POST
    ): ResponseInterface {
        return $this->client->request(
            $method,
            $urlTest,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => Request::METHOD_PATCH === $method ?
                        'application/merge-patch+json' : 'application/json',
                    'Authorization' => 'Bearer '.$json[$keyToken],
                ],
                'body' => json_encode($data),
            ]
        );
    }

    /**
     * testGetErrorAuth.
     *
     * @throws TransportExceptionInterface
     */
    protected function testGetErrorAuth(string $urlTest, string $method = 'GET'): void
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
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * prepareUser.
     *
     * @throws TransportExceptionInterface
     */
    protected function prepareUser(string $urlAuth): ResponseInterface
    {
        return $this->client->request(
            'POST',
            $urlAuth,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'email' => self::EMAIL_TEST,
                    'password' => self::PASSWORD_TEST,
                ],
            ]
        );
    }
}
