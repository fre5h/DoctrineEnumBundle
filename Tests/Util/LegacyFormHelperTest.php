<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Util
{
    function method_exists($class, $method)
    {
        global $LegacyFormHelperTest_legacySymfonyVersion;
        return !$LegacyFormHelperTest_legacySymfonyVersion;
    }
}

namespace Fresh\DoctrineEnumBundle\Tests\Util
{
    use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
    use Fresh\DoctrineEnumBundle\Util\LegacyFormHelper;

    /**
     * LegacyFormHelperTest
     *
     * @author Jaik Dean <jaik@fluoresce.co>
     *
     * @coversClass \Fresh\DoctrineEnumBundle\Util\LegacyFormHelper
     */
    class LegacyFormHelperTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Test that the helper identifies whether we’re running a legacy
         * version of Symfony.
         */
        public function testIsLegacy()
        {
            global $LegacyFormHelperTest_legacySymfonyVersion;

            $LegacyFormHelperTest_legacySymfonyVersion = true;
            $this->assertEquals(true, LegacyFormHelper::isLegacy());

            $LegacyFormHelperTest_legacySymfonyVersion = false;
            $this->assertEquals(false, LegacyFormHelper::isLegacy());
        }

        /**
         * Test that the correct form field type is returned for current and legacy
         * versions of Symfony.
         */
        public function testGetType()
        {
            global $LegacyFormHelperTest_legacySymfonyVersion;
            $formType = 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';

            $LegacyFormHelperTest_legacySymfonyVersion = true;
            $this->assertEquals('choice', LegacyFormHelper::getType($formType));

            $LegacyFormHelperTest_legacySymfonyVersion = false;
            $this->assertEquals($formType, LegacyFormHelper::getType($formType));
        }
    }
}
