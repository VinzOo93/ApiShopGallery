<?php

namespace App\Tests\Functional;

use App\Dto\CreateItemDto;
use App\Entity\Cart;
use App\Entity\Item;
use App\Tests\Base\ShopTestBase;
use Symfony\Component\HttpFoundation\Response;

class CreateItemTest extends ShopTestBase
{
    private const string ROUTE_CREATE_ITEM = '/items';

    /** @var array<string,mixed> */
    private array $itemToBeCreated = [
        'quantity' => 2,
        'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
        'printFormat' => '30x20 cm',
        'unitPrice' => '480.00',
        'unitPreTaxPrice' => '400.00',
        'preTaxPrice' => '800.00',
        'taxPrice' => '960.00',
    ];

    public function testCreateItem(): void
    {
        $this->initShopTest();
        $this->testAuthCreateCart();
        $this->createCart();
        $this->testCreatInExistingCartCart();
    }

    /**
     * createCartWithItemNoAuth.
     */
    private function testAuthCreateCart(): void
    {
        $this->createObjectWithNoAuth(self::ROUTE_CREATE_ITEM, $this->itemToBeCreated);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(0, $this->countObjectsOnDb(Item::class));
    }

    private function createCart(): void
    {
        $data = new CreateItemDto();
        $data->item = $this->itemToBeCreated;
        $this->createOnDb([$data], self::ROUTE_CREATE_ITEM);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertEquals(1, $this->countObjectsOnDb(Cart::class));
    }


    private function testCreatInExistingCartCart(): void
    {
        $data = $this->createItemDto($this->itemToBeCreated);
        $this->createOnDb([$data], self::ROUTE_CREATE_ITEM);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertEquals(2, $this->countObjectsOnDb(Item::class));
    }

    private function getExistingCart(): Cart
    {
        $cartRepository = $this->entityManager->getRepository(Cart::class);

        return $cartRepository->findOneBy([], ['id' => 'ASC']);
    }

    private function createItemDto(array $item): CreateItemDto
    {
        $data = new CreateItemDto();
        $data->item = $item;
        $data->cart = $this->getExistingCart();

        return $data;
    }
}
