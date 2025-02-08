<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Cart;
use App\Entity\Payment;
use App\Enum\PaymentStatusEnum;
use App\Enum\PaymentTypeEnum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CreatePaymentProcessor extends BasePayementProcessor implements ProcessorInterface
{
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
        $email = $data->email;
        $address = $data->address;
        $postalCode = $data->postalCode;
        $city = $data->city;
        $country = $data->country;

        $responseCheckout = json_decode($this->client->request(
            Request::METHOD_POST,
            $this->parameterBag->get('app.api.baseurl_paypal_sandbox').parent::ROUTE_CHECKOUT_ORDER,
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
                    'payment_source' => [
                        'paypal' => [
                            'experience_context' => [
                                'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                                'landing_page' => 'LOGIN',
                                'user_action' => 'PAY_NOW',
                                'return_url' => 'https://www.vincent-orru.com/payment/confirmation',
                            ],
                        ],
                    ],
                ],
            ])->getContent(), true);
        $href = Request::create($responseCheckout['links'][1]['href']);
        $payment = new Payment();
        $payment->setCart($cart)
            ->setLink('https://'.$href->headers->get('host').$href->getPathInfo().'?token=')
            ->setToken($href->query->get('token'))
            ->setAmount($cart->getTotal())
            ->setStatus(PaymentStatusEnum::PENDING)
            ->setType(PaymentTypeEnum::PAYPAL)
            ->setEmail($email)
            ->setAddress($address)
            ->setPostalCode($postalCode)
            ->setCity($city)
            ->setCountry($country);

        return $this->persistProcessor->process($payment, $operation);
    }
}
