<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Tests\Util;

use Fresh\DoctrineEnumBundle\Util\LegacyFormHelper;

/**
 * LegacyFormHelperTest.
 *
 * @author Jaik Dean <jaik@fluoresce.co>
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class LegacyFormHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderForIsLegacyTest
     */
    public function testIsLegacy($majorVersion, $expectedLegacyStatus)
    {
        $this->assertEquals($expectedLegacyStatus, LegacyFormHelper::isLegacy($majorVersion));
    }

    public function dataProviderForIsLegacyTest()
    {
        return [
            [1, true],
            [2, true],
            [3, false],
        ];
    }

    /**
     * @dataProvider dataProviderForGetTypeTest
     */
    public function testGetType($majorVersion, $expectedFormType)
    {
        $this->assertEquals(
            $expectedFormType,
            LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\ChoiceType', $majorVersion)
        );
    }

    public function dataProviderForGetTypeTest()
    {
        return [
            [2, 'choice'],
            [3, 'Symfony\Component\Form\Extension\Core\Type\ChoiceType'],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Form type with class "Symfony\Component\Form\Extension\Core\Type\TextType" can not be found. Please check for typos or add it to the map in LegacyFormHelper
     */
    public function testExceptionForGetUnsupportedType()
    {
        LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\TextType', 2);
    }
}
