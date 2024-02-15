<?php

namespace App\Tests\Entity;

use App\Entity\Cart;
use App\Entity\Item;
use App\Tests\Base\TestBase;

/**
 * CartTest.
 */
class CartTest extends TestBase
{
    private Cart $cart;
    private \DateTimeInterface $date;
    private Item $item;

    /**
     * getContainerCart.
     */
    protected function getContainerCart(): void
    {
        $this->getContainer();
        $this->cart = new Cart();
        $this->item = new Item();
        $this->date = new \DateTime('10/01/2023');
    }

    /**
     * testSetUpCart.
     */
    public function testSetUpCart(): void
    {
        $this->getContainerCart();
        $this->testCreatedAt();
        $this->testUpdatedAt();
        $this->testItems();
        $this->testSubtotal();
        $this->testTaxes();
        $this->testShipping();
        $this->testTotal();
    }

    /**
     * testCreatedAt.
     */
    private function testCreatedAt(): void
    {
        $this->cart->setCreatedAt($this->date);
        $this->assertEquals($this->date, $this->cart->getCreatedAt());
    }

    /**
     * testUpdatedAt.
     */
    private function testUpdatedAt(): void
    {
        $this->cart->setUpdatedAt($this->date);
        $this->assertEquals($this->date, $this->cart->getUpdatedAt());
    }

    /**
     * testSubtotal.
     */
    private function testSubtotal(): void
    {
        $this->cart->setSubtotal('100.00');
        $this->assertEquals('100.00', $this->cart->getSubtotal());
    }

    private function testTaxes(): void
    {
        $this->cart->setTaxes('100.00');
        $this->assertEquals('100.00', $this->cart->getTaxes());
    }

    private function testShipping(): void
    {
        $this->cart->setShipping('Delivred');
        $this->assertEquals('Delivred', $this->cart->getShipping());
    }

    /**
     * testTotal.
     */
    private function testTotal(): void
    {
        $this->cart->setTotal('100.00');
        $this->assertEquals('100.00', $this->cart->getTotal());
    }

    /**
     * testItem.
     */
    private function testItems(): void
    {
        $item = $this->item->setUnitPrice('100.00');
        $this->cart->addItem($item);

        foreach ($this->cart->getItems() as $item) {
            $this->assertEquals('100.00', $item->getUnitPrice());
            $this->cart->removeItem($item);
            $this->assertCount(0, $this->cart->getItems());
        }
    }
}
