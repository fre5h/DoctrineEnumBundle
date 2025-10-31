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

namespace Fresh\DoctrineEnumBundle\Tests\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\DBAL\Types\Type;
use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;
use Fresh\DoctrineEnumBundle\Exception\InvalidArgumentException;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\HTTPStatusCodeType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\NoValueType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\NumericType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\StubType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\TaskStatusType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * AbstractEnumTypeTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 * @author Ben Davies <ben.davies@gmail.com>
 */
final class AbstractEnumTypeTest extends TestCase
{
    private AbstractEnumType $type;

    public static function setUpBeforeClass(): void
    {
        Type::addType('BasketballPositionType', BasketballPositionType::class);
        Type::addType('TaskStatusType', TaskStatusType::class);
        Type::addType('StubType', StubType::class);
        Type::addType('NumericType', NumericType::class);
        Type::addType('HTTPStatusCodeType', HTTPStatusCodeType::class);
    }

    protected function setUp(): void
    {
        $this->type = Type::getType('BasketballPositionType');
    }

    protected function tearDown(): void
    {
        unset($this->type);
    }

    #[Test]
    #[DataProvider('platformProviderForGetSqlDeclarationWithoutDefaultValue')]
    public function GetSqlDeclarationWithoutDefaultValue(array $fieldDeclaration, AbstractPlatform $platform, string $expected): void
    {
        self::assertEquals($expected, $this->type->getSqlDeclaration($fieldDeclaration, $platform));
    }

    public static function platformProviderForGetSqlDeclarationWithoutDefaultValue(): iterable
    {
        yield 'mysql' => [
            ['name' => 'position'],
            new MySQLPlatform(),
            "ENUM('PG', 'SG', 'SF', 'PF', 'C')",
        ];
        yield 'sqlite' => [
            ['name' => 'position'],
            new SQLitePlatform(),
            "TEXT CHECK(position IN ('PG', 'SG', 'SF', 'PF', 'C'))",
        ];
        yield 'postgresql' => [
            ['name' => 'position'],
            new PostgreSQLPlatform(),
            "VARCHAR(255) CHECK(position IN ('PG', 'SG', 'SF', 'PF', 'C'))",
        ];
        yield 'sql server' => [
            ['name' => 'position'],
            new SQLServerPlatform(),
            "VARCHAR(255) CHECK(position IN ('PG', 'SG', 'SF', 'PF', 'C'))",
        ];
    }

    #[Test]
    #[DataProvider('platformProviderForGetSqlDeclarationWithDefaultValue')]
    public function getSqlDeclarationWithDefaultValue(array $fieldDeclaration, AbstractPlatform $platform, string $expected): void
    {
        $type = Type::getType('TaskStatusType');
        self::assertEquals($expected, $type->getSqlDeclaration($fieldDeclaration, $platform));
    }

    public static function platformProviderForGetSqlDeclarationWithDefaultValue(): iterable
    {
        yield 'mysql' => [
            ['name' => 'position'],
            new MySQLPlatform(),
            "ENUM('pending', 'done', 'failed') DEFAULT 'pending'",
        ];
        yield 'sqlite' => [
            ['name' => 'position'],
            new SQLitePlatform(),
            "TEXT CHECK(position IN ('pending', 'done', 'failed')) DEFAULT 'pending'",
        ];
        yield 'postgresql' => [
            ['name' => 'position'],
            new PostgreSQLPlatform(),
            "VARCHAR(255) CHECK(position IN ('pending', 'done', 'failed')) DEFAULT 'pending'",
        ];
        yield 'sql server' => [
            ['name' => 'position'],
            new SQLServerPlatform(),
            "VARCHAR(255) CHECK(position IN ('pending', 'done', 'failed')) DEFAULT 'pending'",
        ];
    }

    #[Test]
    public function getName(): void
    {
        self::assertEquals('BasketballPositionType', $this->type->getName());
        self::assertEquals('StubType', Type::getType('StubType')->getName());
    }

    #[Test]
    public function requiresSQLCommentHint(): void
    {
        self::assertTrue($this->type->requiresSQLCommentHint(new MySqlPlatform()));
    }

    #[Test]
    public function convertToDatabaseValue(): void
    {
        self::assertNull($this->type->convertToDatabaseValue(null, new MySqlPlatform()));
        self::assertEquals('SF', $this->type->convertToDatabaseValue('SF', new MySqlPlatform()));
    }

    #[Test]
    public function invalidArgumentExceptionInConvertToDatabaseValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->type->convertToDatabaseValue('YO', new MySqlPlatform());
    }

    #[Test]
    public function getRandomValue(): void
    {
        $values = $this->type::getValues();

        self::assertContains($this->type::getRandomValue(), $values);
        self::assertContains($this->type::getRandomValue(), $values);
        self::assertContains($this->type::getRandomValue(), $values);
    }

    #[Test]
    public function getRandomValueWithException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('There is no value in Enum type');

        NoValueType::getRandomValue();
    }

    #[Test]
    public function getReadableValues(): void
    {
        $choices = [
            'PG' => 'Point Guard',
            'SG' => 'Shooting Guard',
            'SF' => 'Small Forward',
            'PF' => 'Power Forward',
            'C' => 'Center',
        ];
        self::assertEquals($choices, $this->type::getReadableValues());
    }

    #[Test]
    public function assertValidChoiceString(): void
    {
        self::assertNull($this->type::assertValidChoice(BasketballPositionType::SMALL_FORWARD));
    }

    #[Test]
    public function assertValidChoiceNumeric(): void
    {
        $this->type = Type::getType('NumericType');
        self::assertNull($this->type::assertValidChoice(NumericType::TWO));

        $this->type = Type::getType('HTTPStatusCodeType');
        self::assertNull($this->type::assertValidChoice(HTTPStatusCodeType::HTTP_NOT_FOUND));

        $this->type = Type::getType('BasketballPositionType');
    }

    #[Test]
    public function invalidArgumentExceptionInAssertValidChoice(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->type::assertValidChoice('YO');
    }

    #[Test]
    public function getReadableValueString(): void
    {
        $this->type = Type::getType('HTTPStatusCodeType');
        self::assertEquals('Not Found', $this->type::getReadableValue(HTTPStatusCodeType::HTTP_NOT_FOUND));

        $this->type = Type::getType('BasketballPositionType');
        self::assertEquals('Small Forward', $this->type::getReadableValue(BasketballPositionType::SMALL_FORWARD));
    }

    #[Test]
    public function getReadableValueNumeric(): void
    {
        $this->type = Type::getType('NumericType');
        self::assertEquals(2, $this->type::getReadableValue(NumericType::TWO));
        $this->type = Type::getType('BasketballPositionType');
    }

    #[Test]
    public function getDefaultValue(): void
    {
        self::assertNull($this->type::getDefaultValue());
        self::assertEquals('pending', Type::getType('TaskStatusType')::getDefaultValue());
        self::assertEquals(0, Type::getType('NumericType')::getDefaultValue());
        self::assertEquals(200, Type::getType('HTTPStatusCodeType')::getDefaultValue());
    }

    #[Test]
    public function invalidArgumentExceptionInGetReadableValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->type::getReadableValue('YO');
    }

    #[Test]
    public function getChoices(): void
    {
        $choices = [
            'Point Guard' => 'PG',
            'Shooting Guard' => 'SG',
            'Small Forward' => 'SF',
            'Power Forward' => 'PF',
            'Center' => 'C',
        ];

        self::assertEquals($choices, $this->type::getChoices());
    }

    #[Test]
    public function mappedDatabaseTypesContainEnumOnMySQL(): void
    {
        $actual = $this->type->getMappedDatabaseTypes(new MySQLPlatform());
        self::assertContains('enum', $actual);
    }

    #[Test]
    public function mappedDatabaseTypesDoesNotContainEnumOnNonMySQL(): void
    {
        $testProviders = [
            new SQLitePlatform(),
            new PostgreSQLPlatform(),
            new SQLServerPlatform(),
        ];

        foreach ($testProviders as $testProvider) {
            $actual = $this->type->getMappedDatabaseTypes($testProvider);
            self::assertNotContains('enum', $actual);
        }
    }

    #[Test]
    public function convertToPHPValue(): void
    {
        self::assertNull($this->type->convertToPHPValue(null, new MySQLPlatform()));
        self::assertSame('SF', $this->type->convertToPHPValue('SF', new MySQLPlatform()));

        $this->type = Type::getType('NumericType');
        self::assertNull($this->type->convertToPHPValue(null, new MySQLPlatform()));
        self::assertEquals(1, $this->type->convertToPHPValue('1', new MySQLPlatform()));

        $this->type = Type::getType('HTTPStatusCodeType');
        self::assertNull($this->type->convertToPHPValue(null, new MySQLPlatform()));
        self::assertEquals(200, $this->type->convertToPHPValue('200', new MySQLPlatform()));
    }
}
