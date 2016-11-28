<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Tests\Validator;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Fresh\DoctrineEnumBundle\FreshDoctrineEnumBundle;
use Symfony\Component\DependencyInjection\Container;

/**
 * FreshDoctrineEnumBundleTest.
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class FreshDoctrineEnumBundleTest extends \PHPUnit_Framework_TestCase
{
    /** @var Container|\PHPUnit_Framework_MockObject_MockObject */
    private $container;

    /** @@var AbstractPlatform|\PHPUnit_Framework_MockObject_MockObject */
    private $databasePlatform;

    protected function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
                                ->disableOriginalConstructor()
                                ->setMethods(['has', 'get', 'getDatabasePlatform'])
                                ->getMock();

        $this->container->expects($this->any())
                        ->method('get')
                        ->will($this->returnSelf());

        $this->databasePlatform = $this->getMockForAbstractClass('Doctrine\DBAL\Platforms\AbstractPlatform');
    }

    protected function tearDown()
    {
        unset($this->container);
        unset($this->databasePlatform);
    }

    public function testEnumMappingRegistration()
    {
        $this->container->expects($this->once())
                        ->method('has')
                        ->with('doctrine.dbal.default_connection')
                        ->willReturn(true);

        $this->container->expects($this->once())
                        ->method('getDatabasePlatform')
                        ->willReturn($this->databasePlatform);

        $bundle = new FreshDoctrineEnumBundle();
        $bundle->setContainer($this->container);
        $bundle->boot();

        $this->assertTrue($this->databasePlatform->hasDoctrineTypeMappingFor('enum'));
        $this->assertEquals('string', $this->databasePlatform->getDoctrineTypeMapping('enum'));
    }

    public function testAlreadyRegisteredEnumMapping()
    {
        $this->databasePlatform->registerDoctrineTypeMapping('enum', 'string');

        $this->container->expects($this->once())
                        ->method('has')
                        ->with('doctrine.dbal.default_connection')
                        ->willReturn(true);

        $this->container->expects($this->once())
                        ->method('getDatabasePlatform')
                        ->willReturn($this->databasePlatform);

        $bundle = new FreshDoctrineEnumBundle();
        $bundle->setContainer($this->container);
        $bundle->boot();

        $this->assertTrue($this->databasePlatform->hasDoctrineTypeMappingFor('enum'));
        $this->assertEquals('string', $this->databasePlatform->getDoctrineTypeMapping('enum'));
    }

    public function testEnumMappingReregistrationToString()
    {
        $this->databasePlatform->registerDoctrineTypeMapping('enum', 'boolean');

        $this->container->expects($this->once())
                        ->method('has')
                        ->with('doctrine.dbal.default_connection')
                        ->willReturn(true);

        $this->container->expects($this->once())
                        ->method('getDatabasePlatform')
                        ->willReturn($this->databasePlatform);

        $bundle = new FreshDoctrineEnumBundle();
        $bundle->setContainer($this->container);
        $bundle->boot();

        $this->assertTrue($this->databasePlatform->hasDoctrineTypeMappingFor('enum'));
        $this->assertEquals('string', $this->databasePlatform->getDoctrineTypeMapping('enum'));
    }

    public function testEnumMappingForRenamedDefaultConnection()
    {
        $this->container->expects($this->once())
                        ->method('has')
                        ->with('doctrine.dbal.default_connection')
                        ->willReturn(false);

        $this->container->expects($this->never())
                        ->method('getDatabasePlatform')
                        ->willReturn($this->databasePlatform);

        $bundle = new FreshDoctrineEnumBundle();
        $bundle->setContainer($this->container);
        $bundle->boot();

        $this->assertFalse($this->databasePlatform->hasDoctrineTypeMappingFor('enum'));
        $this->expectException('Doctrine\DBAL\DBALException');
        $this->assertEquals('string', $this->databasePlatform->getDoctrineTypeMapping('enum'));
    }
}
