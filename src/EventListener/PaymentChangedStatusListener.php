<?php

namespace App\EventListener;

use App\Entity\Order;
use App\Entity\Payment;
use App\Enum\PaymentStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Payment::class)]
readonly class PaymentChangedStatusListener
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }


    public function postUpdate(Payment $payment, PostUpdateEventArgs $eventArgs): void
    {
        if (PaymentStatusEnum::PAID === $payment->getStatus()) {
            $order = new Order();
            $order->setPayment($payment)
                ->setCart($payment->getCart())
            ;
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        }
    }
}
