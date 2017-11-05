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
 * ENUM Constraint.
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 *
 * @Annotation
 */
class Enum extends Choice
{
    /** @var string */
    public $entity;

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        $this->strict = true;

        if (isset($options['entity'])) {
            /** @var AbstractEnumType $entity */
            $entity = $options['entity'];

            if (is_subclass_of($entity, AbstractEnumType::class)) {
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
