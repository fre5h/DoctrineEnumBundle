<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Twig\Extension;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * BaseEnumExtension
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
abstract class AbstractEnumExtension extends \Twig_Extension
{
    /**
     * @var AbstractEnumType[] $registeredEnumTypes Array of registered ENUM types
     */
    protected $registeredEnumTypes = [];

    /**
     * Constructor
     *
     * @param array $registeredTypes Array of registered ENUM types
     */
    public function __construct(array $registeredTypes)
    {
        foreach ($registeredTypes as $type => $details) {
            if (is_subclass_of($details['class'], '\Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType')) {
                $this->registeredEnumTypes[$type] = $details['class'];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getName();
}
