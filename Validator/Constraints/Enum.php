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

use Attribute;
use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;
use Symfony\Component\Validator\Constraints\Choice;

/**
 * ENUM Constraint.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Enum extends Choice
{
    /**
     * @param string $entity
     *
     * {@inheritdoc}
     */
    public function __construct(public string $entity, ...$options)
    {
        $this->strict = true;

        if (\is_subclass_of($entity, AbstractEnumType::class)) {
            /** @var array $choices */
            $choices = $entity::getValues();
            $this->choices = $choices;
        }

        parent::__construct(...$options);
    }
}
