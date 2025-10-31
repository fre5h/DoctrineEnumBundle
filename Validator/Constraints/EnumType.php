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
use Fresh\DoctrineEnumBundle\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraints\Choice;

/**
 * EnumType Constraint.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class EnumType extends Choice
{
    /**
     * @param string        $entity
     * @param string|null   $message
     * @param string[]|null $groups
     * @param mixed         $payload
     *
     * @throws InvalidArgumentException
     */
    public function __construct(public string $entity, ?string $message = null, ?array $groups = null, mixed $payload = null)
    {
        if (!\is_subclass_of($entity, AbstractEnumType::class)) {
            throw new InvalidArgumentException(\sprintf('%s is not instance of %s', $entity, AbstractEnumType::class));
        }

        parent::__construct(
            choices: $entity::getValues(),
            strict: true,
            message: $message,
            groups: $groups,
            payload: $payload
        );
    }
}
