<?php

namespace App\EventListener;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Order::class)]
readonly class OrderNotifierListener
{
    public function __construct(
        private MailerInterface $mailer,
        private ParameterBagInterface $parameterBag
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function postPersist(Order $order, PostPersistEventArgs $eventArgs): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->parameterBag->get('app.api.email_from'), 'Vincent ORRU'))
            ->to($order->getPayment()->getEmail())
            ->subject('Votre commande est enregistrée')
            ->htmlTemplate('email/confirmation-order.html.twig')
            ->context([
                'firstname' => $order->getPayment()->getFirstname(),
                'name' => $order->getPayment()->getName(),
                'items' => $order->getCart()->getItems(),
                'address' => $order->getPayment()->getAddress(),
                'city' => $order->getPayment()->getCity(),
                'postalCode' => $order->getPayment()->getPostalCode(),
                'country' => $order->getPayment()->getCountry(),
                'shipping' => $order->getCart()->getShipping(),
                'taxes' => $order->getCart()->getTaxes(),
                'total' => $order->getCart()->getTotal()
            ]);
        $this->mailer->send($email);
    }
}
