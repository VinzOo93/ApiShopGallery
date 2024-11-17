<?php

namespace App\Tests\Authentication;

use App\State\BasePayementProcessor;
use App\Tests\Base\AuthenticationTestBase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AuthenticationPayPalTest extends AuthenticationTestBase
{
    private BasePayementProcessor $basePayementProcessor;

    public function setUp(): void
    {
        parent::setUp();
        $this->basePayementProcessor = $this->getContainer()->get(BasePayementProcessor::class);
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testPaypalAuth()
    {
        $response = $this->basePayementProcessor->getPaypalAuthResponse();
        $this->assertArrayHasKey('access_token', json_decode($response->getContent(), true));
    }
}
