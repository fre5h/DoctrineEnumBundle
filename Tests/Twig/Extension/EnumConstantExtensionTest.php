<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fresh\DoctrineEnumBundle\Tests\Twig\Extension;

use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\MapLocationType;
use Fresh\DoctrineEnumBundle\Twig\Extension\EnumConstantExtension;
use PHPUnit\Framework\TestCase;

/**
 * EnumConstantExtensionTest.
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class EnumConstantExtensionTest extends TestCase
{
    /** @var EnumConstantExtension */
    private $enumConstantExtension;

    public function setUp()
    {
        $this->enumConstantExtension = new EnumConstantExtension([
            'BasketballPositionType' => ['class' => BasketballPositionType::class],
            'MapLocationType' => ['class' => MapLocationType::class],
        ]);
    }

    public function testGetFilters()
    {
        $this->assertEquals(
            [new \Twig_SimpleFilter('enum_constant', [$this->enumConstantExtension, 'getEnumConstant'])],
            $this->enumConstantExtension->getFilters()
        );
    }

    /**
     * @dataProvider dataProviderForGetReadableEnumValueTest
     */
    public function testGetEnumConstant(string $expectedValueOfConstant, string $enumConstant, string $enumType)
    {
        $this->assertEquals(
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
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\EnumTypeIsNotRegisteredException
     */
    public function testEnumTypeIsNotRegisteredException()
    {
        $this->enumConstantExtension->getEnumConstant('Pitcher', 'BaseballPositionType');
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\ConstantIsFoundInFewRegisteredEnumTypesException
     */
    public function testConstantIsFoundInFewRegisteredEnumTypesException()
    {
        $this->enumConstantExtension->getEnumConstant('CENTER');
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\ConstantIsNotFoundInAnyRegisteredEnumTypeException
     */
    public function testConstantIsNotFoundInAnyRegisteredEnumTypeException()
    {
        $this->enumConstantExtension->getEnumConstant('Pitcher');
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\NoRegisteredEnumTypesException
     */
    public function testNoRegisteredEnumTypesException()
    {
        // Create EnumConstantExtension without any registered ENUM type
        $extension = new EnumConstantExtension([]);
        $extension->getEnumConstant(BasketballPositionType::POINT_GUARD, 'BasketballPositionType');
    }
}
