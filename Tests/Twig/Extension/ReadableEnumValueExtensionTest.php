<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fresh\DoctrineEnumBundle\Tests\Twig\Extension;

use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\MapLocationType;
use Fresh\DoctrineEnumBundle\Twig\Extension\ReadableEnumValueTwigExtension;
use PHPUnit\Framework\TestCase;

/**
 * ReadableEnumValueExtensionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class ReadableEnumValueExtensionTest extends TestCase
{
    /** @var ReadableEnumValueTwigExtension */
    private $readableEnumValueExtension;

    public function setUp(): void
    {
        $this->readableEnumValueExtension = new ReadableEnumValueTwigExtension([
            'BasketballPositionType' => ['class' => BasketballPositionType::class],
            'MapLocationType' => ['class' => MapLocationType::class],
        ]);
    }

    protected function tearDown(): void
    {
        unset($this->readableEnumValueExtension);
    }

    public function testGetFilters(): void
    {
        self::assertEquals(
            [new \Twig_SimpleFilter('readable_enum', [$this->readableEnumValueExtension, 'getReadableEnumValue'])],
            $this->readableEnumValueExtension->getFilters()
        );
    }

    /**
     * @dataProvider dataProviderForGetReadableEnumValueTest
     */
    public function testGetReadableEnumValue(?string $expectedReadableValue, ?string $enumValue, ?string $enumType): void
    {
        self::assertEquals(
            $expectedReadableValue,
            $this->readableEnumValueExtension->getReadableEnumValue($enumValue, $enumType)
        );
    }

    public function dataProviderForGetReadableEnumValueTest(): array
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
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsNotRegisteredException
     */
    public function testEnumTypeIsNotRegisteredException(): void
    {
        $this->readableEnumValueExtension->getReadableEnumValue('Pitcher', 'BaseballPositionType');
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\EnumValue\ValueIsFoundInFewRegisteredEnumTypesException
     */
    public function testValueIsFoundInFewRegisteredEnumTypesException(): void
    {
        $this->readableEnumValueExtension->getReadableEnumValue(BasketballPositionType::CENTER);
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\EnumValue\ValueIsNotFoundInAnyRegisteredEnumTypeException
     */
    public function testValueIsNotFoundInAnyRegisteredEnumTypeException(): void
    {
        $this->readableEnumValueExtension->getReadableEnumValue('Pitcher');
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\EnumType\NoRegisteredEnumTypesException
     */
    public function testNoRegisteredEnumTypesException(): void
    {
        // Create ReadableEnumValueExtension without any registered ENUM type
        $extension = new ReadableEnumValueTwigExtension([]);
        $extension->getReadableEnumValue(BasketballPositionType::POINT_GUARD, 'BasketballPositionType');
    }
}
