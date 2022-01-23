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
 * DayOfWeekFullNameType.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 *
 * @extends AbstractEnumType<string, string>
 */
final class DayOfWeekFullNameType extends AbstractEnumType
{
    public final const MONDAY = 'monday';

    public final const TUESDAY = 'tuesday';

    public final const WEDNESDAY = 'wednesday';

    public final const THURSDAY = 'thursday';

    public final const FRIDAY = 'friday';

    public final const SATURDAY = 'saturday';

    public final const SUNDAY = 'sunday';

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
