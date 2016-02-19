<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Util;

/**
 * This class was based on LegacyFormHelper in FOSUserBundle:
 * https://github.com/FriendsOfSymfony/FOSUserBundle/blob/c533a233b52c1d3843e816a35677561330ddbc74/Util/LegacyFormHelper.php
 *
 * @internal
 *
 * @author Gabor Egyed <gabor.egyed@gmail.com>
 * @author Jaik Dean <jaik@fluoresce.co>
 */
final class LegacyFormHelper
{
    private static $map = array(
        'Symfony\Component\Form\Extension\Core\Type\ChoiceType' => 'choice',
    );

    public static function getType($class)
    {
        if (!self::isLegacy()) {
            return $class;
        }

        if (!isset(self::$map[$class])) {
            throw new \InvalidArgumentException(sprintf('Form type with class "%s" can not be found. Please check for typos or add it to the map in LegacyFormHelper', $class));
        }

        return self::$map[$class];
    }

    public static function isLegacy()
    {
        return !method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
