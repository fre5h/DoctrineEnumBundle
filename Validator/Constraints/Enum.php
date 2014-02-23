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
 * ENUM Constraint
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 *
 * @Annotation
 */
class Enum extends Choice
{
    /**
     * @var string Entity
     */
    public $entity;

    /**
     * Returns the name of the required options
     *
     * @return array
     */
    public function requiredOptions()
    {
        return array('entity');
    }

    /**
     * Returns the name of the default option
     *
     * @return string
     */
    public function getDefaultOption()
    {
        return 'choices';
    }
}
