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
     * @var AbstractEnumType $type AbstractEnumType
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        Type::addType('BasketballPositionType', '\Fresh\DoctrineEnumBundle\Fixtures\DBAL\Types\BasketballPositionType');
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
     * Data provider for method getSqlDeclaration
     *
     * @return array
     */
    public function platformProvider()
    {
        return array(
            array(
                array('name' => 'position'),
                new MySqlPlatform(),
                "ENUM('PG', 'SG', 'SF', 'PF', 'C')"
            ),
            array(
                array('name' => 'position'),
                new SqlitePlatform(),
                "TEXT CHECK(position IN ('PG', 'SG', 'SF', 'PF', 'C'))"
            )
        );
    }
}
