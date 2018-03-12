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

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsNotRegisteredException
     */
    public function testEnumTypeIsNotRegisteredException(): void
    {
        $this->enumConstantExtension->getEnumConstant('Pitcher', 'BaseballPositionType');
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\Constant\ConstantIsFoundInFewRegisteredEnumTypesException
     */
    public function testConstantIsFoundInFewRegisteredEnumTypesException(): void
    {
        $this->enumConstantExtension->getEnumConstant('CENTER');
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\Constant\ConstantIsNotFoundInAnyRegisteredEnumTypeException
     */
    public function testConstantIsNotFoundInAnyRegisteredEnumTypeException(): void
    {
        $this->enumConstantExtension->getEnumConstant('Pitcher');
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\EnumType\NoRegisteredEnumTypesException
     */
    public function testNoRegisteredEnumTypesException(): void
    {
        // Create EnumConstantExtension without any registered ENUM type
        $extension = new EnumConstantTwigExtension([]);
        $extension->getEnumConstant(BasketballPositionType::POINT_GUARD, 'BasketballPositionType');
    }
}
