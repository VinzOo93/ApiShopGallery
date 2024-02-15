<?php

namespace App\Tests\Base;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * TestBase.
 */
abstract class TestBase extends KernelTestCase
{
    protected ContainerInterface $container;
    protected EntityManagerInterface $entityManager;

    protected function initContainer(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager();
    }
}
