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
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Platforms\SQLServer2012Platform;
use Doctrine\DBAL\Types\Type;
use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;
use Fresh\DoctrineEnumBundle\Exception\InvalidArgumentException;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\HTTPStatusCodeType;
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
            new MySqlPlatform(),
            "ENUM('PG', 'SG', 'SF', 'PF', 'C')",
        ];
        yield 'sqlite' => [
            ['name' => 'position'],
            new SqlitePlatform(),
            "TEXT CHECK(position IN ('PG', 'SG', 'SF', 'PF', 'C'))",
        ];
        yield 'postgresql_9' => [
            ['name' => 'position'],
            new PostgreSQL94Platform(),
            "VARCHAR(255) CHECK(position IN ('PG', 'SG', 'SF', 'PF', 'C'))",
        ];
        yield 'postgresql_10' => [
            ['name' => 'position'],
            new PostgreSQL100Platform(),
            "VARCHAR(255) CHECK(position IN ('PG', 'SG', 'SF', 'PF', 'C'))",
        ];
        yield 'sql server' => [
            ['name' => 'position'],
            new SQLServer2012Platform(),
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
            new MySqlPlatform(),
            "ENUM('pending', 'done', 'failed') DEFAULT 'pending'",
        ];
        yield 'sqlite' => [
            ['name' => 'position'],
            new SqlitePlatform(),
            "TEXT CHECK(position IN ('pending', 'done', 'failed')) DEFAULT 'pending'",
        ];
        yield 'postgresql_9' => [
            ['name' => 'position'],
            new PostgreSQL94Platform(),
            "VARCHAR(255) CHECK(position IN ('pending', 'done', 'failed')) DEFAULT 'pending'",
        ];
        yield 'postgresql_10' => [
            ['name' => 'position'],
            new PostgreSQL100Platform(),
            "VARCHAR(255) CHECK(position IN ('pending', 'done', 'failed')) DEFAULT 'pending'",
        ];
        yield 'sql server' => [
            ['name' => 'position'],
            new SQLServer2012Platform(),
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
        $actual = $this->type->getMappedDatabaseTypes(new MySqlPlatform());
        self::assertContains('enum', $actual);
    }

    public function testMappedDatabaseTypesDoesNotContainEnumOnNonMySQL(): void
    {
        $testProviders = [
            new SqlitePlatform(),
            new PostgreSQL94Platform(),
            new PostgreSQL100Platform(),
            new SQLServer2012Platform(),
        ];

        foreach ($testProviders as $testProvider) {
            $actual = $this->type->getMappedDatabaseTypes($testProvider);
            self::assertNotContains('enum', $actual);
        }
    }

    public function testConvertToPHPValue(): void
    {
        self::assertNull($this->type->convertToPHPValue(null, new MySqlPlatform()));
        self::assertSame('SF', $this->type->convertToPHPValue('SF', new MySqlPlatform()));

        $this->type = Type::getType('NumericType');
        self::assertNull($this->type->convertToPHPValue(null, new MySqlPlatform()));
        self::assertEquals(1, $this->type->convertToPHPValue('1', new MySqlPlatform()));

        $this->type = Type::getType('HTTPStatusCodeType');
        self::assertNull($this->type->convertToPHPValue(null, new MySqlPlatform()));
        self::assertEquals(200, $this->type->convertToPHPValue('200', new MySqlPlatform()));
    }
}
