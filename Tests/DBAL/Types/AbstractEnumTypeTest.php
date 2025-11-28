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
    public function getSqlDeclarationWithoutDefaultValue(array $fieldDeclaration, AbstractPlatform $platform, string $expected): void
    {
        $this->assertEquals($expected, $this->type->getSqlDeclaration($fieldDeclaration, $platform));
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
        $this->assertEquals($expected, $type->getSqlDeclaration($fieldDeclaration, $platform));
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
        $this->assertEquals('BasketballPositionType', $this->type->getName());
        $this->assertEquals('StubType', Type::getType('StubType')->getName());
    }

    #[Test]
    public function requiresSQLCommentHint(): void
    {
        $this->assertTrue($this->type->requiresSQLCommentHint(new MySQLPlatform()));
    }

    #[Test]
    public function convertToDatabaseValue(): void
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, new MySQLPlatform()));
        $this->assertEquals('SF', $this->type->convertToDatabaseValue('SF', new MySQLPlatform()));
    }

    #[Test]
    public function invalidArgumentExceptionInConvertToDatabaseValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->type->convertToDatabaseValue('YO', new MySQLPlatform());
    }

    #[Test]
    public function getRandomValue(): void
    {
        $values = $this->type::getValues();

        $this->assertContains($this->type::getRandomValue(), $values);
        $this->assertContains($this->type::getRandomValue(), $values);
        $this->assertContains($this->type::getRandomValue(), $values);
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
        $this->assertEquals($choices, $this->type::getReadableValues());
    }

    #[Test]
    public function assertValidChoiceString(): void
    {
        $this->assertNull($this->type::assertValidChoice(BasketballPositionType::SMALL_FORWARD));
    }

    #[Test]
    public function assertValidChoiceNumeric(): void
    {
        $this->type = Type::getType('NumericType');
        $this->assertNull($this->type::assertValidChoice(NumericType::TWO));

        $this->type = Type::getType('HTTPStatusCodeType');
        $this->assertNull($this->type::assertValidChoice(HTTPStatusCodeType::HTTP_NOT_FOUND));

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
        $this->assertEquals('Not Found', $this->type::getReadableValue(HTTPStatusCodeType::HTTP_NOT_FOUND));

        $this->type = Type::getType('BasketballPositionType');
        $this->assertEquals('Small Forward', $this->type::getReadableValue(BasketballPositionType::SMALL_FORWARD));
    }

    #[Test]
    public function getReadableValueNumeric(): void
    {
        $this->type = Type::getType('NumericType');
        $this->assertEquals(2, $this->type::getReadableValue(NumericType::TWO));
        $this->type = Type::getType('BasketballPositionType');
    }

    #[Test]
    public function getDefaultValue(): void
    {
        $this->assertNull($this->type::getDefaultValue());
        $this->assertEquals('pending', Type::getType('TaskStatusType')::getDefaultValue());
        $this->assertEquals(0, Type::getType('NumericType')::getDefaultValue());
        $this->assertEquals(200, Type::getType('HTTPStatusCodeType')::getDefaultValue());
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

        $this->assertEquals($choices, $this->type::getChoices());
    }

    #[Test]
    public function mappedDatabaseTypesContainEnumOnMySQL(): void
    {
        $actual = $this->type->getMappedDatabaseTypes(new MySQLPlatform());
        $this->assertContains('enum', $actual);
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
            $this->assertNotContains('enum', $actual);
        }
    }

    #[Test]
    public function convertToPHPValue(): void
    {
        $this->assertNull($this->type->convertToPHPValue(null, new MySQLPlatform()));
        $this->assertSame('SF', $this->type->convertToPHPValue('SF', new MySQLPlatform()));

        $this->type = Type::getType('NumericType');
        $this->assertNull($this->type->convertToPHPValue(null, new MySQLPlatform()));
        $this->assertEquals(1, $this->type->convertToPHPValue('1', new MySQLPlatform()));

        $this->type = Type::getType('HTTPStatusCodeType');
        $this->assertNull($this->type->convertToPHPValue(null, new MySQLPlatform()));
        $this->assertEquals(200, $this->type->convertToPHPValue('200', new MySQLPlatform()));
    }
}
