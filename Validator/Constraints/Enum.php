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
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * ENUM Constraint.
 *
 * @deprecated Support for Enum annotation will be dropped in 9.0. Please switch to using \Fresh\DoctrineEnumBundle\Validator\Constraints\EnumType attribute instead.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 *
 * @Annotation
 */
class Enum extends Choice
{
    /** @var string|AbstractEnumType<int|string, int|string> */
    public $entity;

    /**
     * @param array<string, array<string, string>> $options
     */
    public function __construct($options = null)
    {
        $this->strict = true;

        if (!\is_array($options) || !\array_key_exists('entity', $options)) {
            throw new MissingOptionsException(sprintf('Option "entity" is required for constraint "%s".', __CLASS__), ['entity']);
        }

        if (null !== $options['entity']) {
            /** @var AbstractEnumType<int|string, int|string> $entity */
            $entity = $options['entity'];

            if (\is_subclass_of($entity, AbstractEnumType::class)) { // @phpstan-ignore-line
                $this->choices = $entity::getValues();
            }
        }

        parent::__construct($options); // @phpstan-ignore-line
    }

    /**
     * @return string[]
     */
    public function getRequiredOptions(): array
    {
        return ['entity'];
    }
}
