<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Cart;
use App\Entity\Payment;
use App\Enum\PaymentStatusEnum;
use App\Enum\PaymentTypeEnum;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CreatePaymentProcessor extends BasePayementProcessor implements ProcessorInterface
{
    private const string ROUTE_CHECKOUT_ORDER = '/v2/checkout/orders';

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface $persistProcessor,
        protected ParameterBagInterface $parameterBag,
        protected HttpClientInterface $client
    ) {
        parent::__construct($this->parameterBag, $this->client);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Payment
    {
        $responseAuth = json_decode($this->getPaypalAuthResponse()->getContent(), true);

        /** @var Cart $cart */
        $cart = $data->cart;

        $responseCheckout = json_decode($this->client->request(
            Request::METHOD_POST,
            $this->parameterBag->get('app.api.baseurl_paypal_sandbox').self::ROUTE_CHECKOUT_ORDER,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$responseAuth['access_token'],
                ],
                'json' => [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'amount' => [
                                'currency_code' => 'EUR',
                                'value' => $cart->getTotal(),
                            ],
                        ],
                    ],
                    'application_context' => [
                        'return_url' => 'https://www.vincent-orru.com/photo',
                        'cancel_url' => 'https://www.vincent-orru.com/photo',
                        'brand_name' => 'Galerie photo Vincent ORRU',
                        'locale' => 'fr-FR',
                        'landing_page' => 'BILLING',
                        'user_action' => 'PAY_NOW',
                    ],
                ],
            ])->getContent(), true);

        $href = Request::create($responseCheckout['links'][1]['href']);
        $payment = new Payment();
        $payment->setCart($cart)
            ->setLink($this->parameterBag->get('app.api.baseurl_paypal_sandbox').$href->getPathInfo().'?token=')
            ->setToken($href->query->get('token'))
            ->setAmount($cart->getTotal())
            ->setStatus(PaymentStatusEnum::PENDING)
            ->setType(PaymentTypeEnum::PAYPAL);

        return $this->persistProcessor->process($payment, $operation);
    }
}
