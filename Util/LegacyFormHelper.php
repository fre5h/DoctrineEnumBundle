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

use Symfony\Component\HttpKernel\Kernel;

/**
 * LegacyFormHelper.
 *
 * This class based on LegacyFormHelper in FOSUserBundle:
 * @see https://github.com/FriendsOfSymfony/FOSUserBundle/blob/c533a233b52c1d3843e816a35677561330ddbc74/Util/LegacyFormHelper.php
 *
 * @internal
 *
 * @author Gabor Egyed <gabor.egyed@gmail.com>
 * @author Jaik Dean <jaik@fluoresce.co>
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
final class LegacyFormHelper
{
    const MINIMUM_MAJOR_VERSION = 3;

    /**
     * @var array $map Mapping form type classes to legacy form types
     * @static
     */
    private static $map = [
        'Symfony\Component\Form\Extension\Core\Type\ChoiceType' => 'choice',
    ];

    /**
     * Get a form field type compatible with the current version of Symfony.
     *
     * @param string $class        Class
     * @param int    $majorVersion Major version
     *
     * @return string Class or type name
     */
    public static function getType($class, $majorVersion = Kernel::MAJOR_VERSION)
    {
        if (!self::isLegacy($majorVersion)) {
            return $class;
        }

        if (!isset(self::$map[$class])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Form type with class "%s" can not be found. Please check for typos or add it to the map in LegacyFormHelper',
                    $class
                )
            );
        }

        return self::$map[$class];
    }

    /**
     * Check whether to use legacy form behaviour from Symfony <3.0.
     *
     * @param int $majorVersion Major version
     *
     * @static
     *
     * @return bool
     */
    public static function isLegacy($majorVersion = Kernel::MAJOR_VERSION)
    {
        return $majorVersion < self::MINIMUM_MAJOR_VERSION;
    }
}
