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

namespace Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * MapLocationType.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class MapLocationType extends AbstractEnumType
{
    public const NORTH = 'N';
    public const EAST = 'E';
    public const SOUTH = 'S';
    public const WEST = 'W';
    public const CENTER = 'C';
    public const NORTH_WEST = 'NW';
    public const NORTH_EAST = 'NE';
    public const SOUTH_WEST = 'SW';
    public const SOUTH_EAST = 'SE';

    /** @var string */
    protected $name = 'MapLocationType';

    /**
     * {@inheritdoc}
     */
    protected static $choices = [
        self::NORTH => 'North',
        self::EAST => 'East',
        self::SOUTH => 'South',
        self::WEST => 'West',
        self::CENTER => 'Center',
        self::NORTH_WEST => 'Northwest',
        self::NORTH_EAST => 'Northeast',
        self::SOUTH_WEST => 'Southwest',
        self::SOUTH_EAST => 'Southeast',
    ];

    /**
     * {@inheritdoc}
     */
    public static function getDefaultValue(): ?string
    {
        return self::CENTER;
    }
}
