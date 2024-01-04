<?php 

namespace App\Tests\Fixtures;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

abstract class DataTest extends KernelTestCase {

    private const LIMIT = 1;
   
    protected const PARAMETERS_IDX = 'parameters';

    protected ContainerInterface $container;
    protected EntityManagerInterface $entityManager;
    protected string $rootDir;
    protected string $itemKey;
    protected array $valuesFromParameters = [];
    protected array $parsedYaml = [];

    protected int $index = 0;

    private $classEntityPath;


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
        $assertion = $this->countIfMore($data, self::LIMIT); 
        
        $this->assertTrue(
            $assertion, 
            "La valeur portant l'url de CDN '".$params['value']."' est dupliquée en BDD");
    
    }

    protected function checkYamlValueUnicityClass(): void
    {
        $this->assertFalse(
            (count($this->valuesFromParameters) !== count(array_unique($this->valuesFromParameters))),
            "il y a des doublons dans la partie classe au niveau des clé des paramètres"
        );
    }

    protected function getYamlContent(string $yamlFileName): array
    {
        $yamlContent = file_get_contents("$this->rootDir/fixtures/$yamlFileName");
        $this->assertNotFalse($yamlContent);
        
        return Yaml::parse($yamlContent);
    }

    protected function assertYamlIsReadable($classEntityPath): void 
    {
        $this->assertArrayHasKey(
            self::PARAMETERS_IDX,
            $this->parsedYaml,
             "L'index des paramètres n'éxiste pas"
        );
        
        $this->assertArrayHasKey(
            $classEntityPath,
            $this->parsedYaml,
            "L'index de la classe n'éxiste pas"
        );
        
        $this->classEntityPath = $classEntityPath;
    }
    protected function checkParameterKeysAndValues(string $key, string $data, array $paramsClass): void
    {
        $this->checkYamlValueUnicityParameter($this->parsedYaml[self::PARAMETERS_IDX], $data);
        
        if ($this->shouldProcessKey($key)) {
            $classItem = $this->parsedYaml[$this->classEntityPath];

            $this->index = $this->getIndexFromString($key);
            $this->checkItemKeyExist($classItem, $this->classEntityPath);
            $this->checkParametersValuesOnClass($classItem[$this->itemKey], $paramsClass);
        } 
    }

    private function checkYamlValueUnicityParameter(array $array, mixed $data): void 
    {

        $keysParameters = array_keys($array, $data);
        $assertion = $this->countIfMore($keysParameters, self::LIMIT); 
        
        $this->assertTrue($assertion, 
        "une valeur $data est dupliquée dans la fixture partie");

    }

    private function checkParametersValuesOnClass(array $item, array $params): void
    {
        $columns = $this->entityManager->getClassMetadata($this->classEntityPath)->getColumnNames();
        $attribLoop = 1;
       

        foreach($item as $attribut) { 
            $dataValue = $params["dataValue"];   
            if ($attribut !== $item[$params["avoid"]]) {
                $dataValue = $this->registerClassValues($columns, $attribLoop);
            }
            $this->checkYamlKeyParameterByClassValue($params["parameters"], $attribut);
            $this->checkYamlValueByAttributClass($dataValue, $attribut);
            
            $attribLoop++;
        }  

    }

    private function registerClassValues(array $columns, int $index ): string
    {
        $value ="<{".$columns[$index]."_".$this->index."}>";
        $this->valuesFromParameters[] = $value;
        
        return $value;
    }

    private function checkYamlKeyParameterByClassValue(array $array, mixed $value): void
    {
        $value = str_replace(["<{", "}>"], "", $value);
        
        $this->assertArrayHasKey(
            $value,
            $array,
            "la valeur de l'index : '$value' n'est pas présente dans les paramètres de la classe"
            );
    }

    private function checkYamlValueByAttributClass(string $value, mixed $attribut): void 
    {
        $this->assertEquals(
            $value,
            $attribut, 
            "la valeur du champ : $attribut n'est pas identique à la valeur de la source : $value"
        );
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

    private function getIndexFromString(string $key): string 
    {
       return filter_var($key, FILTER_SANITIZE_NUMBER_INT);
    }

    private function shouldProcessKey(string $key): bool
    {
        return $this->index != $this->getIndexFromString($key);
    }

    private function checkItemKeyExist(array $arrayClass): void 
    {
        $this->itemKey = $this->getEndOfPath("\\", $this->classEntityPath)."_".$this->index."{".$this->index."..".$this->index."}";    
        
        $this->assertArrayHasKey(
            $this->itemKey,
            $arrayClass,
            "la clé dans la classe $this->itemKey n'éxiste pas"
        );
    }

}

?>