<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\Bundle\DoctrineEnumBundle\Tests\Twig\Extension;

use Fresh\Bundle\DoctrineEnumBundle\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\Bundle\DoctrineEnumBundle\Fixtures\DBAL\Types\MapLocationType;
use Fresh\Bundle\DoctrineEnumBundle\Twig\Extension\ReadableEnumValueExtension;

/**
 * ReadableEnumValueExtensionTest
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class ReadableEnumValueExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReadableEnumValueExtension
     */
    protected $readableEnumValueExtension;

    /**
     * Set up ReadableEnumValueExtension
     */
    public function setUp()
    {
        $this->readableEnumValueExtension = new ReadableEnumValueExtension([
            'BasketballPositionType' => [
                'class' => 'Fresh\Bundle\DoctrineEnumBundle\Fixtures\DBAL\Types\BasketballPositionType'
            ],
            'MapLocationType'        => [
                'class' => 'Fresh\Bundle\DoctrineEnumBundle\Fixtures\DBAL\Types\MapLocationType'
            ]
        ]);
    }

    /**
     * Test that method getReadableEnumValue returns expected readable value
     *
     * @dataProvider dataProviderForTestGetReadableEnumValue
     */
    public function testGetReadableEnumValue($expectedReadableValue, $enumValue, $enumType)
    {
        $this->assertEquals($expectedReadableValue, $this->readableEnumValueExtension->getReadableEnumValue($enumValue, $enumType));
    }

    /**
     * Data provider for method testGetReadableEnumValue
     *
     * @return array
     */
    public static function dataProviderForTestGetReadableEnumValue()
    {
        return [
            ['Point guard', BasketballPositionType::POINT_GUARD, 'BasketballPositionType'],
            ['Center', BasketballPositionType::CENTER, 'BasketballPositionType'],
            ['Center', MapLocationType::CENTER, 'MapLocationType']
        ];
    }

    /**
     * Test that using readable ENUM value extension for ENUM type that is not registered throws EnumTypeIsNotRegisteredException
     *
     * @expectedException \Fresh\Bundle\DoctrineEnumBundle\Exception\EnumTypeIsNotRegisteredException
     */
    public function testEnumTypeIsNotRegisteredException()
    {
        $this->readableEnumValueExtension->getReadableEnumValue('Pitcher', 'BaseballPositionType');
    }

    /**
     * Test that using ENUM value that is found in few registered ENUN types throws ValueIsFoundInFewRegisteredEnumTypesException
     *
     * @expectedException \Fresh\Bundle\DoctrineEnumBundle\Exception\ValueIsFoundInFewRegisteredEnumTypesException
     */
    public function testValueIsFoundInFewRegisteredEnumTypesException()
    {
        $this->readableEnumValueExtension->getReadableEnumValue(BasketballPositionType::CENTER);
    }

    /**
     * Test that using ENUM value that is not found in any registered ENUN type throws ValueIsNotFoundInAnyRegisteredEnumTypeException
     *
     * @expectedException \Fresh\Bundle\DoctrineEnumBundle\Exception\ValueIsNotFoundInAnyRegisteredEnumTypeException
     */
    public function testValueIsNotFoundInAnyRegisteredEnumTypeException()
    {
        $this->readableEnumValueExtension->getReadableEnumValue('Pitcher');
    }

    /**
     * Test that using readable ENUM value extension without any registered ENUM type throws NoRegisteredEnumTypesException
     *
     * @expectedException \Fresh\Bundle\DoctrineEnumBundle\Exception\NoRegisteredEnumTypesException
     */
    public function testNoRegisteredEnumTypesException()
    {
        // Create ReadableEnumValueExtension without any registered ENUM type
        $readableEnumValueExtension = new ReadableEnumValueExtension([]);
        $readableEnumValueExtension->getReadableEnumValue(BasketballPositionType::POINT_GUARD, 'BasketballPositionType');
    }
}
