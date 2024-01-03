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
    protected string $itemKey;
    protected array $valuesFromParameters = [];
    protected int $index = 0;

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

    protected function checkItemKeyExist(array $arrayClass, string $classPath): void 
    {
        $this->itemKey = $this->getEndOfPath("\\", $classPath)."_".$this->index."{".$this->index."..".$this->index."}";    
        $this->assertArrayHasKey($this->itemKey, $arrayClass,
         "la clé dans la classe $this->itemKey n'éxiste pas"
        );
    }

    protected function checkYamlValueUnicityParameter(array $array, mixed $data): void 
    {

        $keysParameters = array_keys($array, $data);
        $assertion = $this->countIfMore($keysParameters, DataTest::LIMIT); 
        
        $this->assertTrue($assertion, 
        "une valeur $data est dupliquée dans la fixture partie");

    }

    protected function checkYamlValueUnicityClass(): void
    {
        $this->assertFalse(
            (count($this->valuesFromParameters) !== count(array_unique($this->valuesFromParameters))),
            "il y a des doublons dans la partie classe au niveau des clé des paramètres"
        );
    }

    protected function registerClassValues(array $columns, int $index ): string
    {
        $value ="<{".$columns[$index]."_".$this->index."}>";
        $this->valuesFromParameters[] = $value;
        
        return $value;
    }

    protected function shouldProcessKey(string $key): bool
    {
        return $this->index != filter_var($key, FILTER_SANITIZE_NUMBER_INT);
    }

    protected function getYamlContent(string $yamlFileName): array
    {
        $yamlContent = file_get_contents("$this->rootDir/fixtures/$yamlFileName");
        $this->assertNotFalse($yamlContent);
        
        return Yaml::parse($yamlContent);
    }

    protected function checkYamlKeyParameterByClassValue(array $array, mixed $value): void
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
        return (count($objects) === $valueCounted); 
    }

    private function getEndOfPath(string $haystack, string $path): string 
    {
        $arrPath = explode($haystack, $path);
        return strtolower(end($arrPath));
    }


}

?>