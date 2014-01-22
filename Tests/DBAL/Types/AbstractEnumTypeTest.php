<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fresh\Bundle\DoctrineEnumBundle\Tests\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;

/**
 * AbstractEnumTypeTest
 *
 * @author Ben Davies <ben.davies@gmail.com>
 *
 * @coversDefaultClass \Fresh\Bundle\DoctrineEnumBundle\DBAL\Types\AbstractEnumType
 */
class AbstractEnumTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Fresh\Bundle\DoctrineEnumBundle\DBAL\Types\AbstractEnumType
     */
    private $type;

    /**
     * Set up EnumType
     */
    public function setUp()
    {
        $this->type = $this->getMockBuilder('Fresh\Bundle\DoctrineEnumBundle\DBAL\Types\AbstractEnumType')
            ->disableOriginalConstructor()
            ->setMethods(array('getValues'))
            ->getMockForAbstractClass();

        $this->type->staticExpects($this->any())
            ->method('getValues')
            ->will($this->returnValue(array('M', 'F')));
    }

    /**
     * Test that the SQL declaration is the correct for the platform
     *
     * @param array            $fieldDeclaration The Field Declaration
     * @param AbstractPlatform $platform         The DBAL Platform
     * @param string           $expected         Expected Sql Declaration
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
     * @return array
     */
    public function platformProvider()
    {
        return array(
            array(array('name' => 'sex'), new MySqlPlatform(), "ENUM('M', 'F')"),
            array(array('name' => 'sex'), new SqlitePlatform(), "TEXT CHECK(sex IN ('M', 'F'))")
        );
    }
}
