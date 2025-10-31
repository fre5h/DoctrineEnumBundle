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
 * MonthShortNameType.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 *
 * @extends AbstractEnumType<string, string>
 */
final class MonthShortNameType extends AbstractEnumType
{
    public final const JANUARY = 'jan';

    public final const FEBRUARY = 'feb';

    public final const MARCH = 'mar';

    public final const APRIL = 'apr';

    public final const MAY = 'may';

    public final const JUNE = 'jun';

    public final const JULY = 'jul';

    public final const AUGUST = 'aug';

    public final const SEPTEMBER = 'sep';

    public final const OCTOBER = 'oct';

    public final const NOVEMBER = 'nov';

    public final const DECEMBER = 'dec';

    /**
     * {@inheritdoc}
     */
    protected static array $choices = [
        self::JANUARY => 'January',
        self::FEBRUARY => 'February',
        self::MARCH => 'March',
        self::APRIL => 'April',
        self::MAY => 'May',
        self::JUNE => 'June',
        self::JULY => 'July',
        self::AUGUST => 'August',
        self::SEPTEMBER => 'September',
        self::OCTOBER => 'October',
        self::NOVEMBER => 'November',
        self::DECEMBER => 'December',
    ];
}
