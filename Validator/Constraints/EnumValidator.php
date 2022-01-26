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
 * EnumValidator validates that the value is one of the expected ENUM values.
 *
 * @deprecated Support for Enum annotation will be dropped in 9.0. Please switch to using \Fresh\DoctrineEnumBundle\Validator\Constraints\EnumType attribute instead.
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
     */
    public function validate($value, Constraint|Enum $constraint): void
    {
        if (!$constraint instanceof Enum) {
            throw new RuntimeException(\sprintf('Object of class %s is not instance of %s', \get_class($constraint), Enum::class));
        }

        $constraint->choices = $constraint->entity::getValues();

        parent::validate($value, $constraint);
    }
}
