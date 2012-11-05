<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\Bundle\DoctrineEnumBundle\Tests;

use Fresh\Bundle\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Basketball position type
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class BasketballPositionType extends AbstractEnumType
{
    const POINT_GUARD    = 'PG';
    const SHOOTING_GUARD = 'SG';
    const SMALL_FORWARD  = 'SF';
    const POWER_FORWARD  = 'PF';
    const CENTER         = 'C';

    /**
     * @var string Name of this type
     */
    protected $name = 'BasketballPositionType';

    /**
     * @var array Readable choices
     * @static
     */
    protected static $choices = array(
        self::POINT_GUARD    => 'Point guard',
        self::SHOOTING_GUARD => 'Shooting guard',
        self::SMALL_FORWARD  => 'Small forward',
        self::POWER_FORWARD  => 'Power forward',
        self::CENTER         => 'Center',
    );

    /**
     * Get readable choices for the Enum field
     *
     * @static
     * @return array Readable choices for the Enum field
     */
    public static function getChoices()
    {
        return self::$choices;
    }

    /**
     * Get values for the Enum field
     *
     * @static
     * @return array Values for the Enum field
     */
    public static function getValues()
    {
        return array_keys(self::getChoices());
    }

    /**
     * Get value in readable format
     *
     * @param string $value Enum value
     *
     * @static
     * @return string|null Value in readable format
     */
    public static function getReadableValue($value)
    {
        return isset(self::getChoices()[$value]) ? self::getChoices()[$value] : null;
    }
}
