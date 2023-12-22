<?php 

namespace App\Tests\Fixtures;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class PhotoTest extends KernelTestCase
{
    private const LIMIT = 1;
    private const PARAMETERS_IDX = 'parameters';
    private const QUANTITY_IDX = 'quantity_0';
    private const QUANTITY = 0;

    private ContainerInterface $container;
    private PhotoRepository $photoRepository;
    private EntityManagerInterface $entityManager;
    private string $rootDir;
    private string $classEntityPathPhoto;


    protected function getContainerPhoto(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager();
        $this->rootDir = $this->container->getParameter('kernel.project_dir');
        $this->photoRepository = $this->container->get(PhotoRepository::class);   
        $this->classEntityPathPhoto = $this->photoRepository->getClassName();
    }

    public function testsPhotoSetUp() {
        $this->getContainerPhoto();
        $this->testPhotoYamlFixtures();
        $this->testPhotoData();
    }

    protected function testPhotoYamlFixtures(): void
    {
        $yamlFilePath = "$this->rootDir/fixtures/photo.yaml" ;
        $yamlContent = file_get_contents($yamlFilePath);
        $photoMetadata = $this->entityManager->getClassMetadata($this->classEntityPathPhoto);
        $itemKeys = $photoMetadata->getFieldNames();
        $photoColumn = $photoMetadata->getColumnNames(); 
        $parsedYaml = Yaml::parse($yamlContent);
        $photoParameters = $parsedYaml[PhotoTest::PARAMETERS_IDX];

        $this->assertNotFalse($yamlContent);
        $this->assertArrayHasKey(PhotoTest::PARAMETERS_IDX, $parsedYaml, "L'index des paramètres n'éxiste pas");
        $this->assertArrayHasKey($this->classEntityPathPhoto, $parsedYaml, "L'index de la classe n'éxiste pas");
        $this->assertArrayHasKey(PhotoTest::QUANTITY_IDX,$photoParameters, "L'index de la quantité n'éxiste pas");
        $this->assertEquals(PhotoTest::QUANTITY, $photoParameters[PhotoTest::QUANTITY_IDX], " La quantité n'est pas égale " . PhotoTest::QUANTITY);
            foreach ($photoParameters as $data) {
                    $i = 1;
                    $photoItemKey = "photo_".$i."{".$i."..".$i."}";
                    $photoClass = $parsedYaml[$this->classEntityPathPhoto];
                    $keysParameters = array_keys($photoParameters, $data);
                    $assertDataParam = $this->countIfMore($keysParameters, PhotoTest::LIMIT); 
                    $this->assertTrue($assertDataParam, "une valeur $data est dupliquée dans la fixture .Yaml dans la partie paramètre");

                    $this->assertArrayHasKey($photoItemKey, $photoClass, "la clé dans la classe photo_".$i."{".$i."..".$i." n'éxiste pas");
                    $item = $photoClass[$photoItemKey];
                    for($a = 0; $a < 3; $a++) {
                        $keyValue = str_replace(["<{", "}>"], "", $item[$itemKeys[$a+1]]);
                        $this->assertArrayHasKey($keyValue, $photoParameters,  "la valeur de l'index : $keyValue n'est pas présente dans les paramètres de la classe");
                        
                        $dataPhotoValue = ($a+1 !== 3 ) ? "<{" .$photoColumn[$a+1]. "_$i}>" : "<{" .PhotoTest::QUANTITY_IDX. "}>";
                        $this->assertEquals($dataPhotoValue, $item[$itemKeys[$a+1]], "la valeur du champ de la classe Photo : " . $item[$itemKeys[$a+1]]. "n'est pas identique à la valeur du fichier". $dataPhotoValue);
                    }
                    $i++;
                }
    }

    protected function testPhotoData(): void
    {
        $photos = $this->photoRepository->findAll(); 

        foreach ($photos as $photo) {
           $this->assertInstanceOf(Photo::class, $photo, "l'objet retournée ne provient pas de la classe Photo");
           
           $names = $this->photoRepository->findBy(['name' => $photo->getName()]);
           $assertNames = $this->countIfMore($names, PhotoTest::LIMIT); 
           $this->assertTrue($assertNames, "La valeur portant le nom'".$photo->getName()."' est dupliquée en BDD");
           
           $urlsCdn = $this->photoRepository->findBy(['urlCdn' => $photo->getUrlCdn()]); 
           $assertUrlsCdns = $this->countIfMore($urlsCdn, PhotoTest::LIMIT); 
           $this->assertTrue($assertUrlsCdns, "La valeur portant l'url de CDN '".$photo->getUrlCdn()."' est dupliquée en BDD");
        }
    }

    private function countIfMore(array $objects, int $valueCounted): bool
    {
        return (count($objects) === $valueCounted) ? true : false; 
    }
}

?>