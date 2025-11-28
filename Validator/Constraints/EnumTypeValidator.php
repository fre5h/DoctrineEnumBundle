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

use Fresh\DoctrineEnumBundle\Exception\RuntimeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ChoiceValidator;

/**
 * EnumTypeValidator validates that the value is one of the expected ENUM values.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class EnumTypeValidator extends ChoiceValidator
{
    /**
     * @param mixed               $value
     * @param Constraint|EnumType $constraint
     *
     * @throws RuntimeException
     */
    public function validate(mixed $value, Constraint|EnumType $constraint): void
    {
        if (!$constraint instanceof EnumType) {
            throw new RuntimeException(\sprintf('Object of class %s is not instance of %s', $constraint::class, EnumType::class));
        }

        $constraint->choices = $constraint->entity::getValues();

        parent::validate($value, $constraint);
    }
}
