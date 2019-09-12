<?php
/*
 * This file is part of the FreshDoctrineEnumBundle.
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Validator\Constraints;

use Fresh\DoctrineEnumBundle\Exception\RuntimeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * EnumValidator validates that the value is one of the expected ENUM values.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class EnumValidator extends ChoiceValidator
{
    /**
     * @param mixed           $value
     * @param Constraint|Enum $constraint
     *
     * @throws RuntimeException
     * @throws ConstraintDefinitionException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Enum) {
            throw new RuntimeException(\sprintf('Object of class %s is not instance of %s', \get_class($constraint), Enum::class));
        }

        if (!$constraint->entity) {
            throw new ConstraintDefinitionException('Entity not specified.');
        }

        $constraint->choices = $constraint->entity::getValues();

        parent::validate($value, $constraint);
    }
}
