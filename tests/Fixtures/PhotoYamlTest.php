<?php

namespace App\Tests\Fixtures;

use App\Repository\PhotoRepository;
use App\Tests\Base\DataTestBase;

/**
 * PhotoYamlTest.
 */
class PhotoYamlTest extends DataTestBase
{
    private const FILE_NAME = 'photo.yaml';
    private const QUANTITY_IDX = 'quantity_0';
    private const QUANTITY_SOLD = 'quantitySold';
    private const QUANTITY = 0;
    private const ATTRIB_PREFIX = '_p';

    protected string $classEntityPathPhoto;

    /**
     * @var array<string, string>
     */
    private array $photoParameters = [];

    /**
     * getContainerPhoto.
     */
    protected function getContainerPhoto(): void
    {
        $this->initContainerDataBase();
        $this->attribPrefix = self::ATTRIB_PREFIX;
        $photoRepository = $this->container->get(PhotoRepository::class);
        $this->classEntityPathPhoto = $photoRepository->getClassName();
    }

    /**
     * testPhotoSetUp.
     */
    public function testPhotoSetUp(): void
    {
        $this->getContainerPhoto();
        $this->testPhotoYamlIsReadable();
        $this->testPhotoParametersAndItems();
    }

    /**
     * testPhotoYamlIsReadable.
     */
    protected function testPhotoYamlIsReadable(): void
    {
        $this->parsedYaml = $this->getYamlContent(self::FILE_NAME);
        $this->photoParameters = $this->parsedYaml[parent::PARAMETERS_IDX];

        $this->assertYamlIsReadable($this->classEntityPathPhoto);
        $this->assertPhotoQuantity();
    }

    /**
     * testPhotoParametersAndItems.
     */
    protected function testPhotoParametersAndItems(): void
    {
        foreach ($this->photoParameters as $key => $data) {
            if (self::QUANTITY_IDX !== $key) {
                $this->checkParameterKeysAndValues($key, $data, [
                    'dataValue' => '<{'.self::QUANTITY_IDX.'}>',
                    'avoid' => self::QUANTITY_SOLD,
                ]);
            }
        }
        $this->checkYamlValueUnicityClass();
    }

    /**
     * assertPhotoQuantity.
     */
    private function assertPhotoQuantity(): void
    {
        $this->assertArrayHasKey(
            self::QUANTITY_IDX,
            $this->photoParameters,
            "L'index de la quantité n'éxiste pas"
        );

        $this->assertEquals(
            self::QUANTITY,
            $this->photoParameters[self::QUANTITY_IDX],
            " La quantité n'est pas égale ".self::QUANTITY
        );
    }
}
