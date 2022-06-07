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
use Doctrine\DBAL\Platforms\SqlitePlatform;
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

    /**
     * @dataProvider platformProviderForGetSqlDeclarationWithoutDefaultValue
     *
     * @param array            $fieldDeclaration
     * @param AbstractPlatform $platform
     * @param string           $expected
     */
    public function testGetSqlDeclarationWithoutDefaultValue(array $fieldDeclaration, AbstractPlatform $platform, string $expected): void
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
            new SqlitePlatform(),
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

    /**
     * @dataProvider platformProviderForGetSqlDeclarationWithDefaultValue
     *
     * @param array            $fieldDeclaration
     * @param AbstractPlatform $platform
     * @param string           $expected
     */
    public function testGetSqlDeclarationWithDefaultValue(array $fieldDeclaration, AbstractPlatform $platform, string $expected): void
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
            new SqlitePlatform(),
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

    public function testGetName(): void
    {
        self::assertEquals('BasketballPositionType', $this->type->getName());
        self::assertEquals('StubType', Type::getType('StubType')->getName());
    }

    public function testRequiresSQLCommentHint(): void
    {
        self::assertTrue($this->type->requiresSQLCommentHint(new MySqlPlatform()));
    }

    public function testConvertToDatabaseValue(): void
    {
        self::assertNull($this->type->convertToDatabaseValue(null, new MySqlPlatform()));
        self::assertEquals('SF', $this->type->convertToDatabaseValue('SF', new MySqlPlatform()));
    }

    public function testInvalidArgumentExceptionInConvertToDatabaseValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->type->convertToDatabaseValue('YO', new MySqlPlatform());
    }

    public function testGetRandomValue(): void
    {
        $values = $this->type::getValues();

        self::assertContains($this->type::getRandomValue(), $values);
        self::assertContains($this->type::getRandomValue(), $values);
        self::assertContains($this->type::getRandomValue(), $values);
    }

    public function testGetRandomValueWithException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('There is no value in Enum type');

        NoValueType::getRandomValue();
    }

    public function testGetReadableValues(): void
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

    public function testAssertValidChoiceString(): void
    {
        self::assertNull($this->type::assertValidChoice(BasketballPositionType::SMALL_FORWARD));
    }

    public function testAssertValidChoiceNumeric(): void
    {
        $this->type = Type::getType('NumericType');
        self::assertNull($this->type::assertValidChoice(NumericType::TWO));

        $this->type = Type::getType('HTTPStatusCodeType');
        self::assertNull($this->type::assertValidChoice(HTTPStatusCodeType::HTTP_NOT_FOUND));

        $this->type = Type::getType('BasketballPositionType');
    }

    public function testInvalidArgumentExceptionInAssertValidChoice(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->type::assertValidChoice('YO');
    }

    public function testGetReadableValueString(): void
    {
        $this->type = Type::getType('HTTPStatusCodeType');
        self::assertEquals('Not Found', $this->type::getReadableValue(HTTPStatusCodeType::HTTP_NOT_FOUND));

        $this->type = Type::getType('BasketballPositionType');
        self::assertEquals('Small Forward', $this->type::getReadableValue(BasketballPositionType::SMALL_FORWARD));
    }

    public function testGetReadableValueNumeric(): void
    {
        $this->type = Type::getType('NumericType');
        self::assertEquals(2, $this->type::getReadableValue(NumericType::TWO));
        $this->type = Type::getType('BasketballPositionType');
    }

    public function testGetDefaultValue(): void
    {
        self::assertNull($this->type::getDefaultValue());
        self::assertEquals('pending', Type::getType('TaskStatusType')::getDefaultValue());
        self::assertEquals(0, Type::getType('NumericType')::getDefaultValue());
        self::assertEquals(200, Type::getType('HTTPStatusCodeType')::getDefaultValue());
    }

    public function testInvalidArgumentExceptionInGetReadableValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->type::getReadableValue('YO');
    }

    public function testGetChoices(): void
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

    public function testMappedDatabaseTypesContainEnumOnMySQL(): void
    {
        $actual = $this->type->getMappedDatabaseTypes(new MySQLPlatform());
        self::assertContains('enum', $actual);
    }

    public function testMappedDatabaseTypesDoesNotContainEnumOnNonMySQL(): void
    {
        $testProviders = [
            new SqlitePlatform(),
            new PostgreSQLPlatform(),
            new SQLServerPlatform(),
        ];

        foreach ($testProviders as $testProvider) {
            $actual = $this->type->getMappedDatabaseTypes($testProvider);
            self::assertNotContains('enum', $actual);
        }
    }

    public function testConvertToPHPValue(): void
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
