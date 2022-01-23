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
 * MonthFullNameType.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 *
 * @extends AbstractEnumType<string, string>
 */
final class MonthFullNameType extends AbstractEnumType
{
    public final const JANUARY = 'january';

    public final const FEBRUARY = 'february';

    public final const MARCH = 'march';

    public final const APRIL = 'april';

    public final const MAY = 'may';

    public final const JUNE = 'june';

    public final const JULY = 'july';

    public final const AUGUST = 'august';

    public final const SEPTEMBER = 'september';

    public final const OCTOBER = 'october';

    public final const NOVEMBER = 'november';

    public final const DECEMBER = 'december';

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
