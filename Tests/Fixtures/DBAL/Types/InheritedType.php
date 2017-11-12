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

/**
 * InheritedType.
 *
 * @author Arturs Vonda <github@artursvonda.lv>
 */
final class InheritedType extends AbstractParentType
{
    protected $name = 'InheritedType';

    /**
     * {@inheritdoc}
     */
    protected static $choices = [
        'foo' => 'bar',
    ];
}
