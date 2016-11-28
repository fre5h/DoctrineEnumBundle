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
 * AbstractEnumExtension.
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
abstract class AbstractEnumExtension extends \Twig_Extension
{
    /**
     * @var AbstractEnumType[]
     */
    protected $registeredEnumTypes = [];

    /**
     * @param array $registeredTypes
     */
    public function __construct(array $registeredTypes)
    {
        foreach ($registeredTypes as $type => $details) {
            if (is_subclass_of($details['class'], AbstractEnumType::class)) {
                $this->registeredEnumTypes[$type] = $details['class'];
            }
        }
    }
}
