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
use PHPUnit\Framework\Attributes\Test;
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
    private ContainerBuilder|MockObject $containerBuilder;

    /** @var ManagerRegistry|MockObject */
    private ManagerRegistry|MockObject $managerRegistry;

    private RegisterEnumTypePass $registerEnumTypePass;

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

    #[Test]
    public function processSuccessful(): void
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

        $matcher = $this->exactly(2);

        $this->containerBuilder
            ->expects(self::exactly(3))
            ->method('getDefinition')
            ->willReturnCallback(function () use ($matcher) {
                return match ($matcher->numberOfInvocations()) {
                    1 => ['default'],
                    2 => ['custom1'],
                    3 => ['custom2'],
                };
            })
            ->willReturnOnConsecutiveCalls($default, $custom1, $custom2)
        ;

        $this->registerEnumTypePass->process($this->containerBuilder);
    }

    #[Test]
    public function missingDoctrine(): void
    {
        $this->containerBuilder
            ->expects(self::once())
            ->method('get')
            ->with('doctrine', ContainerInterface::NULL_ON_INVALID_REFERENCE)
            ->willReturn(null)
        ;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Service "doctrine" is missed in container');

        $this->registerEnumTypePass->process($this->containerBuilder);
    }
}
