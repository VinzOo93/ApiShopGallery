<?php

namespace App\Tests\Base;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * TestBase
 */
abstract class TestBase extends KernelTestCase
{
    protected ContainerInterface $container;
    protected EntityManagerInterface $entityManager;

    /**
     *
     */
    protected function initContainer()
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager();
    }
}