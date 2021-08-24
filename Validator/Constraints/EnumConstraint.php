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

namespace Fresh\DoctrineEnumBundle\Validator\Constraints;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;
use Symfony\Component\Validator\Constraints\Choice;

/**
 * ENUM Constraint.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 *
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class EnumConstraint extends Choice
{
    /** @var string|AbstractEnumType<int|string> */
    public $entity;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $entity = null, ...$options)
    {
        $this->strict = true;

        if (!is_null($entity)) {
            /** @var AbstractEnumType<int|string> $entity */
            if (\is_subclass_of($entity, AbstractEnumType::class)) {
                $this->choices = $entity::getValues();
            }
        }
        parent::__construct(...$options);
    }

    /**
     * @return string[]
     */
    public function getRequiredOptions(): array
    {
        return ['entity'];
    }
}
