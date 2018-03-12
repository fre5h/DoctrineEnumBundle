<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Tests\Validator;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Fresh\DoctrineEnumBundle\FreshDoctrineEnumBundle;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * FreshDoctrineEnumBundleTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class FreshDoctrineEnumBundleTest extends TestCase
{
    /** @var Container|MockObject */
    private $container;

    /** @@var Registry|MockObject */
    private $doctrine;

    protected function setUp(): void
    {
        $this->container = $this->getMockBuilder(Container::class)
                                ->disableOriginalConstructor()
                                ->setMethods(['get'])
                                ->getMock();

        $this->doctrine = $this->getMockBuilder(Registry::class)
                               ->disableOriginalConstructor()
                               ->setMethods(['getConnections'])
                               ->getMock();

        $this->container->expects($this->once())
                        ->method('get')
                        ->with('doctrine')
                        ->willReturn($this->doctrine);

    }

    protected function tearDown(): void
    {
        unset($this->container, $this->doctrine);
    }

    public function testEnumMappingRegistration(): void
    {
        /**
         * @var AbstractPlatform|MockObject $databasePlatformAbc
         * @var AbstractPlatform|MockObject $databasePlatformDef
         */
        $databasePlatformAbc = $this->getMockForAbstractClass(AbstractPlatform::class);
        $databasePlatformDef = $this->getMockForAbstractClass(AbstractPlatform::class);

        $connectionAbc = $this->getMockBuilder(Connection::class)
                              ->disableOriginalConstructor()
                              ->setMethods(['getDatabasePlatform'])
                              ->getMock();

        $connectionAbc->expects($this->once())
                      ->method('getDatabasePlatform')
                      ->willReturn($databasePlatformAbc);

        $connectionDef = $this->getMockBuilder(Connection::class)
                              ->disableOriginalConstructor()
                              ->setMethods(['getDatabasePlatform'])
                              ->getMock();

        $connectionDef->expects($this->once())
                      ->method('getDatabasePlatform')
                      ->willReturn($databasePlatformDef);

        $this->doctrine->expects($this->once())
                       ->method('getConnections')
                       ->willReturn([$connectionAbc, $connectionDef]);

        $bundle = new FreshDoctrineEnumBundle();
        $bundle->setContainer($this->container);
        $bundle->boot();

        self::assertTrue($databasePlatformAbc->hasDoctrineTypeMappingFor('enum'));
        self::assertEquals('string', $databasePlatformAbc->getDoctrineTypeMapping('enum'));

        self::assertTrue($databasePlatformDef->hasDoctrineTypeMappingFor('enum'));
        self::assertEquals('string', $databasePlatformDef->getDoctrineTypeMapping('enum'));
    }

    public function testAlreadyRegisteredEnumMapping(): void
    {
        /** @var AbstractPlatform|MockObject $databasePlatformAbc */
        $databasePlatformAbc = $this->getMockForAbstractClass(AbstractPlatform::class);

        $connectionAbc = $this->getMockBuilder(Connection::class)
                              ->disableOriginalConstructor()
                              ->setMethods(['getDatabasePlatform'])
                              ->getMock();

        $connectionAbc->expects($this->once())
                      ->method('getDatabasePlatform')
                      ->willReturn($databasePlatformAbc);

        $this->doctrine->expects($this->once())
                       ->method('getConnections')
                       ->willReturn([$connectionAbc]);

        $databasePlatformAbc->registerDoctrineTypeMapping('enum', 'string');

        $bundle = new FreshDoctrineEnumBundle();
        $bundle->setContainer($this->container);
        $bundle->boot();

        self::assertTrue($databasePlatformAbc->hasDoctrineTypeMappingFor('enum'));
        self::assertEquals('string', $databasePlatformAbc->getDoctrineTypeMapping('enum'));
    }

    public function testEnumMappingReregistrationToString(): void
    {
        /** @var AbstractPlatform|MockObject $databasePlatformAbc */
        $databasePlatformAbc = $this->getMockForAbstractClass(AbstractPlatform::class);

        $connectionAbc = $this->getMockBuilder(Connection::class)
                              ->disableOriginalConstructor()
                              ->setMethods(['getDatabasePlatform'])
                              ->getMock();

        $connectionAbc->expects($this->once())
                      ->method('getDatabasePlatform')
                      ->willReturn($databasePlatformAbc);

        $this->doctrine->expects($this->once())
                       ->method('getConnections')
                       ->willReturn([$connectionAbc]);

        $databasePlatformAbc->registerDoctrineTypeMapping('enum', 'boolean');

        $bundle = new FreshDoctrineEnumBundle();
        $bundle->setContainer($this->container);
        $bundle->boot();

        self::assertTrue($databasePlatformAbc->hasDoctrineTypeMappingFor('enum'));
        self::assertEquals('string', $databasePlatformAbc->getDoctrineTypeMapping('enum'));
    }
}
