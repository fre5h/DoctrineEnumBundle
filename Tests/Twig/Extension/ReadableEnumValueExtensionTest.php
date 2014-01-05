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
 *
 * @coversDefaultClass \Fresh\Bundle\DoctrineEnumBundle\Twig\Extension\ReadableEnumValueExtension
 */
class ReadableEnumValueExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReadableEnumValueExtension
     */
    private $readableEnumValueExtension;

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
     * @param string $expectedReadableValue Expected readable value
     * @param string $enumValue             Enum value
     * @param string $enumType              Enum type
     *
     * @test
     * @covers ::getReadableEnumValue
     * @dataProvider dataProviderForGetReadableEnumValueTest
     */
    public function getReadableEnumValue($expectedReadableValue, $enumValue, $enumType)
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
            ['Center', BasketballPositionType::CENTER, 'BasketballPositionType'],
            ['Center', MapLocationType::CENTER, 'MapLocationType']
        ];
    }

    /**
     * Test that using readable ENUM value extension for ENUM type that is not registered
     * throws EnumTypeIsNotRegisteredException
     *
     * @test
     * @covers ::getReadableEnumValue
     * @expectedException \Fresh\Bundle\DoctrineEnumBundle\Exception\EnumTypeIsNotRegisteredException
     */
    public function enumTypeIsNotRegisteredException()
    {
        $this->readableEnumValueExtension->getReadableEnumValue('Pitcher', 'BaseballPositionType');
    }

    /**
     * Test that using ENUM value that is found in few registered ENUM types
     * throws ValueIsFoundInFewRegisteredEnumTypesException
     *
     * @test
     * @covers ::getReadableEnumValue
     * @expectedException \Fresh\Bundle\DoctrineEnumBundle\Exception\ValueIsFoundInFewRegisteredEnumTypesException
     */
    public function valueIsFoundInFewRegisteredEnumTypesException()
    {
        $this->readableEnumValueExtension->getReadableEnumValue(BasketballPositionType::CENTER);
    }

    /**
     * Test that using ENUM value that is not found in any registered ENUM type
     * throws ValueIsNotFoundInAnyRegisteredEnumTypeException
     *
     * @test
     * @covers ::getReadableEnumValue
     * @expectedException \Fresh\Bundle\DoctrineEnumBundle\Exception\ValueIsNotFoundInAnyRegisteredEnumTypeException
     */
    public function valueIsNotFoundInAnyRegisteredEnumTypeException()
    {
        $this->readableEnumValueExtension->getReadableEnumValue('Pitcher');
    }

    /**
     * Test that using readable ENUM value extension without any registered ENUM type
     * throws NoRegisteredEnumTypesException
     *
     * @test
     * @covers ::getReadableEnumValue
     * @expectedException \Fresh\Bundle\DoctrineEnumBundle\Exception\NoRegisteredEnumTypesException
     */
    public function noRegisteredEnumTypesException()
    {
        // Create ReadableEnumValueExtension without any registered ENUM type
        $extension = new ReadableEnumValueExtension([]);
        $extension->getReadableEnumValue(BasketballPositionType::POINT_GUARD, 'BasketballPositionType');
    }
}
