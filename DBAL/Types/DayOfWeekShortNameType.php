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
 * @extends AbstractEnumType<string, string>
 */
final class DayOfWeekShortNameType extends AbstractEnumType
{
    public final const MONDAY = 'mon';

    public final const TUESDAY = 'tue';

    public final const WEDNESDAY = 'wed';

    public final const THURSDAY = 'thu';

    public final const FRIDAY = 'fri';

    public final const SATURDAY = 'sat';

    public final const SUNDAY = 'sun';

    /**
     * {@inheritdoc}
     */
    protected static array $choices = [
        self::MONDAY => 'Monday',
        self::TUESDAY => 'Tuesday',
        self::WEDNESDAY => 'Wednesday',
        self::THURSDAY => 'Thursday',
        self::FRIDAY => 'Friday',
        self::SATURDAY => 'Saturday',
        self::SUNDAY => 'Sunday',
    ];
}
