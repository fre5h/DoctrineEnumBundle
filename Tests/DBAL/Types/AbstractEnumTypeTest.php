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
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Types\Type;
use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * AbstractEnumTypeTest
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 * @author Ben Davies <ben.davies@gmail.com>
 *
 * @coversDefaultClass \Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType
 */
class AbstractEnumTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractEnumType
     */
    private $type;

    /**
     * Set up before test suite
     */
    public static function setUpBeforeClass()
    {
        Type::addType('BasketballPositionType', '\Fresh\DoctrineEnumBundle\Fixtures\DBAL\Types\BasketballPositionType');
    }

    /**
     * Set up environment
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
     * @test
     * @covers ::getSqlDeclaration
     * @dataProvider platformProvider
     */
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform, $expected)
    {
        $this->assertEquals($expected, $this->type->getSqlDeclaration($fieldDeclaration, $platform));
    }

    /**
     * Data provider for method getSqlDeclaration
     *
     * @return array
     */
    public function platformProvider()
    {
        return [
            [
                ['name' => 'sex'],
                new MySqlPlatform(),
                "ENUM('PG', 'SG', 'SF', 'PF', 'C')"
            ],
            [
                ['name' => 'sex'],
                new SqlitePlatform(),
                "TEXT CHECK(sex IN ('PG', 'SG', 'SF', 'PF', 'C'))"
            ]
        ];
    }
}
