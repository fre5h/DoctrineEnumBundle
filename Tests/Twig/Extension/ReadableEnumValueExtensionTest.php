<?php
/*
 * This file is part of the FreshDoctrineEnumBundle.
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fresh\DoctrineEnumBundle\Tests\Twig\Extension;

use Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsNotRegisteredException;
use Fresh\DoctrineEnumBundle\Exception\EnumType\NoRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Exception\EnumValue\ValueIsFoundInFewRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Exception\EnumValue\ValueIsNotFoundInAnyRegisteredEnumTypeException;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\MapLocationType;
use Fresh\DoctrineEnumBundle\Twig\Extension\ReadableEnumValueTwigExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;

/**
 * ReadableEnumValueExtensionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class ReadableEnumValueExtensionTest extends TestCase
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
            [new TwigFilter('readable_enum', [$this->readableEnumValueExtension, 'getReadableEnumValue'])],
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

    public function testEnumTypeIsNotRegisteredException(): void
    {
        $this->expectException(EnumTypeIsNotRegisteredException::class);
        $this->readableEnumValueExtension->getReadableEnumValue('Pitcher', 'BaseballPositionType');
    }

    public function testValueIsFoundInFewRegisteredEnumTypesException(): void
    {
        $this->expectException(ValueIsFoundInFewRegisteredEnumTypesException::class);
        $this->readableEnumValueExtension->getReadableEnumValue(BasketballPositionType::CENTER);
    }

    public function testValueIsNotFoundInAnyRegisteredEnumTypeException(): void
    {
        $this->expectException(ValueIsNotFoundInAnyRegisteredEnumTypeException::class);
        $this->readableEnumValueExtension->getReadableEnumValue('Pitcher');
    }

    public function testNoRegisteredEnumTypesException(): void
    {
        $this->expectException(NoRegisteredEnumTypesException::class);
        (new ReadableEnumValueTwigExtension([]))->getReadableEnumValue(BasketballPositionType::POINT_GUARD, 'BasketballPositionType');
    }
}
