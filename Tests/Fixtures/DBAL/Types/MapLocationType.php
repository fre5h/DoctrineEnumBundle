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
 *
 * @extends AbstractEnumType<string, string>
 */
final class MapLocationType extends AbstractEnumType
{
    public final const NORTH = 'N';
    public final const EAST = 'E';
    public final const SOUTH = 'S';
    public final const WEST = 'W';
    public final const CENTER = 'C';
    public final const NORTH_WEST = 'NW';
    public final const NORTH_EAST = 'NE';
    public final const SOUTH_WEST = 'SW';
    public final const SOUTH_EAST = 'SE';

    protected string $name = 'MapLocationType';

    /**
     * {@inheritdoc}
     */
    protected static array $choices = [
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
