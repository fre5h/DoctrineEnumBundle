<?php
/*
 * This file is part of the FreshDoctrineEnumBundle.
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fresh\DoctrineEnumBundle\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\Persistence\ManagerRegistry;
use Fresh\DoctrineEnumBundle\Exception\InvalidArgumentException;
use Fresh\DoctrineEnumBundle\FreshDoctrineEnumBundle;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * FreshDoctrineEnumBundleTest
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class FreshDoctrineEnumBundleTest extends TestCase
{
    /** @var ContainerInterface|MockObject */
    private $container;

    /** @@var ManagerRegistry|MockObject */
    private $doctrine;

    protected function setUp(): void
    {
        $this->doctrine = $this->createMock(ManagerRegistry::class);
        $this->container = $this->createMock(ContainerInterface::class);
    }

    protected function tearDown(): void
    {
        unset(
            $this->container,
            $this->doctrine,
        );
    }

    public function testEnumMappingRegistration(): void
    {
        $this->container
            ->expects(self::once())
            ->method('get')
            ->with('doctrine', ContainerInterface::NULL_ON_INVALID_REFERENCE)
            ->willReturn($this->doctrine)
        ;

        /**
         * @var AbstractPlatform|MockObject $databasePlatformAbc
         * @var AbstractPlatform|MockObject $databasePlatformDef
         */
        $databasePlatformAbc = $this->getMockForAbstractClass(AbstractPlatform::class);
        $databasePlatformDef = $this->getMockForAbstractClass(AbstractPlatform::class);

        $connectionAbc = $this->createMock(Connection::class);

        $connectionAbc
            ->expects(self::once())
            ->method('getDatabasePlatform')
            ->willReturn($databasePlatformAbc)
        ;

        $connectionDef = $this->createMock(Connection::class);
        $connectionDef
            ->expects(self::once())
            ->method('getDatabasePlatform')
            ->willReturn($databasePlatformDef)
        ;

        $this->doctrine
            ->expects(self::once())
            ->method('getConnections')
            ->willReturn([$connectionAbc, $connectionDef])
        ;

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
        $this->container
            ->expects(self::once())
            ->method('get')
            ->with('doctrine', ContainerInterface::NULL_ON_INVALID_REFERENCE)
            ->willReturn($this->doctrine)
        ;

        /** @var AbstractPlatform|MockObject $databasePlatformAbc */
        $databasePlatformAbc = $this->getMockForAbstractClass(AbstractPlatform::class);

        $connectionAbc = $this->createMock(Connection::class);
        $connectionAbc
            ->expects(self::once())
            ->method('getDatabasePlatform')
            ->willReturn($databasePlatformAbc)
        ;

        $this->doctrine
            ->expects(self::once())
            ->method('getConnections')
            ->willReturn([$connectionAbc])
        ;

        $databasePlatformAbc->registerDoctrineTypeMapping('enum', 'string');

        $bundle = new FreshDoctrineEnumBundle();
        $bundle->setContainer($this->container);
        $bundle->boot();

        self::assertTrue($databasePlatformAbc->hasDoctrineTypeMappingFor('enum'));
        self::assertEquals('string', $databasePlatformAbc->getDoctrineTypeMapping('enum'));
    }

    public function testEnumMappingReregistrationToString(): void
    {
        $this->container
            ->expects(self::once())
            ->method('get')
            ->with('doctrine', ContainerInterface::NULL_ON_INVALID_REFERENCE)
            ->willReturn($this->doctrine)
        ;

        /** @var AbstractPlatform|MockObject $databasePlatformAbc */
        $databasePlatformAbc = $this->getMockForAbstractClass(AbstractPlatform::class);

        $connectionAbc = $this->createMock(Connection::class);

        $connectionAbc
            ->expects(self::once())
            ->method('getDatabasePlatform')
            ->willReturn($databasePlatformAbc)
        ;

        $this->doctrine
            ->expects(self::once())
            ->method('getConnections')
            ->willReturn([$connectionAbc])
        ;

        $databasePlatformAbc->registerDoctrineTypeMapping('enum', 'boolean');

        $bundle = new FreshDoctrineEnumBundle();
        $bundle->setContainer($this->container);
        $bundle->boot();

        self::assertTrue($databasePlatformAbc->hasDoctrineTypeMappingFor('enum'));
        self::assertEquals('string', $databasePlatformAbc->getDoctrineTypeMapping('enum'));
    }

    public function testMissedDoctrine(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects(self::once())
            ->method('get')
            ->with('doctrine', ContainerInterface::NULL_ON_INVALID_REFERENCE)
            ->willReturn(null)
        ;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Service "doctrine" is missed in container');

        $bundle = new FreshDoctrineEnumBundle();
        $bundle->setContainer($container);
        $bundle->boot();
    }
}
