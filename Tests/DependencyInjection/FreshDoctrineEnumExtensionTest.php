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
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * FreshDoctrineEnumExtensionTest.
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class FreshDoctrineEnumExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FreshDoctrineEnumExtension $extension FreshDoctrineEnumExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder $container Container builder
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->extension = new FreshDoctrineEnumExtension();
        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    public function testLoadExtension()
    {
        // Add some dummy required parameter and service
        $this->container->setParameter('doctrine.dbal.connection_factory.types', null);
        $this->container->set('doctrine', new \StdClass());

        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        // Check that services have been loaded
        $this->assertTrue($this->container->has('twig.extension.readable_enum_value'));
        $this->assertTrue($this->container->has('twig.extension.enum_constant'));
        $this->assertTrue($this->container->has('enum_type_guesser'));
    }
}
