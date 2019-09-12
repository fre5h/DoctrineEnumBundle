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
 * BasketballPositionType.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class BasketballPositionType extends AbstractEnumType
{
    public const POINT_GUARD = 'PG';
    public const SHOOTING_GUARD = 'SG';
    public const SMALL_FORWARD = 'SF';
    public const POWER_FORWARD = 'PF';
    public const CENTER = 'C';

    protected $name = 'BasketballPositionType';

    protected static $choices = [
        self::POINT_GUARD => 'Point Guard',
        self::SHOOTING_GUARD => 'Shooting Guard',
        self::SMALL_FORWARD => 'Small Forward',
        self::POWER_FORWARD => 'Power Forward',
        self::CENTER => 'Center',
    ];
}
