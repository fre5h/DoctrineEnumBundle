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

namespace Fresh\DoctrineEnumBundle\Tests\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\Persistence\ManagerRegistry;
use Fresh\DoctrineEnumBundle\Command\EnumDropCommentCommand;
use Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsRegisteredButClassDoesNotExistException;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\TaskStatusType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class EnumDropCommentCommandTest extends TestCase
{
    /** @var EntityManagerInterface|MockObject */
    private $em;

    /** @var ManagerRegistry|MockObject */
    private $registry;

    /** @var Connection|MockObject */
    private $connection;

    /** @var AbstractPlatform|MockObject */
    private $platform;

    private Command $command;

    private ClassMetadataFactory $metadataFactory;

    private Application $application;

    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->metadataFactory = $this->createMock(ClassMetadataFactory::class);

        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->em->method('getConnection')->willReturn($this->connection);
        $this->em->method('getMetadataFactory')->willReturn($this->metadataFactory);

        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->registry->method('getManager')->willReturn($this->em);

        $command = new EnumDropCommentCommand(
            $this->registry,
            [
                'BasketballPositionType' => ['class' => BasketballPositionType::class],
                'TaskStatusType' => ['class' => TaskStatusType::class],
            ],
            'doctrine:enum:drop-comment'
        );

        $this->application = new Application();
        $this->application->add($command);

        $this->command = $this->application->find('doctrine:enum:drop-comment');
        $this->commandTester = new CommandTester($this->command);
    }

    protected function tearDown(): void
    {
        unset(
            $this->em,
            $this->registry,
            $this->connection,
            $this->platform,
            $this->metadataFactory,
            $this->command,
            $this->application,
            $this->commandTester,
        );
    }

    public function testExceptionInConstructor(): void
    {
        $this->expectException(EnumTypeIsRegisteredButClassDoesNotExistException::class);
        $this->expectErrorMessage('ENUM type "CustomType" is registered as "Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\CustomType", but that class does not exist');

        new EnumDropCommentCommand(
            $this->registry,
            [
                'CustomType' => ['class' => 'Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\CustomType'],
                'TaskStatusType' => ['class' => TaskStatusType::class],
            ]
        );

        $this->commandTester->getDisplay();
    }

    public function testExceptionOnExecution(): void
    {
        $this->em
            ->expects(self::once())
            ->method('getConnection')
            ->willThrowException(new \Exception('test', 5))
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'enumType' => 'TaskStatusType',
            ]
        );
        self::assertSame(5, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('test', $output);
    }

    public function testInvalidEnumTypeArgument(): void
    {
        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'enumType' => null,
            ]
        );
        self::assertSame(1, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Argument "enumType" is not a string.', $output);
    }

    public function testExceptionNotRegisteredEnumType(): void
    {
        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'enumType' => 'UnknownType',
            ]
        );
        self::assertSame(2, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Argument "enumType" is not a registered ENUM type.', $output);
    }

    public function testMissingDatabasePlatformForConnection(): void
    {
        $this->connection
            ->expects(self::once())
            ->method('getDatabasePlatform')
            ->willReturn(null)
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'enumType' => 'TaskStatusType',
            ]
        );
        self::assertSame(3, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Missing database platform for connection.', $output);
    }

    public function testExecutionWithCaughtException(): void
    {
        $this->connection
            ->expects(self::once())
            ->method('getDatabasePlatform')
            ->willThrowException(new \Exception('test', 5))
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'enumType' => 'TaskStatusType',
            ]
        );
        self::assertSame(5, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('test', $output);
    }

    public function testSuccessfulExecutionWithNoMetadata(): void
    {
        $this->connection
            ->expects(self::once())
            ->method('getDatabasePlatform')
            ->willReturn($this->platform)
        ;

        $this->metadataFactory
            ->expects(self::once())
            ->method('getAllMetadata')
            ->willReturn([])
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'enumType' => 'TaskStatusType',
                '--em' => 'default',
            ]
        );
        self::assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Dropping comments for TaskStatusType type...', $output);
        self::assertStringContainsString('NO METADATA FOUND', $output);
    }

    public function testSuccessfulExecutionWithMetadata(): void
    {
        $this->connection
            ->expects(self::once())
            ->method('getDatabasePlatform')
            ->willReturn($this->platform)
        ;

        $metadata = $this->createMock(ClassMetadata::class);
        $this->metadataFactory
            ->expects(self::once())
            ->method('getAllMetadata')
            ->willReturn([$metadata])
        ;

        $metadata->expects(self::once())->method('getName')->willReturn('Task');
        $metadata->expects(self::once())->method('getTableName')->willReturn('tasks');
        $metadata->expects(self::once())->method('getFieldNames')->willReturn(['status']);
        $metadata->expects(self::once())->method('getTypeOfField')->with('status')->willReturn('TaskStatusType');
        $metadata->expects(self::once())->method('getFieldMapping')->with('status')->willReturn(['columnName' => 'task_column_name']);

        $this->platform->expects(self::once())->method('getCommentOnColumnSQL')->with('tasks', 'task_column_name', null)->willReturn('test SQL');

        $this->connection->expects(self::once())->method('executeQuery')->with('test SQL');

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'enumType' => 'TaskStatusType',
                '--em' => 'default',
            ]
        );
        self::assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Dropping comments for TaskStatusType type...', $output);
        self::assertStringContainsString(' * Task::$status   Dropped ✔', $output);
        self::assertStringContainsString('TOTAL: 1', $output);
        self::assertStringContainsString('DONE', $output);
    }
}
