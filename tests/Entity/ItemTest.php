<?php

namespace App\Tests\Entity;

use App\Entity\Cart;
use App\Entity\Item;
use App\Entity\PrintFormat;
use App\Tests\Base\TestBase;

/**
 * ItemTest
 */
class ItemTest extends TestBase
{
    private Item $item;
    private PrintFormat $printFormat;
    private Cart $cart;

    /**
     * getContainerItem
     *
     * @return void
     */
    protected function getContainerItem(): void
    {
        $this->getContainer();
        $this->item = new Item();
        $this->printFormat = new PrintFormat();
        $this->cart = new Cart();
    }

    /**
     * testItemSetUp
     *
     * @return void
     */
    public function testItemSetUp(): void
    {
        $this->getContainerItem();
        $this->testQuantity();
        $this->testPrice();
        $this->testImage();
        $this->testPrintFormat();
        $this->testCart();
    }

    /**
     * testQuantity
     *
     * @return void
     */
    private function testQuantity(): void
    {
        $this->item->setQuantity(5);
        $this->assertEquals(5, $this->item->getQuantity());
    }

    /**
     * testPrice
     *
     * @return void
     */
    private function testPrice(): void
    {
        $this->item->setPrice('5.00');
        $this->assertEquals('5.00', $this->item->getPrice());
    }

    /**
     * testImage
     *
     * @return void
     */
    private function testImage()
    {
        $this->item->setImage('image.jpg');
        $this->assertEquals('image.jpg', $this->item->getImage());
    }

    /**
     * testPrintFormat
     *
     * @return void
     */
    private function testPrintFormat(): void
    {
        $printFormat = $this->printFormat->setName('50x50 cm');
        $this->item->setPrintFormat($printFormat);
        $this->assertEquals('50x50 cm', $this->item->getPrintFormat()->getName());
    }

    /**
     * testCart
     *
     * @return void
     */
    private function testCart(): void
    {
        $cart = $this->cart->setTotal('100');
        $this->item->addCart($cart);

        foreach ($this->item->getCarts() as $cart) {
            $this->assertEquals('100', $cart->getTotal());
            $this->item->removeCart($cart);
            $this->assertCount(0, $this->item->getCarts());
        }
    }
}
