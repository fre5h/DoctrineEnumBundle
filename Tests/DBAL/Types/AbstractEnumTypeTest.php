<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Tests\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\DBAL\Types\Type;
use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;

/**
 * AbstractEnumTypeTest
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 * @author Ben Davies    <ben.davies@gmail.com>
 *
 * @coversDefaultClass \Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType
 */
class AbstractEnumTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractEnumType $type Abstract EnumType
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        Type::addType(
            'BasketballPositionType',
            '\Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType'
        );
        Type::addType('StubType', '\Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\StubType');
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->type = Type::getType('BasketballPositionType');
    }

    /**
     * Test that the SQL declaration is the correct for the platform
     *
     * @param array            $fieldDeclaration The field declaration
     * @param AbstractPlatform $platform         The DBAL platform
     * @param string           $expected         Expected SQL declaration
     *
     * @dataProvider platformProvider
     */
    public function testGetSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform, $expected)
    {
        $this->assertEquals($expected, $this->type->getSqlDeclaration($fieldDeclaration, $platform));
    }

    /**
     * Data provider for method `getSqlDeclaration`
     *
     * @return array
     */
    public function platformProvider()
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

    /**
     * Test method `getName`
     */
    public function testGetName()
    {
        $this->assertEquals('BasketballPositionType', $this->type->getName());
        $this->assertEquals('StubType', Type::getType('StubType')->getName());
    }

    /**
     * Test method `requiresSQLCommentHint`
     */
    public function testRequiresSQLCommentHint()
    {
        $this->assertTrue($this->type->requiresSQLCommentHint(new MySqlPlatform()));
    }

    /**
     * Test method `convertToDatabaseValue`
     */
    public function testConvertToDatabaseValue()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, new MySqlPlatform()));
        $this->assertEquals('SF', $this->type->convertToDatabaseValue('SF', new MySqlPlatform()));
    }

    /**
     * Test that converting unknown value of ENUM type throws InvalidArgumentException
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgumentExceptionInConvertToDatabaseValue()
    {
        $this->type->convertToDatabaseValue('YO', new MySqlPlatform());
    }

    /**
     * Test method `getReadableValue`
     */
    public function testGetReadableValue()
    {
        $this->assertEquals('Small Forward', $this->type->getReadableValue(BasketballPositionType::SMALL_FORWARD));
    }

    /**
     * Test that getting readable value for unknown value of ENUM type throws InvalidArgumentException
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgumentExceptionInGetReadableValue()
    {
        $this->type->getReadableValue('YO');
    }
}
