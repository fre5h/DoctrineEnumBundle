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
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Enum extends Choice
{
    /**
     * {@inheritdoc}
     * @property string|AbstractEnumType<int|string> $entity
     */
    public function __construct(public string $entity, ...$options)
    {
        $this->strict = true;

        if (\is_subclass_of($entity, AbstractEnumType::class)) {
            $this->choices = $entity::getValues();
        }
        parent::__construct(...$options);
    }
}
