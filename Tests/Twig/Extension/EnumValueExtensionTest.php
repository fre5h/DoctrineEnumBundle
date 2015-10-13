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

use Fresh\DoctrineEnumBundle\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Fixtures\DBAL\Types\MapLocationType;
use Fresh\DoctrineEnumBundle\Twig\Extension\EnumValueExtension;

/**
 * ReadableEnumValueExtensionTest
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class EnumValueExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EnumValueExtension $readableEnumValueExtension
     */
    private $enumValueExtension;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->enumValueExtension = new EnumValueExtension([
            'BasketballPositionType' => [
                'class' => 'Fresh\DoctrineEnumBundle\Fixtures\DBAL\Types\BasketballPositionType'
            ],
            'MapLocationType'        => [
                'class' => 'Fresh\DoctrineEnumBundle\Fixtures\DBAL\Types\MapLocationType'
            ]
        ]);
    }

    /**
     * Test method getName
     */
    public function testGetName()
    {
        $this->assertEquals('ENUM Value', $this->enumValueExtension->getName());
    }

    /**
     * Test method getFilters
     */
    public function testGetFilters()
    {
        $this->assertEquals(
            ['enum' => new \Twig_Filter_Method($this->enumValueExtension, 'getEnumValue')],
            $this->enumValueExtension->getFilters()
        );
    }

    /**
     * Test that method getReadableEnumValue returns expected readable value
     *
     * @param string $expectedReadableValue Expected readable value
     * @param string $enumValue             Enum value
     * @param string $enumType              Enum type
     *
     * @dataProvider dataProviderForGetReadableEnumValueTest
     */
    public function testGetReadableEnumValue($expectedReadableValue, $enumValue, $enumType)
    {
        $this->assertEquals(
            $expectedReadableValue,
            $this->enumValueExtension->getEnumValue($enumValue, $enumType)
        );
    }

    /**
     * Data provider for method getReadableEnumValue
     *
     * @return array
     */
    public function dataProviderForGetReadableEnumValueTest()
    {
        return [
            ['PG', 'POINT_GUARD', 'BasketballPositionType'],
            ['PG', 'POINT_GUARD', null],
            ['C', 'CENTER', 'BasketballPositionType'],
            ['C', 'CENTER', 'MapLocationType']
        ];
    }

    /**
     * Test that using readable ENUM value extension for ENUM type that is not registered
     * throws EnumTypeIsNotRegisteredException
     *
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\EnumTypeIsNotRegisteredException
     */
    public function testEnumTypeIsNotRegisteredException()
    {
        $this->enumValueExtension->getEnumValue('Pitcher', 'BaseballPositionType');
    }

    /**
     * Test that using ENUM value that is found in few registered ENUM types
     * throws ValueIsFoundInFewRegisteredEnumTypesException
     *
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\ConstantIsFoundInFewRegisteredEnumTypesException
     */
    public function testConstantIsFoundInFewRegisteredEnumTypesException()
    {
        $this->enumValueExtension->getEnumValue('CENTER');
    }

    /**
     * Test that using ENUM value that is not found in any registered ENUM type
     * throws ValueIsNotFoundInAnyRegisteredEnumTypeException
     *
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\ConstantIsNotFoundInAnyRegisteredEnumTypeException
     */
    public function testConstantIsNotFoundInAnyRegisteredEnumTypeException()
    {
        $this->enumValueExtension->getEnumValue('Z');
    }

    /**
     * Test that using readable ENUM value extension without any registered ENUM type
     * throws NoRegisteredEnumTypesException
     *
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\NoRegisteredEnumTypesException
     */
    public function testNoRegisteredEnumTypesException()
    {
        // Create ReadableEnumValueExtension without any registered ENUM type
        $extension = new EnumValueExtension([]);
        $extension->getEnumValue('POINT_GUARD', 'BasketballPositionType');
    }
}
