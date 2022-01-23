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
 * TaskStatusType.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 *
 * @extends AbstractEnumType<string, string>
 */
final class TaskStatusType extends AbstractEnumType
{
    public final const PENDING = 'pending';
    public final const DONE = 'done';
    public final const FAILED = 'failed';

    protected string $name = 'TaskStatusType';

    /**
     * {@inheritdoc}
     */
    protected static array $choices = [
        self::PENDING => 'Pending',
        self::DONE => 'Done',
        self::FAILED => 'Failed',
    ];

    /**
     * {@inheritdoc}
     */
    public static function getDefaultValue(): ?string
    {
        return self::PENDING;
    }
}
