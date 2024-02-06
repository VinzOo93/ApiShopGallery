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
        $this->testImage();
        $this->testPrintFormat();
        $this->testCart();
        $this->testUnitPrice();
        $this->testUnitPreTaxPrice();
        $this->testPreTaxPrice();
        $this->testTaxPrice();
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
     * testUnitPrice
     *
     * @return void
     */
    private function testUnitPrice(): void
    {
        $this->item->setUnitPrice('5.00');
        $this->assertEquals('5.00', $this->item->getUnitPrice());
    }

    /**
     * testUnitPreTaxPrice
     *
     * @return void
     */
    private function testUnitPreTaxPrice(): void
    {
        $this->item->setUnitPreTaxPrice('5.00');
        $this->assertEquals('5.00', $this->item->getUnitPreTaxPrice());
    }

    /**
     * testPreTaxPrice
     *
     * @return void
     */
    private function testPreTaxPrice(): void
    {
        $this->item->setUnitPreTaxPrice('5.00');
        $this->assertEquals('5.00', $this->item->getUnitPreTaxPrice());
    }

    /**
     * testTaxPrice
     *
     * @return void
     */
    private function testTaxPrice(): void
    {
        $this->item->setTaxPrice('5.00');
        $this->assertEquals('5.00', $this->item->getTaxPrice());
    }

    /**
     * testCart
     *
     * @return void
     */
    private function testCart(): void
    {
        $cart = $this->cart->setSubtotal("100.00");
        $this->item->setCart($cart);
        $this->assertEquals("100.00", $this->item->getCart()->getSubtotal());
    }
}
