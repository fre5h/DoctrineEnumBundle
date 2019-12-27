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

/**
 * InheritedType.
 *
 * @author Arturs Vonda <github@artursvonda.lv>
 */
final class InheritedType extends AbstractParentType
{
    /** @var string */
    protected $name = 'InheritedType';

    /**
     * {@inheritdoc}
     */
    protected static $choices = [
        'foo' => 'bar',
    ];
}
