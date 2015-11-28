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
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\MapLocationType;
use Fresh\DoctrineEnumBundle\Twig\Extension\ReadableEnumValueExtension;

/**
 * ReadableEnumValueExtensionTest
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class ReadableEnumValueExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReadableEnumValueExtension $readableEnumValueExtension ReadableEnumValueExtension
     */
    private $readableEnumValueExtension;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->readableEnumValueExtension = new ReadableEnumValueExtension([
            'BasketballPositionType' => [
                'class' => 'Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType',
            ],
            'MapLocationType'        => [
                'class' => 'Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\MapLocationType',
            ],
        ]);
    }

    /**
     * Test method getName
     */
    public function testGetName()
    {
        $this->assertEquals('Readable ENUM Value', $this->readableEnumValueExtension->getName());
    }

    /**
     * Test method getFilters
     */
    public function testGetFilters()
    {
        $this->assertEquals(
            [new \Twig_SimpleFilter('readable', [$this->readableEnumValueExtension, 'getReadableEnumValue'])],
            $this->readableEnumValueExtension->getFilters()
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
            $this->readableEnumValueExtension->getReadableEnumValue($enumValue, $enumType)
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
            ['Point guard', BasketballPositionType::POINT_GUARD, 'BasketballPositionType'],
            ['Point guard', BasketballPositionType::POINT_GUARD, null],
            ['Center', BasketballPositionType::CENTER, 'BasketballPositionType'],
            ['Center', MapLocationType::CENTER, 'MapLocationType'],
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
        $this->readableEnumValueExtension->getReadableEnumValue('Pitcher', 'BaseballPositionType');
    }

    /**
     * Test that using ENUM value that is found in few registered ENUM types
     * throws ValueIsFoundInFewRegisteredEnumTypesException
     *
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\ValueIsFoundInFewRegisteredEnumTypesException
     */
    public function testValueIsFoundInFewRegisteredEnumTypesException()
    {
        $this->readableEnumValueExtension->getReadableEnumValue(BasketballPositionType::CENTER);
    }

    /**
     * Test that using ENUM value that is not found in any registered ENUM type
     * throws ValueIsNotFoundInAnyRegisteredEnumTypeException
     *
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\ValueIsNotFoundInAnyRegisteredEnumTypeException
     */
    public function testValueIsNotFoundInAnyRegisteredEnumTypeException()
    {
        $this->readableEnumValueExtension->getReadableEnumValue('Pitcher');
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
        $extension = new ReadableEnumValueExtension([]);
        $extension->getReadableEnumValue(BasketballPositionType::POINT_GUARD, 'BasketballPositionType');
    }
}
