<?php

namespace App\Tests\Fixtures;

use App\Repository\PrintFormatRepository;
use App\Tests\Base\DataTestBase;

class PrintFormatYamlTest extends DataTestBase
{
    private PrintFormatRepository $printFormatRepository;
    private const FILE_NAME = 'printFormat.yaml';
    private const ATTRIB_PREFIX = '_pf';


    protected string $classEntityPathPrintFormat;

    /**
     * @var array<string, string>
     */
    private array $printFormatParameters = [];



    /**
     * getContainerPrintFormat
     *
     * @return void
     */
    protected function getContainerPrintFormat(): void
    {
        $this->initContainerDataBase();
        $this->attribPrefix = self::ATTRIB_PREFIX;
        $this->printFormatRepository = $this->container->get(PrintFormatRepository::class);
        $this->classEntityPathPrintFormat = $this->printFormatRepository->getClassName();
    }

    /**
     * testsPrintFormatSetUp
     *
     * @return void
     */
    public function testsPrintFormatSetUp(): void
    {
        $this->getContainerPrintFormat();
        $this->testPrintFormatYamlIsReadable();
        $this->testPrintFormatParametersAndItems();
    }

    /**
     * testPrintFormatYamlIsReadable
     *
     * @return void
     */
    protected function testPrintFormatYamlIsReadable(): void
    {
        $this->parsedYaml = $this->getYamlContent(self::FILE_NAME);
        $this->printFormatParameters = $this->parsedYaml[parent::PARAMETERS_IDX];
        $this->assertYamlIsReadable($this->classEntityPathPrintFormat);
    }
    /**
     * testPrintFormatParametersAndItems
     *
     * @return void
     */
    protected function testPrintFormatParametersAndItems(): void
    {
        foreach ($this->printFormatParameters as $key => $data) {
            $this->checkParameterKeysAndValues($key, $data, [
                'dataValue' => '<{' . $key . '}>',
            ]);
        }
        $this->checkYamlValueUnicityClass();
    }
}
