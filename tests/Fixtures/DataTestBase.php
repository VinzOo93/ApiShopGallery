<?php

namespace App\Tests\Fixtures;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * DataTest
 */
abstract class DataTestBase extends KernelTestCase
{
    private const LIMIT = 1;

    protected const PARAMETERS_IDX = 'parameters';

    protected ContainerInterface $container;
    protected EntityManagerInterface $entityManager;
    protected string $rootDir;
    protected string $itemKey;
    protected int $index = 0;



    /**
     * @var array<int, string> $valuesFromParameters
     */
    protected array $valuesFromParameters = [];

    /**
     * @var array<string, array<mixed>> $parsedYaml
     */
    protected array $parsedYaml = [];


    /**
     * @var string
     */
    private $classEntityPath;

    /**
     *
     */
    protected function initContainer(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager();
        $this->rootDir = $this->container->getParameter('kernel.project_dir');
    }

    /**
     * @param EntityRepository $repository
     * @param array<string, string> $params
     */
    protected function checkDbUnicity(EntityRepository $repository, array $params): void
    {
        $data = $repository->findBy([$params['key'] => $params['value']]);
        $assertion = $this->countIfMore($data, self::LIMIT);

        $this->assertTrue(
            $assertion,
            "La valeur portant l'url de CDN '" . $params['value'] . "' est dupliquée en BDD"
        );
    }

    /**
     *
     */
    protected function checkYamlValueUnicityClass(): void
    {
        $this->assertFalse(
            count($this->valuesFromParameters) !== count(array_unique($this->valuesFromParameters)),
            'il y a des doublons dans la partie classe au niveau des clé des paramètres'
        );
    }

    /**
     * @param string $yamlFileName
     * @return mixed
     */
    protected function getYamlContent(string $yamlFileName): mixed
    {
        $yamlContent = file_get_contents("$this->rootDir/fixtures/$yamlFileName");
        $this->assertNotFalse($yamlContent);

        return Yaml::parse($yamlContent);
    }

    /**
     * @param string $classEntityPath
     */
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
     * @param string $key
     * @param string $data
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
     * @param mixed $data
     */
    private function checkYamlValueUnicityParameter(array $array, mixed $data): void
    {
        $keysParameters = array_keys($array, $data);
        $assertion = $this->countIfMore($keysParameters, self::LIMIT);

        $this->assertTrue(
            $assertion,
            "une valeur $data est dupliquée dans la fixture partie"
        );
    }

    /**
     * @param array<int, string> $item
     * @param array<string, mixed> $params
     */
    private function checkParametersValuesOnClass(array $item, array $params): void
    {
        $columns = $this->entityManager->getClassMetadata($this->classEntityPath)->getColumnNames();
        $attribLoop = 1;

        foreach ($item as $attribut) {
            $dataValue = $params['dataValue'];
            if ($attribut !== $item[$params['avoid']]) {
                $dataValue = $this->registerClassValues($columns, $attribLoop);
            }
            $this->checkYamlKeyParameterByClassValue($this->parsedYaml[self::PARAMETERS_IDX], $attribut);
            $this->checkYamlValueByAttributClass($dataValue, $attribut);

            ++$attribLoop;
        }
    }

    /**
     * @param array<int, string> $columns
     * @param int $index
     * @return string
     */
    private function registerClassValues(array $columns, int $index): string
    {
        $value = '<{' . $columns[$index] . '_' . $this->index . '}>';
        $this->valuesFromParameters[] = $value;

        return $value;
    }

    /**
     * @param array<string, mixed> $array
     * @param mixed $value
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

    /**
     * @param string $value
     * @param mixed $attribut
     */
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
     * @param int $valueCounted
     * @return bool
     */
    private function countIfMore(array $objects, int $valueCounted): bool
    {
        return count($objects) === $valueCounted;
    }

    /**
     * @param string $haystack
     * @param string $path
     * @return string
     */
    private function getEndOfPath(string $haystack, string $path): string
    {
        $arrPath = explode($haystack, $path);

        return strtolower(end($arrPath));
    }

    /**
     * @param string $key
     * @return mixed
     */
    private function getIndexFromString(string $key): mixed
    {
        return filter_var($key, FILTER_SANITIZE_NUMBER_INT);
    }
    /**
     * @param string $key
     */
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
            '\\',
            $this->classEntityPath
        ) . '_' . $this->index . '{' . $this->index . '..' . $this->index . '}';

        $this->assertArrayHasKey(
            $this->itemKey,
            $arrayClass,
            "la clé dans la classe $this->itemKey n'éxiste pas"
        );
    }
}
