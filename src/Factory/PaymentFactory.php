<?php

namespace App\Factory;

use App\Entity\Payment;
use App\Enum\PaymentStatusEnum;
use App\Enum\PaymentTypeEnum;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Payment>
 *
 * @method        Payment|Proxy                              create(array|callable $attributes = [])
 * @method static Payment|Proxy                              createOne(array $attributes = [])
 * @method static Payment|Proxy                              find(object|array|mixed $criteria)
 * @method static Payment|Proxy                              findOrCreate(array $attributes)
 * @method static Payment|Proxy                              first(string $sortedField = 'id')
 * @method static Payment|Proxy                              last(string $sortedField = 'id')
 * @method static Payment|Proxy                              random(array $attributes = [])
 * @method static Payment|Proxy                              randomOrCreate(array $attributes = [])
 * @method static PaymentRepository|ProxyRepositoryDecorator repository()
 * @method static Payment[]|Proxy[]                          all()
 * @method static Payment[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Payment[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Payment[]|Proxy[]                          findBy(array $attributes)
 * @method static Payment[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Payment[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class PaymentFactory extends PersistentProxyObjectFactory
{

    public static function class(): string
    {
        return Payment::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     */
    protected function defaults(): array|callable
    {
        return [
            'amount' => '105.00',
            'cart' => CartFactory::createOne(),
            'createdAt' => new \DateTimeImmutable(),
            'link' => 'https://api-m.sandbox.paypal.com/checkoutnow?token=',
            'status' => PaymentStatusEnum::PENDING,
            'token' => '5O190127TN'.uniqid(),
            'type' => PaymentTypeEnum::PAYPAL,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Payment $payment): void {})
        ;
    }
}
