<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Basketball position type
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
final class BasketballPositionType extends AbstractEnumType
{
    const POINT_GUARD    = 'PG';
    const SHOOTING_GUARD = 'SG';
    const SMALL_FORWARD  = 'SF';
    const POWER_FORWARD  = 'PF';
    const CENTER         = 'C';

    /**
     * {@inheritdoc}
     */
    protected $name = 'BasketballPositionType';

    /**
     * {@inheritdoc}
     */
    protected static $choices = [
        self::POINT_GUARD    => 'Point Guard',
        self::SHOOTING_GUARD => 'Shooting Guard',
        self::SMALL_FORWARD  => 'Small Forward',
        self::POWER_FORWARD  => 'Power Forward',
        self::CENTER         => 'Center',
    ];
}
