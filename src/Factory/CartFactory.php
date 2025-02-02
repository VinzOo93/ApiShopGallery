<?php

namespace App\Factory;

use App\Entity\Cart;
use App\Repository\CartRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Cart>
 *
 * @method        Cart|Proxy                              create(array|callable $attributes = [])
 * @method static Cart|Proxy                              createOne(array $attributes = [])
 * @method static Cart|Proxy                              find(object|array|mixed $criteria)
 * @method static Cart|Proxy                              findOrCreate(array $attributes)
 * @method static Cart|Proxy                              first(string $sortedField = 'id')
 * @method static Cart|Proxy                              last(string $sortedField = 'id')
 * @method static Cart|Proxy                              random(array $attributes = [])
 * @method static Cart|Proxy                              randomOrCreate(array $attributes = [])
 * @method static CartRepository|ProxyRepositoryDecorator repository()
 * @method static Cart[]|Proxy[]                          all()
 * @method static Cart[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Cart[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Cart[]|Proxy[]                          findBy(array $attributes)
 * @method static Cart[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Cart[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 **/
final class CartFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Cart::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'createdAt' => new \DateTimeImmutable(),
            'shipping' => '5.00',
            'subtotal' => '100.00',
            'taxes' => '20.00',
            'token' => 'pGwFZj3E7F5ZcuXkv3fOTVqcozA3Qoj-'.uniqid(),
            'total' => '105.00',
            'updatedAt' => new \DateTimeImmutable(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Cart $cart): void {})
        ;
    }
}
