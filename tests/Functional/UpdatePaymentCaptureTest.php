<?php

namespace App\Tests\Functional;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use App\Entity\Payment;
use App\Enum\PaymentStatusEnum;
use App\Repository\PaymentRepository;
use App\State\UpdatePaymentCaptureProcessor;
use App\Tests\Base\ShopTestBase;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class UpdatePaymentCaptureTest extends ShopTestBase
{
    private const string ROUTE_PAYMENT_CAPTURE = '/payments/capture';
    private PaymentRepository $paymentRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->initApiTest();
        $this->initShopTest();
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     *
     * @dataProvider getPaymentContext
     */
    public function testCapturePayment(array $data)
    {
        $client = static::createClient();
        $container = static::getContainer();
        $this->paymentRepository = $container->get(PaymentRepository::class);
        $payment = $this->paymentRepository->find($data['paymentId']);
        $paymentProcessorMock = $this->getMockBuilder(UpdatePaymentCaptureProcessor::class)
            ->setConstructorArgs([
                $container->get(PersistProcessor::class),
                $container->get(ParameterBagInterface::class),
                $container->get(HttpClientInterface::class),
                $container->get('doctrine')->getManager(),
            ])
            ->onlyMethods(['capturePayment'])
            ->getMock();
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getContent')->willReturn(json_encode([
            'id' => $payment->getToken(),
            'status' => !empty($data['paypalResponse']) ? $data['paypalResponse'] : 'COMPLETED',
            'amount' => [
                'currency_code' => 'EUR',
                'value' => '105.00',
            ],
        ]));
        $mockResponse->method('getStatusCode')->willReturn(Response::HTTP_CREATED);
        $paymentProcessorMock->method('capturePayment')->willReturn($mockResponse);
        $container->set(UpdatePaymentCaptureProcessor::class, $paymentProcessorMock);
        $tokenResponse = $this->prepareUser(parent::ROUTE_AUTH);
        $response = $client->request(
            Request::METHOD_PATCH,
            self::ROUTE_PAYMENT_CAPTURE,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/merge-patch+json',
                    'Authorization' => 'Bearer '.$tokenResponse->toArray()['token'],
                ],
                'json' => ['cart' => '/carts/'.$payment->getCart()->getToken()],
            ]
        );
        $responseData = json_decode($response->getContent(), true);

        if ($responseData) {
            $this->assertEquals($data['expected'], $responseData['status']);
            if (PaymentStatusEnum::PAID->name === $responseData['status']) {
                /** @var array<int, Payment> $paymentsExpired */
                $paymentsExpired = $this->paymentRepository->findBy(['cart' => $payment->getCart()]);
                unset($paymentsExpired[array_search($payment, $paymentsExpired)]);
                foreach ($paymentsExpired as $item) {
                    $this->assertEquals(PaymentStatusEnum::EXPIRED, $item->getStatus());
                    $this->assertStringContainsString($item->getComment(), 'Paiement payé : 1');
                }
                $this->assertEmailCount(1);
                $mail = $this->getMailerEvent();
                $this->assertEmailSubjectContains($mail->getMessage(), 'Votre commande est bien enregistrée !');
                $this->assertEquals($mail->getEnvelope()->getSender()->getAddress(), 'orru.vincent@orange.fr');
                $this->assertEquals($mail->getEnvelope()->getRecipients()[0]->getAddress(), 'test@live.fr');
                $this->assertEmailTextBodyContains($mail->getMessage(), 'Nous allons prochainement lancer son expédition, nous vous en tenons informé');
            }
        } else {
            $this->assertFalse($data['expected']);
        }
    }

    public static function getPaymentContext(): array
    {
        return [
            [
                [
                    'expected' => PaymentStatusEnum::PAID->value,
                    'paymentId' => 1,
                ],
            ],
            [
                [
                    'expected' => PaymentStatusEnum::REFUSED->value,
                    'paymentId' => 2,
                ],
            ],
            [
                [
                    'expected' => false,
                    'paymentId' => 3,
                ],
            ],
            [
                [
                    'expected' => PaymentStatusEnum::REFUSED->value,
                    'paymentId' => 2,
                    'paypalResponse' => 'ERROR',
                ],
            ],
        ];
    }
}
