<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
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
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\DBAL\Types\Type;
use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\NumericType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\StubType;
use PHPUnit\Framework\TestCase;

/**
 * AbstractEnumTypeTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 * @author Ben Davies    <ben.davies@gmail.com>
 */
final class AbstractEnumTypeTest extends TestCase
{
    /** @var AbstractEnumType */
    private $type;

    public static function setUpBeforeClass(): void
    {
        Type::addType('BasketballPositionType', BasketballPositionType::class);
        Type::addType('StubType', StubType::class);
        Type::addType('NumericType', NumericType::class);
    }

    public function setUp(): void
    {
        $this->type = Type::getType('BasketballPositionType');
    }

    protected function tearDown(): void
    {
        unset($this->type);
    }

    /**
     * @dataProvider platformProvider
     */
    public function testGetSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform, string $expected): void
    {
        self::assertEquals($expected, $this->type->getSqlDeclaration($fieldDeclaration, $platform));
    }

    public function platformProvider(): array
    {
        return [
            [
                ['name' => 'position'],
                new MySqlPlatform(),
                "ENUM('PG', 'SG', 'SF', 'PF', 'C')",
            ],
            [
                ['name' => 'position'],
                new SqlitePlatform(),
                "TEXT CHECK(position IN ('PG', 'SG', 'SF', 'PF', 'C'))",
            ],
            [
                ['name' => 'position'],
                new PostgreSqlPlatform(),
                "VARCHAR(255) CHECK(position IN ('PG', 'SG', 'SF', 'PF', 'C'))",
            ],
            [
                ['name' => 'position'],
                new SQLServerPlatform(),
                "VARCHAR(255) CHECK(position IN ('PG', 'SG', 'SF', 'PF', 'C'))",
            ],
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
        $this->expectException(\InvalidArgumentException::class);
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

    public function testAssertValidChoice(): void
    {
        self::assertNull($this->type::assertValidChoice(BasketballPositionType::SMALL_FORWARD));
    }

    public function testInvalidArgumentExceptionInAssertValidChoice(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->type::assertValidChoice('YO');
    }

    public function testGetReadableValue(): void
    {
        self::assertEquals('Small Forward', $this->type::getReadableValue(BasketballPositionType::SMALL_FORWARD));
    }

    public function testInvalidArgumentExceptionInGetReadableValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
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
            new PostgreSqlPlatform(),
            new SQLServerPlatform(),
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
    }
}
