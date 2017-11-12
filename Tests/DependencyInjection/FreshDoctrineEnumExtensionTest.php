<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
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

/**
 * FreshDoctrineEnumExtensionTest.
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class FreshDoctrineEnumExtensionTest extends TestCase
{
    /** @var FreshDoctrineEnumExtension */
    private $extension;

    /** @var ContainerBuilder */
    private $container;

    protected function setUp()
    {
        $this->extension = new FreshDoctrineEnumExtension();
        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    public function testLoadExtension()
    {
        // Add some dummy required parameter and service
        $this->container->setParameter('doctrine.dbal.connection_factory.types', []);
        $this->container->set('doctrine', new \stdClass());

        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        $this->assertFalse($this->container->findDefinition(ReadableEnumValueTwigExtension::class)->isPublic());
        $this->assertFalse($this->container->findDefinition(EnumConstantTwigExtension::class)->isPublic());
        $this->assertFalse($this->container->findDefinition(EnumTypeGuesser::class)->isPublic());
    }
}
