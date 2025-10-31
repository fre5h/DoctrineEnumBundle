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
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\HTTPStatusCodeType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\MapLocationType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\NumericType;
use Fresh\DoctrineEnumBundle\Twig\Extension\ReadableEnumValueTwigExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;

/**
 * ReadableEnumValueTwigExtensionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class ReadableEnumValueTwigExtensionTest extends TestCase
{
    private ReadableEnumValueTwigExtension $readableEnumValueTwigExtension;

    protected function setUp(): void
    {
        $this->readableEnumValueTwigExtension = new ReadableEnumValueTwigExtension([
            'BasketballPositionType' => ['class' => BasketballPositionType::class],
            'MapLocationType' => ['class' => MapLocationType::class],
            'NumericType' => ['class' => NumericType::class],
            'HTTPStatusCodeType' => ['class' => HTTPStatusCodeType::class],
        ]);
    }

    protected function tearDown(): void
    {
        unset($this->readableEnumValueTwigExtension);
    }

    #[Test]
    public function getFilters(): void
    {
        self::assertEquals(
            [new TwigFilter('readable_enum', [$this->readableEnumValueTwigExtension, 'getReadableEnumValue'])],
            $this->readableEnumValueTwigExtension->getFilters()
        );
    }

    #[Test]
    #[DataProvider('dataProviderForGetReadableEnumValueTest')]
    public function getReadableEnumValue(int|string|null $expectedReadableValue, int|string|null $enumValue, ?string $enumType): void
    {
        self::assertEquals(
            $expectedReadableValue,
            $this->readableEnumValueTwigExtension->getReadableEnumValue($enumValue, $enumType)
        );
    }

    public static function dataProviderForGetReadableEnumValueTest(): iterable
    {
        yield ['Point Guard', BasketballPositionType::POINT_GUARD, 'BasketballPositionType'];
        yield ['Point Guard', BasketballPositionType::POINT_GUARD, null];
        yield ['Center', BasketballPositionType::CENTER, 'BasketballPositionType'];
        yield ['Center', MapLocationType::CENTER, 'MapLocationType'];
        yield [null, null, 'MapLocationType'];
        yield [1, NumericType::ONE, 'NumericType'];
        yield [1, NumericType::ONE, null];
        yield ['Not Found', HTTPStatusCodeType::HTTP_NOT_FOUND, 'HTTPStatusCodeType'];
    }

    #[Test]
    public function enumTypeIsNotRegisteredException(): void
    {
        $this->expectException(EnumTypeIsNotRegisteredException::class);
        $this->readableEnumValueTwigExtension->getReadableEnumValue('Pitcher', 'BaseballPositionType');
    }

    #[Test]
    public function valueIsFoundInFewRegisteredEnumTypesException(): void
    {
        $this->expectException(ValueIsFoundInFewRegisteredEnumTypesException::class);
        $this->readableEnumValueTwigExtension->getReadableEnumValue(BasketballPositionType::CENTER);
    }

    #[Test]
    public function valueIsNotFoundInAnyRegisteredEnumTypeException(): void
    {
        $this->expectException(ValueIsNotFoundInAnyRegisteredEnumTypeException::class);
        $this->readableEnumValueTwigExtension->getReadableEnumValue('Pitcher');
    }

    #[Test]
    public function noRegisteredEnumTypesException(): void
    {
        $this->expectException(NoRegisteredEnumTypesException::class);
        (new ReadableEnumValueTwigExtension([]))->getReadableEnumValue(BasketballPositionType::POINT_GUARD, 'BasketballPositionType');
    }
}
