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

use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * EnumValidator validates that the value is one of the expected values
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class EnumValidator extends ChoiceValidator
{
    /**
     * Checks if the passed value is valid
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @throws ConstraintDefinitionException
     *
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint->entity) {
            throw new ConstraintDefinitionException('Entity not specified.');
        }

        $entity = $constraint->entity;
        $constraint->choices = $entity::getValues();

        parent::validate($value, $constraint);
    }
}
