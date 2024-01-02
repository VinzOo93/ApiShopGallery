<?php 

namespace App\Tests\Fixtures;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

abstract class DataTest extends KernelTestCase {

    private const LIMIT = 1;

    protected ContainerInterface $container;
    protected EntityManagerInterface $entityManager;
    protected string $rootDir;
    protected array $valuesFromParameters = [];

    protected function initContainer(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager();
        $this->rootDir = $this->container->getParameter('kernel.project_dir');
   
    }

    protected function checkDbUnicity(EntityRepository $repository, array $params): void
    {

        $data = $repository->findBy([$params['key'] => $params['value']]);
        $assertion = $this->countIfMore($data, DataTest::LIMIT); 
        
        $this->assertTrue($assertion, 
            "La valeur portant l'url de CDN '".$params['value']."' est dupliquée en BDD");
    
    }

    protected function checkYamlValueUnicityParameter(array $array, mixed $data): void 
    {

        $keysParameters = array_keys($array, $data);
        $assertion = $this->countIfMore($keysParameters, DataTest::LIMIT); 
        
        $this->assertTrue($assertion, 
        "une valeur $data est dupliquée dans la fixture partie");

    }

    protected function checkYamlValueUnicityClass()
    {
        $this->assertTrue(
            (count($this->valuesFromParameters) !== count(array_unique($this->valuesFromParameters))),
            "il y a des doublons dans la partie classe au miveau des clé des paramètres"
        );
    }

    protected function getYamlContent(string $yamlFileName): array
    {
        $yamlContent = file_get_contents("$this->rootDir/fixtures/$yamlFileName");
        $this->assertNotFalse($yamlContent);
        
        return Yaml::parse($yamlContent);
    }

    protected function checkYamlKeyParamerterByClassValue(array $array, mixed $value): void
    {
        
        $value = str_replace(["<{", "}>"], "", $value);
        $this->assertArrayHasKey($value, $array,
        "la valeur de l'index : '$value' n'est pas présente dans les paramètres de la classe");

    }

    protected function checkYamlValueByAttributClass(string $value, mixed $attribut): void 
    {
        $this->assertEquals($value, $attribut, 
        "la valeur du champ : $attribut n'est pas identique à la valeur 
        de la source : $value");
    }

    private function countIfMore(array $objects, int $valueCounted): bool
    {
        return (count($objects) === $valueCounted) ? true : false; 
    }


}

?>