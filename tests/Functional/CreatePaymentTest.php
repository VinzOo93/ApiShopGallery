<?php

namespace App\Tests\Functional;

use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Tests\Base\ShopTestBase;
use Symfony\Component\HttpFoundation\Request;

class CreatePaymentTest extends ShopTestBase
{
    private const string ROUTE_PAYMENT_CHECKOUT = '/payment/checkout';

    private CartRepository $cartRepository;
    public function setUp(): void
    {
        $this->initApiTest();
        $this->initShopTest();
        $this->createCart();
        $this->cartRepository = $this->container->get(CartRepository::class);
    }

    public function testCheckoutPayment(): void
    {
        /** @var Cart $cart */
        $cart = $this->cartRepository->findOneBy([], ['id' => 'ASC']);

        $response = $this->sendRequestToApi(
            ['cart' => '/carts/'.$cart->getToken()],
            self::ROUTE_PAYMENT_CHECKOUT,
            Request::METHOD_POST
        );
        $this->assertResponseIsSuccessful();

        if (str_contains(json_decode($response->getContent(), true), 'https://www.sandbox.paypal.com/checkoutnow?token=')) {
            $checkoutNowUrlFound = true;
        }

        $this->assertTrue($checkoutNowUrlFound, 'No valid PayPal CheckoutNow URL was found in the response.');
    }
}
