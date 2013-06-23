<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\Bundle\DoctrineEnumBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ReadableEnumValueExtension returns the readable variant of ENUM value
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class ReadableEnumValueExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    protected $container;

    /**
     * @var array
     */
    protected $registeredEnumTypes = [];

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        // Find all registered ENUM types
        if ($this->container->hasParameter('doctrine.dbal.connection_factory.types')) {
            $types = $this->container->getParameter('doctrine.dbal.connection_factory.types');

            foreach ($types as $type => $details) {
                $this->registeredEnumTypes[$type] = $details['class'];
            }
        }
    }

    /**
     * Returns a list of filters
     *
     * @return array
     */
    public function getFilters()
    {
        return ['readable_enum_value' => new \Twig_Filter_Method($this, 'getReadableEnumValue')];
    }

    /**
     * Get readable variant of ENUM value
     *
     * @param string $enumValue ENUM value
     * @param string $enumType  ENUM type
     *
     * @return string
     *
     * @throws \UnexpectedValueException
     * @throws \LogicException
     */
    public function getReadableEnumValue($enumValue, $enumType)
    {
        if (!empty($this->registeredEnumTypes)) {
            if (!isset($this->registeredEnumTypes[$enumType])) {
                throw new \UnexpectedValueException(sprintf('ENUM type %s is not registered', $enumType));
            }

            /** @var $enumTypeClass \Fresh\Bundle\DoctrineEnumBundle\DBAL\Types\AbstractEnumType */
            $enumTypeClass = $this->registeredEnumTypes[$enumType];

            return $enumTypeClass::getReadableValue($enumValue);
        } else {
            throw new \LogicException('There are no registered ENUM types');
        }
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getName()
    {
        return 'Readable ENUM Value';
    }
}
