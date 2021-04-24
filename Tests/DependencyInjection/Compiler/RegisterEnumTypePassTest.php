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

namespace Fresh\DoctrineEnumBundle\Tests\DependencyInjection\Compiler;

use Doctrine\Persistence\ManagerRegistry;
use Fresh\DoctrineEnumBundle\DependencyInjection\Compiler\RegisterEnumTypePass;
use Fresh\DoctrineEnumBundle\Exception\InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;

/**
 * RegisterEnumTypePassTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class RegisterEnumTypePassTest extends TestCase
{
    /** @var ContainerBuilder|MockObject */
    private $containerBuilder;

    /** @var ManagerRegistry|MockObject */
    private $managerRegistry;

    /** @var RegisterEnumTypePass */
    private $registerEnumTypePass;

    protected function setUp(): void
    {
        $this->containerBuilder = $this->createMock(ContainerBuilder::class);
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->registerEnumTypePass = new RegisterEnumTypePass();
    }

    protected function tearDown(): void
    {
        unset(
            $this->containerBuilder,
            $this->managerRegistry,
            $this->registerEnumTypePass,
        );
    }

    public function testProcessSuccessful(): void
    {
        $this->containerBuilder
            ->expects(self::once())
            ->method('get')
            ->with('doctrine', ContainerInterface::NULL_ON_INVALID_REFERENCE)
            ->willReturn($this->managerRegistry)
        ;

        $this->managerRegistry
            ->expects(self::once())
            ->method('getConnectionNames')
            ->willReturn(['default', 'custom1', 'custom2'])
        ;

        $default = $this->createMock(Definition::class);
        $default
            ->expects(self::once())
            ->method('getArgument')
            ->with(3)
            ->willReturn(['test' => '_test'])
        ;
        $default
            ->expects(self::once())
            ->method('setArgument')
            ->with(3, ['test' => '_test', 'enum' => 'string'])
        ;

        $custom1 = $this->createMock(Definition::class);
        $custom1
            ->expects(self::once())
            ->method('getArgument')
            ->with(3)
            ->willReturn(['test' => '_test', 'enum' => '_test'])
        ;
        $custom1
            ->expects(self::once())
            ->method('setArgument')
            ->with(3, ['test' => '_test', 'enum' => 'string'])
        ;

        $custom2 = $this->createMock(Definition::class);
        $custom2
            ->expects(self::once())
            ->method('getArgument')
            ->with(3)
            ->willReturn(['test' => '_test', 'enum' => 'string'])
        ;
        $custom2
            ->expects(self::never())
            ->method('setArgument')
        ;

        $this->containerBuilder
            ->expects(self::exactly(3))
            ->method('getDefinition')
            ->withConsecutive(['default'], ['custom1'], ['custom2'])
            ->willReturnOnConsecutiveCalls($default, $custom1, $custom2)
        ;

        $this->registerEnumTypePass->process($this->containerBuilder);
    }

    public function testMissingDoctrine(): void
    {
        $this->containerBuilder
            ->expects(self::once())
            ->method('get')
            ->with('doctrine', ContainerInterface::NULL_ON_INVALID_REFERENCE)
            ->willReturn(null)
        ;

        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('Service "doctrine" is missed in container');

        $this->registerEnumTypePass->process($this->containerBuilder);
    }
}
