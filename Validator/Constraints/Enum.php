<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Validator\Constraints;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;
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
     * @var string $entity Entity
     */
    public $entity;

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        if (isset($options['entity'])) {
            $entity = $options['entity'];

            if ($entity instanceof AbstractEnumType) {
                $this->choices = $entity::getValues();
            }
        }

        parent::__construct($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['entity'];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'choices';
    }
}
