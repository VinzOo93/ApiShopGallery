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

    private PhotoRepository $photoRepository;
    protected string $classEntityPathPhoto;


    protected function getContainerPhoto(): void
    {
        $this->initContainer();
        $this->photoRepository = $this->container->get(PhotoRepository::class);   
        $this->classEntityPathPhoto = $this->photoRepository->getClassName();
    }

    public function testsPhotoSetUp() {
        $this->getContainerPhoto();
        $this->testPhotoYamlFixtures();
    }

    protected function testPhotoYamlFixtures(): void
    {

        $photoMetadata = $this->entityManager->getClassMetadata($this->classEntityPathPhoto);
        $photoColumn = $photoMetadata->getColumnNames(); 
        $parsedYaml = $this->getYamlContent(PhotoYamlTest::FILE_NAME);
        $photoParameters = $parsedYaml[PhotoYamlTest::PARAMETERS_IDX];

        $this->assertArrayHasKey(PhotoYamlTest::PARAMETERS_IDX, $parsedYaml, "L'index des paramètres n'éxiste pas");
        $this->assertArrayHasKey($this->classEntityPathPhoto, $parsedYaml, "L'index de la classe n'éxiste pas");
        $this->assertArrayHasKey(PhotoYamlTest::QUANTITY_IDX,$photoParameters, "L'index de la quantité n'éxiste pas");
        $this->assertEquals(PhotoYamlTest::QUANTITY, $photoParameters[PhotoYamlTest::QUANTITY_IDX], " La quantité n'est pas égale " . PhotoYamlTest::QUANTITY);
        
        foreach ($photoParameters as $key => $data) {
            if ($key !== PhotoYamlTest::QUANTITY_IDX ) {
                $attribLoop = 1;
                $i = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
                $photoItemKey = "photo_".$i."{".$i."..".$i."}";
                $photoClass = $parsedYaml[$this->classEntityPathPhoto];

                $this->checkYamlValueUnicityParameter($photoParameters, $data);
                $this->assertArrayHasKey($photoItemKey, $photoClass,
                    "la clé dans la classe $photoItemKey n'éxiste pas"
                );
                $item = $photoClass[$photoItemKey];
                foreach($item as $attribut) {    
                    $dataPhotoValue = "<{" .PhotoYamlTest::QUANTITY_IDX. "}>";    
                    if ($attribut !== $item[PhotoYamlTest::QUANTITY_SOLD]) {
                        $dataPhotoValue ="<{".$photoColumn[$attribLoop]."_$i}>";
                        $this->valuesFromParameters[] = $dataPhotoValue;
                    }
                    $this->checkYamlKeyParamerterByClassValue($photoParameters, $attribut);
                    $this->checkYamlValueByAttributClass($dataPhotoValue, $attribut);
                    $attribLoop++;
                }
            }
        }  
        $this->checkYamlValueUnicityClass();
    }  
}

?>