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
 * EnumConstantExtensionTest.
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
            'BasketballPositionType' => ['class' => 'Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType'],
            'MapLocationType'        => ['class' => 'Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\MapLocationType'],
        ]);
    }

    public function testGetName()
    {
        $this->assertEquals('ENUM Constant', $this->enumConstantExtension->getName());
    }

    public function testGetFilters()
    {
        $this->assertEquals(
            [new \Twig_SimpleFilter('enum_constant', [$this->enumConstantExtension, 'getEnumConstant'])],
            $this->enumConstantExtension->getFilters()
        );
    }

    /**
     * @dataProvider dataProviderForGetReadableEnumValueTest
     */
    public function testGetEnumConstant($expectedValueOfConstant, $enumConstant, $enumType)
    {
        $this->assertEquals(
            $expectedValueOfConstant,
            $this->enumConstantExtension->getEnumConstant($enumConstant, $enumType)
        );
    }

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
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\EnumTypeIsNotRegisteredException
     */
    public function testEnumTypeIsNotRegisteredException()
    {
        $this->enumConstantExtension->getEnumConstant('Pitcher', 'BaseballPositionType');
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\ConstantIsFoundInFewRegisteredEnumTypesException
     */
    public function testConstantIsFoundInFewRegisteredEnumTypesException()
    {
        $this->enumConstantExtension->getEnumConstant('CENTER');
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\ConstantIsNotFoundInAnyRegisteredEnumTypeException
     */
    public function testConstantIsNotFoundInAnyRegisteredEnumTypeException()
    {
        $this->enumConstantExtension->getEnumConstant('Pitcher');
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\NoRegisteredEnumTypesException
     */
    public function testNoRegisteredEnumTypesException()
    {
        // Create EnumConstantExtension without any registered ENUM type
        $extension = new EnumConstantExtension([]);
        $extension->getEnumConstant(BasketballPositionType::POINT_GUARD, 'BasketballPositionType');
    }
}
