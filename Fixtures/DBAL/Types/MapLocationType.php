<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\Bundle\DoctrineEnumBundle\Fixtures\DBAL\Types;

use Fresh\Bundle\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Map location type
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class MapLocationType extends AbstractEnumType
{
    const NORTH      = 'N';
    const EAST       = 'E';
    const SOUTH      = 'S';
    const WEST       = 'W';
    const CENTER     = 'C';
    const NORTH_WEST = 'NW';
    const NORTH_EAST = 'NE';
    const SOUTH_WEST = 'SW';
    const SOUTH_EAST = 'SE';

    /**
     * @var string Name of this type
     */
    protected $name = 'MapLocationType';

    /**
     * @var array Readable choices
     * @static
     */
    protected static $choices = array(
        self::NORTH      => 'North',
        self::EAST       => 'East',
        self::SOUTH      => 'South',
        self::WEST       => 'West',
        self::CENTER     => 'Center',
        self::NORTH_WEST => 'Northwest',
        self::NORTH_EAST => 'Northeast',
        self::SOUTH_WEST => 'Southwest',
        self::SOUTH_EAST => 'Southeast'
    );
}
