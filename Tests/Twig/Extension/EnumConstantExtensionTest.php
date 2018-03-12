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

use Fresh\DoctrineEnumBundle\Exception\Constant\ConstantIsFoundInFewRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Exception\Constant\ConstantIsNotFoundInAnyRegisteredEnumTypeException;
use Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsNotRegisteredException;
use Fresh\DoctrineEnumBundle\Exception\EnumType\NoRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\MapLocationType;
use Fresh\DoctrineEnumBundle\Twig\Extension\EnumConstantTwigExtension;
use PHPUnit\Framework\TestCase;

/**
 * EnumConstantExtensionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class EnumConstantExtensionTest extends TestCase
{
    /** @var EnumConstantTwigExtension */
    private $enumConstantExtension;

    public function setUp(): void
    {
        $this->enumConstantExtension = new EnumConstantTwigExtension([
            'BasketballPositionType' => ['class' => BasketballPositionType::class],
            'MapLocationType' => ['class' => MapLocationType::class],
        ]);
    }

    protected function tearDown(): void
    {
        unset($this->enumConstantExtension);
    }

    public function testGetFilters(): void
    {
        self::assertEquals(
            [new \Twig_SimpleFilter('enum_constant', [$this->enumConstantExtension, 'getEnumConstant'])],
            $this->enumConstantExtension->getFilters()
        );
    }

    /**
     * @dataProvider dataProviderForGetReadableEnumValueTest
     */
    public function testGetEnumConstant(string $expectedValueOfConstant, string $enumConstant, ?string $enumType): void
    {
        self::assertEquals(
            $expectedValueOfConstant,
            $this->enumConstantExtension->getEnumConstant($enumConstant, $enumType)
        );
    }

    public function dataProviderForGetReadableEnumValueTest(): array
    {
        return [
            ['PG', 'POINT_GUARD', 'BasketballPositionType'],
            ['PG', 'POINT_GUARD', null],
            ['C', 'CENTER', 'BasketballPositionType'],
            ['C', 'CENTER', 'MapLocationType'],
        ];
    }

    public function testEnumTypeIsNotRegisteredException(): void
    {
        $this->expectException(EnumTypeIsNotRegisteredException::class);
        $this->enumConstantExtension->getEnumConstant('Pitcher', 'BaseballPositionType');
    }

    public function testConstantIsFoundInFewRegisteredEnumTypesException(): void
    {
        $this->expectException(ConstantIsFoundInFewRegisteredEnumTypesException::class);
        $this->enumConstantExtension->getEnumConstant('CENTER');
    }

    public function testConstantIsNotFoundInAnyRegisteredEnumTypeException(): void
    {
        $this->expectException(ConstantIsNotFoundInAnyRegisteredEnumTypeException::class);
        $this->enumConstantExtension->getEnumConstant('Pitcher');
    }

    public function testNoRegisteredEnumTypesException(): void
    {
        $this->expectException(NoRegisteredEnumTypesException::class);
        (new EnumConstantTwigExtension([]))->getEnumConstant(BasketballPositionType::POINT_GUARD, 'BasketballPositionType');
    }
}
