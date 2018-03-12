<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Tests\DependencyInjection;

use Fresh\DoctrineEnumBundle\DependencyInjection\FreshDoctrineEnumExtension;
use Fresh\DoctrineEnumBundle\Form\EnumTypeGuesser;
use Fresh\DoctrineEnumBundle\Twig\Extension\EnumConstantTwigExtension;
use Fresh\DoctrineEnumBundle\Twig\Extension\ReadableEnumValueTwigExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * FreshDoctrineEnumExtensionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class FreshDoctrineEnumExtensionTest extends TestCase
{
    /** @var FreshDoctrineEnumExtension */
    private $extension;

    /** @var ContainerBuilder */
    private $container;

    protected function setUp(): void
    {
        $this->extension = new FreshDoctrineEnumExtension();
        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    protected function tearDown(): void
    {
        unset($this->extension, $this->container);
    }

    public function testLoadExtension(): void
    {
        $this->container->setParameter('doctrine.dbal.connection_factory.types', []); // Just add a dummy required parameter
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        self::assertArrayHasKey(EnumTypeGuesser::class, $this->container->getRemovedIds());
        self::assertArrayHasKey(ReadableEnumValueTwigExtension::class, $this->container->getRemovedIds());
        self::assertArrayHasKey(EnumConstantTwigExtension::class, $this->container->getRemovedIds());

        self::assertArrayNotHasKey(EnumTypeGuesser::class, $this->container->getDefinitions());
        self::assertArrayNotHasKey(ReadableEnumValueTwigExtension::class, $this->container->getDefinitions());
        self::assertArrayNotHasKey(EnumConstantTwigExtension::class, $this->container->getDefinitions());

        $this->expectException(ServiceNotFoundException::class);
        $this->container->get(EnumTypeGuesser::class);
        $this->container->get(ReadableEnumValueTwigExtension::class);
        $this->container->get(EnumConstantTwigExtension::class);
    }
}
