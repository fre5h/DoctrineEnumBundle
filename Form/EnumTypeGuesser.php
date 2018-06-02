<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fresh\DoctrineEnumBundle\Form;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;
use Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsRegisteredButClassDoesNotExistException;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * EnumTypeGuesser.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 * @author Jaik Dean <jaik@fluoresce.co>
 */
class EnumTypeGuesser extends DoctrineOrmTypeGuesser
{
    /** @var AbstractEnumType[] */
    protected $registeredEnumTypes = [];

    /**
     * @param RegistryInterface $registry
     * @param array             $registeredTypes
     */
    public function __construct(RegistryInterface $registry, array $registeredTypes)
    {
        parent::__construct($registry);

        foreach ($registeredTypes as $type => $details) {
            $this->registeredEnumTypes[$type] = $details['class'];
        }
    }

    /**
     * @param string $class
     * @param string $property
     *
     * @return TypeGuess|null
     *
     * @throws EnumTypeIsRegisteredButClassDoesNotExistException
     */
    public function guessType($class, $property): ?TypeGuess
    {
        $classMetadata = $this->getMetadata($class);

        // If no metadata for this class - can't guess anything
        if (!$classMetadata) {
            return null;
        }

        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        [$metadata] = $classMetadata;
        $fieldType = $metadata->getTypeOfField($property);

        // This is not one of the registered ENUM types
        if (!isset($this->registeredEnumTypes[$fieldType])) {
            return null;
        }

        $registeredEnumTypeFQCN = $this->registeredEnumTypes[$fieldType];

        if (!\class_exists($registeredEnumTypeFQCN)) {
            throw new EnumTypeIsRegisteredButClassDoesNotExistException(\sprintf(
                'ENUM type "%s" is registered as "%s", but that class does not exist',
                $fieldType,
                $registeredEnumTypeFQCN
            ));
        }

        if (!\is_subclass_of($registeredEnumTypeFQCN, AbstractEnumType::class)) {
            return null;
        }

        // Get the choices from the fully qualified class name
        $parameters = [
            'choices' => $registeredEnumTypeFQCN::getChoices(),
            'required' => !$metadata->isNullable($property),
        ];

        return new TypeGuess(ChoiceType::class, $parameters, Guess::VERY_HIGH_CONFIDENCE);
    }
}
