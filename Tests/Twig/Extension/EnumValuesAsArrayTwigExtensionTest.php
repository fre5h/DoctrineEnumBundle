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
use Fresh\DoctrineEnumBundle\Exception\LogicException;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Twig\Extension\EnumValuesAsArrayTwigExtension;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

/**
 * EnumValuesAsArrayTwigExtensionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class EnumValuesAsArrayTwigExtensionTest extends TestCase
{
    private EnumValuesAsArrayTwigExtension $enumValuesAsArrayTwigExtension;

    protected function setUp(): void
    {
        $this->enumValuesAsArrayTwigExtension = new EnumValuesAsArrayTwigExtension([
            'BasketballPositionType' => ['class' => BasketballPositionType::class],
        ]);
    }

    protected function tearDown(): void
    {
        unset($this->enumValuesAsArrayTwigExtension);
    }

    #[Test]
    public function getFunctions(): void
    {
        $this->assertEquals(
            [
                new TwigFunction('enum_values', [$this->enumValuesAsArrayTwigExtension, 'getEnumValuesAsArray']),
                new TwigFunction('enum_readable_values', [$this->enumValuesAsArrayTwigExtension, 'getReadableEnumValuesAsArray']),
            ],
            $this->enumValuesAsArrayTwigExtension->getFunctions()
        );
    }

    #[Test]
    public function getEnumValuesAsArray(): void
    {
        $this->assertEquals(
            ['PG', 'SG', 'SF', 'PF', 'C'],
            $this->enumValuesAsArrayTwigExtension->getEnumValuesAsArray('BasketballPositionType')
        );
    }

    #[Test]
    public function getReadableEnumValuesAsArray(): void
    {
        $this->assertEquals(
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

    #[Test]
    public function enumTypeIsNotRegisteredException(): void
    {
        $this->expectException(EnumTypeIsNotRegisteredException::class);
        $this->enumValuesAsArrayTwigExtension->getEnumValuesAsArray('MapLocationType');
    }

    #[Test]
    public function noRegisteredEnumTypesException(): void
    {
        $this->expectException(NoRegisteredEnumTypesException::class);
        (new EnumValuesAsArrayTwigExtension([]))->getEnumValuesAsArray('MapLocationType');
    }

    #[Test]
    public function invalidCallable(): void
    {
        $extension = new EnumValuesAsArrayTwigExtension([]);

        $property = new \ReflectionProperty(EnumValuesAsArrayTwigExtension::class, 'registeredEnumTypes');
        $property->setAccessible(true);
        $property->setValue($extension, ['invalid_callable' => 'dummy']);
        $property->setAccessible(false);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('dummy::getReadableValues is not a valid exception');

        $extension->getReadableEnumValuesAsArray('invalid_callable');
    }
}
