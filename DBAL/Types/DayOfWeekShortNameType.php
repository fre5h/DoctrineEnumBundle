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

namespace Fresh\DoctrineEnumBundle\DBAL\Types;

/**
 * DayOfWeekShortNameType.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 *
 * @extends AbstractEnumType<string>
 */
final class DayOfWeekShortNameType extends AbstractEnumType
{
    public const MONDAY = 'mon';

    public const TUESDAY = 'tue';

    public const WEDNESDAY = 'wed';

    public const THURSDAY = 'thu';

    public const FRIDAY = 'fri';

    public const SATURDAY = 'sat';

    public const SUNDAY = 'sun';

    /**
     * {@inheritdoc}
     */
    protected static $choices = [
        self::MONDAY => 'Monday',
        self::TUESDAY => 'Tuesday',
        self::WEDNESDAY => 'Wednesday',
        self::THURSDAY => 'Thursday',
        self::FRIDAY => 'Friday',
        self::SATURDAY => 'Saturday',
        self::SUNDAY => 'Sunday',
    ];
}
