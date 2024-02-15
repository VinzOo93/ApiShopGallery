<?php

namespace App\Tests\Fixtures;

use App\Entity\PrintFormat;
use App\Repository\PrintFormatRepository;
use App\Tests\Base\DataTestBase;

class PrintFormatDbTest extends DataTestBase
{
    private PrintFormatRepository $printFormatRepository;

    /**
     * @var array<int, PrintFormat>
     */
    private array $printFormats;

    /**
     * getContainerPrintFormat.
     */
    protected function getContainerPrintFormat(): void
    {
        $this->initContainerDataBase();
        $this->printFormatRepository = $this->container->get(PrintFormatRepository::class);
    }

    public function testPrintFormatSetUp(): void
    {
        $this->getContainerPrintFormat();
        $this->printFormats = $this->getAllPrintFormats();
        $this->testPrintFormatData();
    }

    /**
     * @return PrintFormat[]
     */
    private function getAllPrintFormats(): array
    {
        return $this->printFormatRepository->findAll();
    }

    /**
     * testPrintFormat.
     */
    protected function testPrintFormatData(): void
    {
        foreach ($this->printFormats as $printFormat) {
            $this->assertInstanceOf(
                PrintFormat::class,
                $printFormat,
                "l'objet retourné ne provient pas de la classe PrintFormat"
            );

            $this->checkDbUnicity($this->printFormatRepository, [
                'key' => 'name',
                'value' => $printFormat->getName(),
            ]);

            $this->checkDbUnicity($this->printFormatRepository, [
                'key' => 'width',
                'value' => $printFormat->getWidth(),
            ]);

            $this->checkDbUnicity($this->printFormatRepository, [
                'key' => 'height',
                'value' => $printFormat->getHeight(),
            ]);
        }
    }
}
