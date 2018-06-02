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

use Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsNotRegisteredException;
use Fresh\DoctrineEnumBundle\Exception\EnumType\NoRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Twig\Extension\EnumValuesAsArrayTwigExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

/**
 * EnumValuesAsArrayExtensionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class EnumValuesAsArrayExtensionTest extends TestCase
{
    /** @var EnumValuesAsArrayTwigExtension */
    private $enumValuesAsArrayTwigExtension;

    public function setUp(): void
    {
        $this->enumValuesAsArrayTwigExtension = new EnumValuesAsArrayTwigExtension([
            'BasketballPositionType' => ['class' => BasketballPositionType::class],
        ]);
    }

    protected function tearDown(): void
    {
        unset($this->enumValuesAsArrayTwigExtension);
    }

    public function testGetFunctions(): void
    {
        self::assertEquals(
            [
                new TwigFunction('enum_values_as_array', [$this->enumValuesAsArrayTwigExtension, 'getEnumValuesAsArray']),
                new TwigFunction('enum_readable_values_as_array', [$this->enumValuesAsArrayTwigExtension, 'getReadableEnumValuesAsArray']),
            ],
            $this->enumValuesAsArrayTwigExtension->getFunctions()
        );
    }


    public function testGetEnumValuesAsArray(): void
    {
        self::assertEquals(
            ['PG', 'SG', 'SF', 'PF', 'C'],
            $this->enumValuesAsArrayTwigExtension->getEnumValuesAsArray('BasketballPositionType')
        );
    }

    public function getReadableEnumValuesAsArray(): void
    {
        self::assertEquals(
            [
                'PG' => 'Point Guard',
                'SG' => 'Shooting Guard',
                'SF' => 'Small Forward',
                'PF' => 'Power Forward',
                'C' => 'Center',
            ],
            $this->enumValuesAsArrayTwigExtension->getReadableEnumValuesAsArray('BasketballPositionType')
        );
    }

    public function testEnumTypeIsNotRegisteredException(): void
    {
        $this->expectException(EnumTypeIsNotRegisteredException::class);
        $this->enumValuesAsArrayTwigExtension->getEnumValuesAsArray('MapLocationType');
    }

    public function testNoRegisteredEnumTypesException(): void
    {
        $this->expectException(NoRegisteredEnumTypesException::class);
        (new EnumValuesAsArrayTwigExtension([]))->getEnumValuesAsArray('MapLocationType');
    }
}
