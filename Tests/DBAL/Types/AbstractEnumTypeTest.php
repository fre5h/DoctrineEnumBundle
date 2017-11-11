<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
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
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\StubType;
use PHPUnit\Framework\TestCase;

/**
 * AbstractEnumTypeTest.
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 * @author Ben Davies    <ben.davies@gmail.com>
 */
class AbstractEnumTypeTest extends TestCase
{
    /** @var AbstractEnumType */
    private $type;

    public static function setUpBeforeClass()
    {
        Type::addType('BasketballPositionType', BasketballPositionType::class);
        Type::addType('StubType', StubType::class);
    }

    public function setUp()
    {
        $this->type = Type::getType('BasketballPositionType');
    }

    protected function tearDown()
    {
        unset($this->type);
    }

    /**
     * @dataProvider platformProvider
     */
    public function testGetSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform, string $expected)
    {
        $this->assertEquals($expected, $this->type->getSqlDeclaration($fieldDeclaration, $platform));
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

    public function testGetName()
    {
        $this->assertEquals('BasketballPositionType', $this->type->getName());
        $this->assertEquals('StubType', Type::getType('StubType')->getName());
    }

    public function testRequiresSQLCommentHint()
    {
        $this->assertTrue($this->type->requiresSQLCommentHint(new MySqlPlatform()));
    }

    public function testConvertToDatabaseValue()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, new MySqlPlatform()));
        $this->assertEquals('SF', $this->type->convertToDatabaseValue('SF', new MySqlPlatform()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgumentExceptionInConvertToDatabaseValue()
    {
        $this->type->convertToDatabaseValue('YO', new MySqlPlatform());
    }

    public function testGetReadableValues()
    {
        $choices = [
            'PG' => 'Point Guard',
            'SG' => 'Shooting Guard',
            'SF' => 'Small Forward',
            'PF' => 'Power Forward',
            'C' => 'Center',
        ];
        $this->assertEquals($choices, $this->type->getReadableValues());
    }

    public function testAssertValidChoice()
    {
        $this->assertNull($this->type->assertValidChoice(BasketballPositionType::SMALL_FORWARD));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgumentExceptionInAssertValidChoice()
    {
        $this->type->assertValidChoice('YO');
    }

    public function testGetReadableValue()
    {
        $this->assertEquals('Small Forward', $this->type->getReadableValue(BasketballPositionType::SMALL_FORWARD));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgumentExceptionInGetReadableValue()
    {
        $this->type->getReadableValue('YO');
    }

    public function testGetChoices()
    {
        $choices = [
            'Point Guard' => 'PG',
            'Shooting Guard' => 'SG',
            'Small Forward' => 'SF',
            'Power Forward' => 'PF',
            'Center' => 'C',
        ];

        $this->assertEquals($choices, $this->type->getChoices());
    }

    public function testMappedDatabaseTypesContainEnumOnMySQL()
    {
        $actual = $this->type->getMappedDatabaseTypes(new MySqlPlatform());
        $this->assertContains('enum', $actual);
    }

    public function testMappedDatabaseTypesDoesNotContainEnumOnNonMySQL()
    {
        $testProviders = [
            new SqlitePlatform(),
            new PostgreSqlPlatform(),
            new SQLServerPlatform(),
        ];

        foreach ($testProviders as $testProvider) {
            $actual = $this->type->getMappedDatabaseTypes($testProvider);
            $this->assertNotContains('enum', $actual);
        }
    }
}
