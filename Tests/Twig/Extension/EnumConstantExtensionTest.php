<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Tests\Twig\Extension;

use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Twig\Extension\EnumConstantExtension;

/**
 * EnumConstantExtensionTest
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class EnumConstantExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EnumConstantExtension $enumConstantExtension EnumConstantExtension
     */
    private $enumConstantExtension;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->enumConstantExtension = new EnumConstantExtension([
            'BasketballPositionType' => [
                'class' => 'Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType',
            ],
            'MapLocationType'        => [
                'class' => 'Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\MapLocationType',
            ],
        ]);
    }

    /**
     * Test method `getName`
     */
    public function testGetName()
    {
        $this->assertEquals('ENUM Constant', $this->enumConstantExtension->getName());
    }

    /**
     * Test method `getFilters`
     */
    public function testGetFilters()
    {
        $this->assertEquals(
            [new \Twig_SimpleFilter('enum_constant', [$this->enumConstantExtension, 'getEnumConstant'])],
            $this->enumConstantExtension->getFilters()
        );
    }

    /**
     * Test that method `getEnumConstant` returns expected value of ENUM constant
     *
     * @param string $expectedValueOfConstant Expected readable value
     * @param string $enumConstant            ENUM constant
     * @param string $enumType                ENUM type
     *
     * @dataProvider dataProviderForGetReadableEnumValueTest
     */
    public function testGetEnumConstant($expectedValueOfConstant, $enumConstant, $enumType)
    {
        $this->assertEquals(
            $expectedValueOfConstant,
            $this->enumConstantExtension->getEnumConstant($enumConstant, $enumType)
        );
    }

    /**
     * Data provider for method `testGetEnumValue`
     *
     * @return array
     */
    public function dataProviderForGetReadableEnumValueTest()
    {
        return [
            ['PG', 'POINT_GUARD', 'BasketballPositionType'],
            ['PG', 'POINT_GUARD', null],
            ['C', 'CENTER', 'BasketballPositionType'],
            ['C', 'CENTER', 'MapLocationType'],
        ];
    }

    /**
     * Test that using ENUM constant extension for ENUM type that is not registered throws exception
     *
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\EnumTypeIsNotRegisteredException
     */
    public function testEnumTypeIsNotRegisteredException()
    {
        $this->enumConstantExtension->getEnumConstant('Pitcher', 'BaseballPositionType');
    }

    /**
     * Test that using ENUM constant that is found in few registered ENUM types throws exception
     *
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\ConstantIsFoundInFewRegisteredEnumTypesException
     */
    public function testConstantIsFoundInFewRegisteredEnumTypesException()
    {
        $this->enumConstantExtension->getEnumConstant('CENTER');
    }

    /**
     * Test that using ENUM constant that is not found in any registered ENUM type throws exception
     *
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\ConstantIsNotFoundInAnyRegisteredEnumTypeException
     */
    public function testConstantIsNotFoundInAnyRegisteredEnumTypeException()
    {
        $this->enumConstantExtension->getEnumConstant('Pitcher');
    }

    /**
     * Test that using ENUM constant extension without any registered ENUM type throws exception
     *
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\NoRegisteredEnumTypesException
     */
    public function testNoRegisteredEnumTypesException()
    {
        // Create EnumConstantExtension without any registered ENUM type
        $extension = new EnumConstantExtension([]);
        $extension->getEnumConstant(BasketballPositionType::POINT_GUARD, 'BasketballPositionType');
    }
}
