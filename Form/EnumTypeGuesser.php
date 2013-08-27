<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\Bundle\DoctrineEnumBundle\Form;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * EnumTypeGuesser
 *
 * Provides support of MySQL ENUM type for Doctrine in Symfony applications
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class EnumTypeGuesser extends DoctrineOrmTypeGuesser
{
    /**
     * @var array Array holding 'ShortType' => 'Fully\Qualified\Class\Name' mappings for the enum classes
     */
    protected $registeredTypes = [];

    /**
     * Constructor
     *
     * @param ManagerRegistry $registry Registry
     * @param array           $types    Array of registered ENUM types
     */
    public function __construct(ManagerRegistry $registry, array $types)
    {
        parent::__construct($registry);

        foreach ($types as $type => $details) {
            $this->registeredTypes[$type] = $details['class'];
        }
    }

    /**
     * Returns a field guess for a property name of a class
     *
     * @param string $class    The fully qualified class name
     * @param string $property The name of the property to guess for
     *
     * @return TypeGuess A guess for the field's type and options
     *
     * @throws \LogicException
     */
    public function guessType($class, $property)
    {
        $classMetadata = $this->getMetadata($class);

        // If no metadata for this class - can't guess anything
        if (!$classMetadata) {
            return null;
        }

        list($metadata) = $classMetadata;
        $fieldType = $metadata->getTypeOfField($property);

        // This is not one of the registered ENUM types
        if (!isset($this->registeredTypes[$fieldType])) {
            return null;
        }

        $className = $this->registeredTypes[$fieldType];

        if (!class_exists($className)) {
            throw new \LogicException("ENUM type {$fieldType} is registered as {$className}, but that class does not exist");
        }

        // Get the choices from the fully qualified classname
        $parameters = [
            'choices'  => $className::getChoices(),
            'required' => !$metadata->isNullable($property),
        ];

        return new TypeGuess('choice', $parameters, Guess::VERY_HIGH_CONFIDENCE);
    }
}
