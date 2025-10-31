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

use Fresh\DoctrineEnumBundle\Exception\Constant\ConstantIsFoundInFewRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Exception\Constant\ConstantIsNotFoundInAnyRegisteredEnumTypeException;
use Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsNotRegisteredException;
use Fresh\DoctrineEnumBundle\Exception\EnumType\NoRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\HTTPStatusCodeType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\MapLocationType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\NumericType;
use Fresh\DoctrineEnumBundle\Twig\Extension\EnumConstantTwigExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;

/**
 * EnumConstantTwigExtensionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class EnumConstantTwigExtensionTest extends TestCase
{
    private EnumConstantTwigExtension $enumConstantTwigExtension;

    protected function setUp(): void
    {
        $this->enumConstantTwigExtension = new EnumConstantTwigExtension([
            'BasketballPositionType' => ['class' => BasketballPositionType::class],
            'MapLocationType' => ['class' => MapLocationType::class],
            'NumericType' => ['class' => NumericType::class],
            'HTTPStatusCodeType' => ['class' => HTTPStatusCodeType::class],
        ]);
    }

    protected function tearDown(): void
    {
        unset($this->enumConstantTwigExtension);
    }

    #[Test]
    public function getFilters(): void
    {
        self::assertEquals(
            [new TwigFilter('enum_constant', [$this->enumConstantTwigExtension, 'getEnumConstant'])],
            $this->enumConstantTwigExtension->getFilters()
        );
    }

    #[Test]
    #[DataProvider('dataProviderForGetEnumConstantTest')]
    public function GetEnumConstant(string $expectedValueOfConstant, string $enumConstant, ?string $enumType): void
    {
        self::assertEquals(
            $expectedValueOfConstant,
            $this->enumConstantTwigExtension->getEnumConstant($enumConstant, $enumType)
        );
    }

    public static function dataProviderForGetEnumConstantTest(): iterable
    {
        yield ['PG', 'POINT_GUARD', 'BasketballPositionType'];
        yield ['PG', 'POINT_GUARD', null];
        yield ['C', 'CENTER', 'BasketballPositionType'];
        yield ['C', 'CENTER', 'MapLocationType'];
        yield ['3', 'THREE', 'NumericType'];
        yield ['200', 'HTTP_OK', 'HTTPStatusCodeType'];
    }

    #[Test]
    public function enumTypeIsNotRegisteredException(): void
    {
        $this->expectException(EnumTypeIsNotRegisteredException::class);
        $this->enumConstantTwigExtension->getEnumConstant('Pitcher', 'BaseballPositionType');
    }

    #[Test]
    public function constantIsFoundInFewRegisteredEnumTypesException(): void
    {
        $this->expectException(ConstantIsFoundInFewRegisteredEnumTypesException::class);
        $this->enumConstantTwigExtension->getEnumConstant('CENTER');
    }

    #[Test]
    public function constantIsNotFoundInAnyRegisteredEnumTypeException(): void
    {
        $this->expectException(ConstantIsNotFoundInAnyRegisteredEnumTypeException::class);
        $this->enumConstantTwigExtension->getEnumConstant('Pitcher');
    }

    #[Test]
    public function noRegisteredEnumTypesException(): void
    {
        $this->expectException(NoRegisteredEnumTypesException::class);
        (new EnumConstantTwigExtension([]))->getEnumConstant(BasketballPositionType::POINT_GUARD, 'BasketballPositionType');
    }
}
