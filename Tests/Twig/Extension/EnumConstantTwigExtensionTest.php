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
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;

/**
 * EnumConstantTwigExtensionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class EnumConstantTwigExtensionTest extends TestCase
{
    /** @var EnumConstantTwigExtension */
    private $enumConstantTwigExtension;

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

    public function testGetFilters(): void
    {
        self::assertEquals(
            [new TwigFilter('enum_constant', [$this->enumConstantTwigExtension, 'getEnumConstant'])],
            $this->enumConstantTwigExtension->getFilters()
        );
    }

    /**
     * @dataProvider dataProviderForGetEnumConstantTest
     *
     * @param string      $expectedValueOfConstant
     * @param string      $enumConstant
     * @param string|null $enumType
     */
    public function testGetEnumConstant(string $expectedValueOfConstant, string $enumConstant, ?string $enumType): void
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

    public function testEnumTypeIsNotRegisteredException(): void
    {
        $this->expectException(EnumTypeIsNotRegisteredException::class);
        $this->enumConstantTwigExtension->getEnumConstant('Pitcher', 'BaseballPositionType');
    }

    public function testConstantIsFoundInFewRegisteredEnumTypesException(): void
    {
        $this->expectException(ConstantIsFoundInFewRegisteredEnumTypesException::class);
        $this->enumConstantTwigExtension->getEnumConstant('CENTER');
    }

    public function testConstantIsNotFoundInAnyRegisteredEnumTypeException(): void
    {
        $this->expectException(ConstantIsNotFoundInAnyRegisteredEnumTypeException::class);
        $this->enumConstantTwigExtension->getEnumConstant('Pitcher');
    }

    public function testNoRegisteredEnumTypesException(): void
    {
        $this->expectException(NoRegisteredEnumTypesException::class);
        (new EnumConstantTwigExtension([]))->getEnumConstant(BasketballPositionType::POINT_GUARD, 'BasketballPositionType');
    }
}
