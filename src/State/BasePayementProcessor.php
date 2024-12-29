<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BasePayementProcessor
{
    protected const string ROUTE_AUTH = '/v1/oauth2/token';

    protected const string ROUTE_CHECKOUT_ORDER = '/v2/checkout/orders';

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        protected ProcessorInterface $persistProcessor,
        protected ParameterBagInterface $parameterBag,
        protected HttpClientInterface $client
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getPaypalAuthResponse(): ResponseInterface
    {
        return
            $this->client->request(
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
    }
}
