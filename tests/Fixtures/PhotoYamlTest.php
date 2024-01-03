<?php 

namespace App\Tests\Fixtures;

use App\Repository\PhotoRepository;

class PhotoYamlTest extends DataTest
{
    private const PARAMETERS_IDX = 'parameters';
    private const FILE_NAME = 'photo.yaml';
    private const QUANTITY_IDX = 'quantity_0';
    private const QUANTITY_SOLD = 'quantitySold';
    private const QUANTITY = 0;

    protected string $classEntityPathPhoto;
    
    private PhotoRepository $photoRepository;
    private array $photoParameters = [];
    private array $parsedYaml = [];
    private string $dataPhotoValue;

    protected function getContainerPhoto(): void
    {
        $this->initContainer();
        $this->photoRepository = $this->container->get(PhotoRepository::class);   
        $this->classEntityPathPhoto = $this->photoRepository->getClassName();
    }

    public function testPhotoSetUp(): void
    {
        $this->getContainerPhoto();
        $this->testPhotoYamlFixtures();
        $this->testPhotoParameters();
    }

    protected function testPhotoYamlFixtures(): void
    {
        $this->parsedYaml = $this->getYamlContent(PhotoYamlTest::FILE_NAME);
        $this->photoParameters = $this->parsedYaml[PhotoYamlTest::PARAMETERS_IDX];

        $this->assertArrayHasKey(PhotoYamlTest::PARAMETERS_IDX, $this->parsedYaml, "L'index des paramètres n'éxiste pas");
        $this->assertArrayHasKey($this->classEntityPathPhoto, $this->parsedYaml, "L'index de la classe n'éxiste pas");
        $this->assertArrayHasKey(PhotoYamlTest::QUANTITY_IDX,$this->photoParameters, "L'index de la quantité n'éxiste pas");
        $this->assertEquals(PhotoYamlTest::QUANTITY, $this->photoParameters[PhotoYamlTest::QUANTITY_IDX], " La quantité n'est pas égale " . PhotoYamlTest::QUANTITY);
               
    }  

    protected function testPhotoParameters(): void
    {
        foreach ($this->photoParameters as $key => $data) {
            if ($key !== PhotoYamlTest::QUANTITY_IDX) {
                    $this->checkPhotoParameterKeys($key, $data);
                }
            }
        $this->checkYamlValueUnicityClass();
    }

    private function checkPhotoParameterKeys(string $key, string $data): void
    {
        $this->checkYamlValueUnicityParameter($this->photoParameters, $data);
        if ($this->shouldProcessKey($key)) {
            $photoClass = $this->parsedYaml[$this->classEntityPathPhoto];
            $this->index = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $this->checkItemKeyExist($photoClass,$this->classEntityPathPhoto);
            $this->testPhotoParametersValuesOnClass($photoClass[$this->itemKey]);
        }        
    }   

    private function testPhotoParametersValuesOnClass(array $item): void 
    {
        $columns = $this->entityManager->getClassMetadata($this->classEntityPathPhoto)->getColumnNames();
        $attribLoop = 1;

        foreach($item as $attribut) { 
            $this->dataPhotoValue = "<{".PhotoYamlTest::QUANTITY_IDX."}>";   
            if ($attribut !== $item[PhotoYamlTest::QUANTITY_SOLD]) {
                $this->dataPhotoValue = $this->registerClassValues($columns, $attribLoop);
            }
            $this->checkYamlKeyParameterByClassValue($this->photoParameters, $attribut);
            $this->checkYamlValueByAttributClass($this->dataPhotoValue, $attribut);
            
            $attribLoop++;
        }  
    }
}

?>