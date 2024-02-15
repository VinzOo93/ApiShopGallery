<?php

namespace App\Tests\Entity;

use App\Entity\Photo;
use App\Tests\Base\TestBase;

/**
 * PhotoTest.
 */
class PhotoTest extends TestBase
{
    private Photo $photo;

    protected function getContainerPhoto(): void
    {
        $this->getContainer();
        $this->photo = new Photo();
    }

    public function testPrintFormatSetUp(): void
    {
        $this->getContainerPhoto();
        $this->testName();
        $this->testUrlCdn();
        $this->testQuantitySold();
    }

    /**
     * testName.
     */
    private function testName(): void
    {
        $this->photo->setName('oulah oulah');
        $this->assertEquals('oulah oulah', $this->photo->getName());
    }

    /**
     * getUrlCdn.
     */
    private function testUrlCdn(): void
    {
        $this->photo->setUrlCdn('http://cdn.fr/oulahoulah.jpg');
        $this->assertEquals('http://cdn.fr/oulahoulah.jpg', $this->photo->getUrlCdn());
    }

    /**
     * testQuantitySold.
     *
     * @return void
     */
    private function testQuantitySold(): void
    {
        $this->photo->setQuantitySold(10);
        $this->assertEquals(10, $this->photo->getQuantitySold());
    }
}
