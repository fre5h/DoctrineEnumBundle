<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\Bundle\DoctrineEnumBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Choice;

/**
 * Enum Constraint
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 *
 * @Annotation
 */
class Enum extends Choice
{
    public $entity;

    /**
     * {@inheritDoc}
     */
    public function requiredOptions()
    {
        return array('entity');
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultOption()
    {
        return 'choices';
    }
}
