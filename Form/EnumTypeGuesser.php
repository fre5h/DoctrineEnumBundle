<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Form;

use Doctrine\Common\Persistence\ManagerRegistry;
use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;
use Fresh\DoctrineEnumBundle\Exception\EnumTypeIsRegisteredButClassDoesNotExistException;
use Fresh\DoctrineEnumBundle\Util\LegacyFormHelper;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * EnumTypeGuesser.
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 * @author Jaik Dean <jaik@fluoresce.co>
 */
class EnumTypeGuesser extends DoctrineOrmTypeGuesser
{
    /**
     * @var AbstractEnumType[] $registeredEnumTypes Array of registered ENUM types
     */
    protected $registeredEnumTypes = [];

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry        Registry
     * @param array           $registeredTypes Array of registered ENUM types
     */
    public function __construct(ManagerRegistry $registry, array $registeredTypes)
    {
        parent::__construct($registry);

        foreach ($registeredTypes as $type => $details) {
            $this->registeredEnumTypes[$type] = $details['class'];
        }
    }

    /**
     * Returns a field guess for a property name of a class.
     *
     * @param string $class    The fully qualified class name
     * @param string $property The name of the property to guess for
     *
     * @return TypeGuess A guess for the field's type and options
     *
     * @throws EnumTypeIsRegisteredButClassDoesNotExistException
     */
    public function guessType($class, $property)
    {
        $classMetadata = $this->getMetadata($class);

        // If no metadata for this class - can't guess anything
        if (!$classMetadata) {
            return null;
        }

        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        list($metadata) = $classMetadata;
        $fieldType = $metadata->getTypeOfField($property);

        // This is not one of the registered ENUM types
        if (!isset($this->registeredEnumTypes[$fieldType])) {
            return null;
        }

        $registeredEnumTypeFQCN = $this->registeredEnumTypes[$fieldType];

        if (!class_exists($registeredEnumTypeFQCN)) {
            throw new EnumTypeIsRegisteredButClassDoesNotExistException(sprintf(
                'ENUM type "%s" is registered as "%s", but that class does not exist',
                $fieldType,
                $registeredEnumTypeFQCN
            ));
        }

        $abstractEnumTypeFQCN = 'Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType';

        if (get_parent_class($registeredEnumTypeFQCN) !== $abstractEnumTypeFQCN) {
            return null;
        }

        // Get the choices from the fully qualified class name
        $parameters = [
            'choices'  => $registeredEnumTypeFQCN::getChoices(),
            'required' => !$metadata->isNullable($property),
        ];

        // Compatibility with Symfony <3.0
        $fieldType = LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\ChoiceType');

        return new TypeGuess($fieldType, $parameters, Guess::VERY_HIGH_CONFIDENCE);
    }
}
