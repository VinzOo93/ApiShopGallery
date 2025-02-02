<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Payment;
use App\Enum\PaymentStatusEnum;
use App\Enum\PaymentTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class UpdatePaymentCaptureProcessor extends BasePayementProcessor implements ProcessorInterface
{
    private const string ROUTE_CAPTURE_ORDER = '/capture';

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')] ProcessorInterface $persistProcessor,
        ParameterBagInterface $parameterBag,
        HttpClientInterface $client,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct($persistProcessor, $parameterBag, $client);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws \Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Payment|false
    {
        /** @var Payment $currentPayment */
        $currentPayment = $data->payment;
        $cart = $currentPayment->getCart();

        $this->entityManager->beginTransaction();

        if (
            !$currentPayment instanceof Payment
            || PaymentStatusEnum::PENDING != $currentPayment->getStatus()
        ) {
            return false;
        }
        if ($cart->getTotal() != $currentPayment->getAmount()) {
            $currentPayment->setStatus(PaymentStatusEnum::REFUSED);
            $currentPayment->setComment('Le montant du paiement ne correspond pas à celui du panier');

            return $this->persistProcessor->process($currentPayment, $operation);
        }

        $paymentRepository = $this->entityManager->getRepository(Payment::class);
        $payments = $paymentRepository->findBy([
            'cart' => $cart,
            'status' => PaymentStatusEnum::PENDING,
            'type' => PaymentTypeEnum::PAYPAL,
        ], [
            'createdAt' => 'DESC',
        ]);
        if (count($payments) > 1) {
            unset($payments[array_search($currentPayment, $payments)]);
            foreach ($payments as $payment) {
                if (PaymentStatusEnum::REFUSED != $payment->getStatus() || PaymentStatusEnum::ERROR != $payment->getStatus) {
                    $payment->setStatus(PaymentStatusEnum::EXPIRED);
                    $payment->setComment('Paiement payé :'.$currentPayment->getId());
                }
                $this->entityManager->persist($payment);
            }
        }
        $responseAuth = json_decode($this->getPaypalAuthResponse()->getContent(), true);
        try {
            $responseCapture = $this->capturePayment($currentPayment, $responseAuth);
            $dataResponse = json_decode($responseCapture->getContent(), true);
            if (Response::HTTP_CREATED === $responseCapture->getStatusCode() && 'COMPLETED' === $dataResponse['status']) {
                $currentPayment->setStatus(PaymentStatusEnum::PAID);
            } else {
                $currentPayment->setStatus(PaymentStatusEnum::ERROR);
                $currentPayment->setComment('PayPal error : '.$dataResponse['message']);
            }
        } catch (\Exception $exception) {
            $this->entityManager->rollback();
            throw new \Exception('Erreur connexion paiement webservice : '.$exception);
        }

        return $this->persistProcessor->process($currentPayment, $operation);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function capturePayment(Payment $currentPayment, mixed $responseAuth): ResponseInterface
    {
        return $this->client->request(
            Request::METHOD_POST,
            $this->parameterBag->get('app.api.baseurl_paypal_sandbox').parent::ROUTE_CHECKOUT_ORDER.'/'.$currentPayment->getToken().self::ROUTE_CAPTURE_ORDER,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$responseAuth['access_token'],
                ],
            ]);
    }
}
