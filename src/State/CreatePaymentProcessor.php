<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class CreatePaymentProcessor implements ProcessorInterface
{
    private const string ROUTE_AUTH = '/v1/oauth2/token';

    public function __construct(
        private ParameterBagInterface $parameterBag,
        private HttpClientInterface $client
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $response = $this->client->request(
            Request::METHOD_POST,
            $this->parameterBag->get('app.api.baseurl_paypal_sandbox').self::ROUTE_AUTH,
            [
                'auth_basic' => [
                    $this->parameterBag->get('app.api.username_paypal_sandbox'),
                    $this->parameterBag->get('app.api.password_paypal_sandbox'),
                ],
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'body' => [
                    'grant_type' => 'client_credentials',
                ],
            ]);


        return json_decode($response->getContent(), true)['access_token'];
    }
}
