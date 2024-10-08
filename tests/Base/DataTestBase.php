<?php

namespace App\Tests\Base;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Yaml;

/**
 * DataTest.
 */
abstract class DataTestBase extends TestBase
{
    private const LIMIT = 1;

    protected const PARAMETERS_IDX = 'parameters';

    protected string $attribPrefix;
    protected string $rootDir;
    protected string $itemKey;
    protected int $index = 0;

    /**
     * @var array<int, string>
     */
    protected array $valuesFromParameters = [];

    /**
     * @var array<string, mixed>
     */
    protected array $parsedYaml = [];

    private string $classEntityPath;

    protected function initContainerDataBase(): void
    {
        $this->initContainer();
        $this->rootDir = $this->container->getParameter('kernel.project_dir');
    }

    /**
     * @param array<string, string> $params
     */
    protected function checkDbUnicity(EntityRepository $repository, array $params): void
    {
        $data = $repository->findBy([$params['key'] => $params['value']]);
        $assertion = $this->countIfMore($data, self::LIMIT);

        $this->assertTrue(
            $assertion,
            "La valeur portant l'url de CDN '".$params['value']."' est dupliquée en BDD"
        );
    }

    protected function checkYamlValueUnicityClass(): void
    {
        $this->assertFalse(
            count($this->valuesFromParameters) !== count(array_unique($this->valuesFromParameters)),
            'il y a des doublons dans la partie classe au niveau des clé des paramètres'
        );
    }

    protected function getYamlContent(string $yamlFileName): mixed
    {
        $yamlContent = file_get_contents("$this->rootDir/fixtures/$yamlFileName");
        $this->assertNotFalse($yamlContent);

        return Yaml::parse($yamlContent);
    }

    protected function assertYamlIsReadable(string $classEntityPath): void
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

    /**
     * @param array<string ,mixed> $paramsClass
     */
    protected function checkParameterKeysAndValues(string $key, string $data, array $paramsClass): void
    {
        $this->checkYamlValueUnicityParameter($this->parsedYaml[self::PARAMETERS_IDX], $data);

        if ($this->shouldProcessKey($key)) {
            $classItem = $this->parsedYaml[$this->classEntityPath];

            $this->index = $this->getIndexFromString($key);
            $this->checkItemKeyExist($classItem);
            $this->checkParametersValuesOnClass($classItem[$this->itemKey], $paramsClass);
        }
    }

    /**
     * @param array<string, mixed> $array
     */
    private function checkYamlValueUnicityParameter(array $array, mixed $data): void
    {
        $keysParameters = array_keys($array, $data);
        $assertion = $this->countIfMore($keysParameters, self::LIMIT);

        $this->assertTrue(
            $assertion,
            "une valeur $data est dupliquée dans la fixture partie classe"
        );
    }

    /**
     * @param array<int, string>   $item
     * @param array<string, mixed> $params
     */
    private function checkParametersValuesOnClass(array $item, array $params): void
    {
        $columns = $this->entityManager->getClassMetadata($this->classEntityPath)->getColumnNames();
        $attribLoop = 1;

        foreach ($item as $attribut) {
            $dataValue = $params['dataValue'];
            if ($attribut !== $this->checkAvoiding($params, $item)) {
                $dataValue = $this->registerClassValues($columns, $attribLoop);
            }
            $this->checkYamlKeyParameterByClassValue($this->parsedYaml[self::PARAMETERS_IDX], $attribut);
            $this->checkYamlValueByAttributClass($dataValue, $attribut);

            ++$attribLoop;
        }
    }

    /**
     * checkavoiding.
     *
     * @param array<string, mixed> $params
     * @param array<int, mixed>    $item
     */
    private function checkAvoiding(array $params, array $item): mixed
    {
        return (array_key_exists('avoid', $params)) ? ($item[$params['avoid']]) : false;
    }

    /**
     * @param array<int, string> $columns
     */
    private function registerClassValues(array $columns, int $index): string
    {
        $value = '<{'.$columns[$index].$this->attribPrefix.'_'.$this->index.'}>';
        $this->valuesFromParameters[] = $value;

        return $value;
    }

    /**
     * @param array<string, mixed> $array
     */
    private function checkYamlKeyParameterByClassValue(array $array, mixed $value): void
    {
        $value = str_replace(['<{', '}>'], '', $value);

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

    /**
     * @param array<int, mixed> $objects
     */
    private function countIfMore(array $objects, int $valueCounted): bool
    {
        return count($objects) === $valueCounted;
    }

    private function getEndOfPath(string $path): string
    {
        $arrPath = explode('\\', $path);

        return strtolower(end($arrPath));
    }

    private function getIndexFromString(string $key): mixed
    {
        return filter_var($key, FILTER_SANITIZE_NUMBER_INT);
    }

    private function shouldProcessKey(string $key): bool
    {
        return $this->index != $this->getIndexFromString($key);
    }

    /**
     * @param array<int, string> $arrayClass
     */
    private function checkItemKeyExist(array $arrayClass): void
    {
        $this->itemKey = $this->getEndOfPath(
            $this->classEntityPath
        ).'_'.$this->index.'{'.$this->index.'..'.$this->index.'}';
        $this->assertArrayHasKey(
            $this->itemKey,
            $arrayClass,
            "la clé dans la classe $this->itemKey n'éxiste pas"
        );
    }
}
