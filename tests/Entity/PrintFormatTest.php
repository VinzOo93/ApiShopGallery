<?php

namespace App\Tests\Entity;

use App\Entity\PrintFormat;
use App\Tests\Base\TestBase;

/**
 * PrintFormatTest
 */
class PrintFormatTest extends TestBase
{
    private PrintFormat $printFormat;

    /**
     * getContainerPrintFormat
     *
     * @return void
     */
    protected function getContainerPrintFormat(): void
    {
        $this->getContainer();
        $this->printFormat = new PrintFormat();
    }

    /**
     * testPrintFormatSetUp
     *
     * @return void
     */
    public function testPrintFormatSetUp(): void
    {
        $this->getContainerPrintFormat();
        $this->testName();
        $this->testWidth();
        $this->testHeight();
        $this->testPrestTaxPrice();
    }

    /**
     * testName
     *
     * @return void
     */
    private function testName(): void
    {
        $this->printFormat->setName('50x50 cm');
        $this->assertEquals('50x50 cm', $this->printFormat->getName());
    }

    /**
     * testWidth
     *
     * @return void
     */
    private function testWidth(): void
    {
        $this->printFormat->setWidth(50);
        $this->assertEquals(50, $this->printFormat->getWidth());
    }

    /**
     * testHeight
     *
     * @return void
     */
    private function testHeight(): void
    {
        $this->printFormat->setHeight(50);
        $this->assertEquals(50, $this->printFormat->getHeight());
    }

    /**
     * testPrestTaxPrice
     *
     * @return void
     */
    private function testPrestTaxPrice(): void
    {
        $this->printFormat->setPreTaxPrice('50.00');
        $this->assertEquals('50.00', $this->printFormat->getPreTaxPrice());
    }
}
