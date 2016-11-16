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

    protected function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
                                ->disableOriginalConstructor()
                                ->setMethods(['get', 'getDatabasePlatform'])
                                ->getMock();

        $this->container->expects($this->any())
                        ->method('get')
                        ->will($this->returnSelf());
    }

    protected function tearDown()
    {
        unset($this->container);
    }

    public function testEnumMappingRegistration()
    {
        /** @var AbstractPlatform $databasePlatform */
        $databasePlatform = $this->getMockForAbstractClass('Doctrine\DBAL\Platforms\AbstractPlatform');

        $this->container->expects($this->once())
                        ->method('getDatabasePlatform')
                        ->willReturn($databasePlatform);

        $bundle = new FreshDoctrineEnumBundle();
        $bundle->setContainer($this->container);
        $bundle->boot();

        $this->assertTrue($databasePlatform->hasDoctrineTypeMappingFor('enum'));
        $this->assertEquals('string', $databasePlatform->getDoctrineTypeMapping('enum'));
    }

    public function testAlreadyRegisteredEnumMapping()
    {
        /** @var AbstractPlatform|\PHPUnit_Framework_MockObject_MockObject */
        $databasePlatform = $this->getMockForAbstractClass('Doctrine\DBAL\Platforms\AbstractPlatform');
        $databasePlatform->registerDoctrineTypeMapping('enum', 'string');

        $this->container->expects($this->once())
                        ->method('getDatabasePlatform')
                        ->willReturn($databasePlatform);

        $bundle = new FreshDoctrineEnumBundle();
        $bundle->setContainer($this->container);
        $bundle->boot();

        $this->assertTrue($databasePlatform->hasDoctrineTypeMappingFor('enum'));
        $this->assertEquals('string', $databasePlatform->getDoctrineTypeMapping('enum'));
    }

    public function testEnumMappingReregistrationToString()
    {
        /** @var AbstractPlatform|\PHPUnit_Framework_MockObject_MockObject */
        $databasePlatform = $this->getMockForAbstractClass('Doctrine\DBAL\Platforms\AbstractPlatform');
        $databasePlatform->registerDoctrineTypeMapping('enum', 'boolean');

        $this->container->expects($this->once())
                        ->method('getDatabasePlatform')
                        ->willReturn($databasePlatform);

        $bundle = new FreshDoctrineEnumBundle();
        $bundle->setContainer($this->container);
        $bundle->boot();

        $this->assertTrue($databasePlatform->hasDoctrineTypeMappingFor('enum'));
        $this->assertEquals('string', $databasePlatform->getDoctrineTypeMapping('enum'));
    }
}
