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
 *
 * @extends AbstractEnumType<string, string>
 */
final class BasketballPositionType extends AbstractEnumType
{
    public final const POINT_GUARD = 'PG';
    public final const SHOOTING_GUARD = 'SG';
    public final const SMALL_FORWARD = 'SF';
    public final const POWER_FORWARD = 'PF';
    public final const CENTER = 'C';

    protected string $name = 'BasketballPositionType';

    /**
     * {@inheritdoc}
     */
    protected static array $choices = [
        self::POINT_GUARD => 'Point Guard',
        self::SHOOTING_GUARD => 'Shooting Guard',
        self::SMALL_FORWARD => 'Small Forward',
        self::POWER_FORWARD => 'Power Forward',
        self::CENTER => 'Center',
    ];
}
