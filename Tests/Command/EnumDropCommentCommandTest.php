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
use Doctrine\ORM\Mapping\FieldMapping;
use Doctrine\Persistence\ManagerRegistry;
use Fresh\DoctrineEnumBundle\Command\EnumDropCommentCommand;
use Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsRegisteredButClassDoesNotExistException;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\TaskStatusType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class EnumDropCommentCommandTest extends TestCase
{
    /** @var EntityManagerInterface|MockObject */
    private EntityManagerInterface|MockObject $em;

    /** @var ManagerRegistry|MockObject */
    private ManagerRegistry|MockObject $registry;

    /** @var Connection|MockObject */
    private Connection|MockObject $connection;

    /** @var AbstractPlatform|MockObject */
    private AbstractPlatform|MockObject $platform;

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

    #[Test]
    public function exceptionInConstructor(): void
    {
        $this->expectException(EnumTypeIsRegisteredButClassDoesNotExistException::class);
        $this->expectExceptionMessage('ENUM type "CustomType" is registered as "Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\CustomType", but that class does not exist');

        new EnumDropCommentCommand(
            $this->registry,
            [
                'CustomType' => ['class' => 'Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\CustomType'],
                'TaskStatusType' => ['class' => TaskStatusType::class],
            ]
        );

        $this->commandTester->getDisplay();
    }

    #[Test]
    public function exceptionOnExecution(): void
    {
        $this->em
            ->expects($this->once())
            ->method('getConnection')
            ->willThrowException(new \Exception('test', 5))
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'enumType' => 'TaskStatusType',
            ]
        );
        $this->assertSame(5, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('test', $output);
    }

    #[Test]
    public function invalidEnumTypeArgument(): void
    {
        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'enumType' => null,
            ]
        );
        $this->assertSame(1, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Argument "enumType" is not a string.', $output);
    }

    #[Test]
    public function exceptionNotRegisteredEnumType(): void
    {
        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'enumType' => 'UnknownType',
            ]
        );
        $this->assertSame(2, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Argument "enumType" is not a registered ENUM type.', $output);
    }

    #[Test]
    public function executionWithCaughtException(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('getDatabasePlatform')
            ->willThrowException(new \Exception('test', 5))
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'enumType' => 'TaskStatusType',
            ]
        );
        $this->assertSame(5, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('test', $output);
    }

    #[Test]
    public function successfulExecutionWithNoMetadata(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('getDatabasePlatform')
            ->willReturn($this->platform)
        ;

        $this->metadataFactory
            ->expects($this->once())
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
        $this->assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Dropping comments for TaskStatusType type...', $output);
        $this->assertStringContainsString('NO METADATA FOUND', $output);
    }

    #[Test]
    #[DataProvider('dataProviderForMetadataTest')]
    public function successfulExecutionWithMetadata(?string $schemaName, string $sqlColumnComment): void
    {
        $this->connection
            ->expects($this->once())
            ->method('getDatabasePlatform')
            ->willReturn($this->platform)
        ;

        $metadata = $this->createMock(ClassMetadata::class);
        $this->metadataFactory
            ->expects($this->once())
            ->method('getAllMetadata')
            ->willReturn([$metadata])
        ;

        $metadata->expects($this->once())->method('getName')->willReturn('Task');
        $metadata->expects($this->atLeast(1))->method('getSchemaName')->willReturn($schemaName);
        $metadata->expects($this->once())->method('getTableName')->willReturn('tasks');
        $metadata->expects($this->once())->method('getFieldNames')->willReturn(['status']);
        $metadata->expects($this->once())->method('getTypeOfField')->with('status')->willReturn('TaskStatusType');
        $metadata->expects($this->once())->method('getFieldMapping')->with('status')->willReturn(
            FieldMapping::fromMappingArray(['type' => 'string', 'columnName' => 'task_column_name', 'fieldName' => 'test'])
        );

        $this->platform->expects($this->once())->method('getCommentOnColumnSQL')->with($sqlColumnComment, 'task_column_name', 'NULL')->willReturn('test SQL');

        $this->connection->expects($this->once())->method('executeQuery')->with('test SQL');

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'enumType' => 'TaskStatusType',
                '--em' => 'default',
            ]
        );
        $this->assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Dropping comments for TaskStatusType type...', $output);
        $this->assertStringContainsString(' * Task::$status   Dropped ✔', $output);
        $this->assertStringContainsString('TOTAL: 1', $output);
        $this->assertStringContainsString('DONE', $output);
    }

    public static function dataProviderForMetadataTest(): iterable
    {
        yield 'no schema' => [
            'schemaName' => null,
            'sqlColumnComment' => 'tasks',
        ];
        yield 'public schema' => [
            'schemaName' => 'public',
            'sqlColumnComment' => 'public.tasks',
        ];
        yield 'custom schema' => [
            'schemaName' => 'custom',
            'sqlColumnComment' => 'custom.tasks',
        ];
    }

    #[Test]
    public function autocomplete(): void
    {
        $enumTypes = $this->command->getEnumTypesForAutocompletion()();

        $this->assertSame(['BasketballPositionType', 'TaskStatusType'], $enumTypes);
    }
}
