<?php 

namespace App\Tests\Fixtures;

use App\Repository\PhotoRepository;

class PhotoYamlTest extends DataTest
{
    private const FILE_NAME = 'photo.yaml';
    private const QUANTITY_IDX = 'quantity_0';
    private const QUANTITY_SOLD = 'quantitySold';
    private const QUANTITY = 0;

    protected string $classEntityPathPhoto;
    
    private PhotoRepository $photoRepository;
    private array $photoParameters = [];

    protected function getContainerPhoto(): void
    {
        $this->initContainer();
        $this->photoRepository = $this->container->get(PhotoRepository::class);   
        $this->classEntityPathPhoto = $this->photoRepository->getClassName();
    }

    public function testPhotoSetUp(): void
    {
        $this->getContainerPhoto();
        $this->testPhotoYamlIsReadable();
        $this->testPhotoParametersAndItems();
    }

    protected function testPhotoYamlIsReadable(): void
    {
        $this->parsedYaml = $this->getYamlContent(self::FILE_NAME);
        $this->photoParameters = $this->parsedYaml[parent::PARAMETERS_IDX];

        $this->assertYamlIsReadable($this->classEntityPathPhoto);
        $this->assertPhotoQuantity();

    }  

    protected function testPhotoParametersAndItems(): void
    {
        foreach ($this->photoParameters as $key => $data) {
            if ($key !== self::QUANTITY_IDX) {
                $this->checkParameterKeysAndValues($key, $data, [
                    "dataValue" => "<{".self::QUANTITY_IDX."}>",
                    "avoid" => self::QUANTITY_SOLD,
                    "parameters" => $this->photoParameters
                ]);
            }
            }
        $this->checkYamlValueUnicityClass();
    }

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
              " La quantité n'est pas égale " . self::QUANTITY
        );    
    }
}

?>