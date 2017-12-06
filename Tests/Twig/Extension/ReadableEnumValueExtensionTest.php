<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Tests\Twig\Extension;

use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\MapLocationType;
use Fresh\DoctrineEnumBundle\Twig\Extension\ReadableEnumValueExtension;

/**
 * ReadableEnumValueExtensionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class ReadableEnumValueExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReadableEnumValueExtension
     */
    private $readableEnumValueExtension;

    public function setUp()
    {
        $this->readableEnumValueExtension = new ReadableEnumValueExtension([
            'BasketballPositionType' => ['class' => BasketballPositionType::class],
            'MapLocationType' => ['class' => MapLocationType::class],
        ]);
    }

    public function testGetFilters()
    {
        $this->assertEquals(
            [
                new \Twig_SimpleFilter(
                    'readable_enum',
                    [$this->readableEnumValueExtension, 'getReadableEnumValue']
                ),
            ],
            $this->readableEnumValueExtension->getFilters()
        );
    }

    /**
     * @dataProvider dataProviderForGetReadableEnumValueTest
     */
    public function testGetReadableEnumValue($expectedReadableValue, $enumValue, $enumType)
    {
        $this->assertEquals(
            $expectedReadableValue,
            $this->readableEnumValueExtension->getReadableEnumValue($enumValue, $enumType)
        );
    }

    public function dataProviderForGetReadableEnumValueTest()
    {
        return [
            ['Point Guard', BasketballPositionType::POINT_GUARD, 'BasketballPositionType'],
            ['Point Guard', BasketballPositionType::POINT_GUARD, null],
            ['Center', BasketballPositionType::CENTER, 'BasketballPositionType'],
            ['Center', MapLocationType::CENTER, 'MapLocationType'],
            [null, null, 'MapLocationType'],
        ];
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\EnumTypeIsNotRegisteredException
     */
    public function testEnumTypeIsNotRegisteredException()
    {
        $this->readableEnumValueExtension->getReadableEnumValue('Pitcher', 'BaseballPositionType');
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\ValueIsFoundInFewRegisteredEnumTypesException
     */
    public function testValueIsFoundInFewRegisteredEnumTypesException()
    {
        $this->readableEnumValueExtension->getReadableEnumValue(BasketballPositionType::CENTER);
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\ValueIsNotFoundInAnyRegisteredEnumTypeException
     */
    public function testValueIsNotFoundInAnyRegisteredEnumTypeException()
    {
        $this->readableEnumValueExtension->getReadableEnumValue('Pitcher');
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\NoRegisteredEnumTypesException
     */
    public function testNoRegisteredEnumTypesException()
    {
        // Create ReadableEnumValueExtension without any registered ENUM type
        $extension = new ReadableEnumValueExtension([]);
        $extension->getReadableEnumValue(BasketballPositionType::POINT_GUARD, 'BasketballPositionType');
    }
}
