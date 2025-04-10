<?php

namespace App\Tests\Functional;

use App\Entity\Cart;
use App\Entity\Payment;
use App\Repository\CartRepository;
use App\Tests\Base\ShopTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CreatePaymentTest extends ShopTestBase
{
    private const string ROUTE_PAYMENT_CHECKOUT = '/payments/checkout';

    private CartRepository $cartRepository;

    public function setUp(): void
    {
        $this->initApiTest();
        $this->initShopTest();
        $this->createCart();
        $this->cartRepository = $this->container->get(CartRepository::class);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function testCheckoutPayment(): void
    {
        /** @var Cart $cart */
        $cart = $this->cartRepository->findOneBy([], ['id' => 'ASC']);

        $response = $this->sendRequestToApi(
            [
                'cart' => '/carts/'.$cart->getToken(),
                'email' => 'test@live.fr',
                'firstname' => 'Carlo',
                'secondName' => 'Ancelotti',
                'address' => 'rue de la pinte',
                'postalCode' => '90500',
                'city' => 'Beaucourt',
                'country' => 'France',
            ],
            self::ROUTE_PAYMENT_CHECKOUT,
            Request::METHOD_POST
        );
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'type' => 'PAYPAL',
            'link' => 'https://www.sandbox.paypal.com/checkoutnow?token=',
            'status' => 'PENDING',
            'amount' => '105.00',
            'createdAt' => (new \DateTimeImmutable())->format('c'),
        ]);

        $paymentRepository = $this->entityManager->getRepository(Payment::class);
        /** @var Payment $payment */
        $payment = $paymentRepository->find(7);

        $data = json_decode($response->getContent());
        $this->assertEquals($payment->getToken(), $data->token);
        $this->assertEquals('/carts/'.$payment->getCart()->getToken(), $data->cart);
        $this->assertEquals($payment->getAmount(), $payment->getCart()->getTotal());
    }
}
